<?php
/**
 * Enclosure meta handler and iTunes RSS feed extensions for Podcast Blocks.
 *
 * – On post save: writes the standard WordPress `enclosure` meta field
 *   (url\nsize\nmime) from the `_podcast_media_url` meta so WordPress's
 *   built-in RSS2 feed outputs a proper <enclosure> tag.
 *
 * – On rss2_ns / rss2_head: adds iTunes namespace and channel-level tags
 *   (author, category, image, owner, explicit, etc.).
 */

defined( 'ABSPATH' ) || exit;

class Podcast_Blocks_Enclosure {

	private static $instance = null;

    /**
     * Singleton instance
     */
    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	public function __construct() {
		add_action( 'save_post', array( $this, 'update_enclosure_meta' ), 20, 2 );
		add_action( 'rss2_ns',   array( $this, 'add_itunes_namespace' ) );
		add_action( 'rss2_head', array( $this, 'add_itunes_channel_tags' ) );
	}

	// -------------------------------------------------------------------------
	// Enclosure meta
	// -------------------------------------------------------------------------

	/**
	 * Build and save the `enclosure` post meta whenever a post containing the
	 * podcast-episode block is saved.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function update_enclosure_meta( $post_id, $post ) {
		// Skip revisions, autosaves, and non-public post types.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Only act when the post contains one of the podcast blocks.
		$has_episode = has_block( 'podcast-blocks/podcast-episode', $post );
		$has_player  = has_block( 'podcast-blocks/podcast-player',  $post );
		if ( ! $has_episode && ! $has_player ) {
			return;
		}

		$media_url = get_post_meta( $post_id, '_podcast_media_url', true );
		if ( empty( $media_url ) ) {
			return;
		}

		$mime_type = get_post_meta( $post_id, '_podcast_media_mime', true );
		if ( empty( $mime_type ) ) {
			$mime_type = $this->guess_mime_type( $media_url );
			update_post_meta( $post_id, '_podcast_media_mime', $mime_type );
		}

		$file_size = (int) get_post_meta( $post_id, '_podcast_media_size', true );
		if ( $file_size <= 0 ) {
			$file_size = $this->get_file_size( $media_url );
			if ( $file_size > 0 ) {
				update_post_meta( $post_id, '_podcast_media_size', $file_size );
			}
		}

		// WordPress enclosure format: url\nfilesize\nmimetype
		$enclosure_value = sprintf( "%s\n%d\n%s", $media_url, $file_size, $mime_type );

		// Replace any previously stored enclosure for this post.
		update_post_meta( $post_id, 'enclosure', $enclosure_value );
	}

	// -------------------------------------------------------------------------
	// RSS feed – iTunes namespace & channel tags
	// -------------------------------------------------------------------------

	/**
	 * Output the iTunes and content XML namespace declarations inside <rss>.
	 */
	public function add_itunes_namespace() {
		echo 'xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"' . "\n\t";
		echo 'xmlns:content="http://purl.org/rss/1.0/modules/content/"' . "\n\t";
	}

	/**
	 * Output iTunes channel-level tags inside <channel>.
	 */
	public function add_itunes_channel_tags() {
		$options = get_option( 'podcast_blocks_options', array() );

		$title       = ! empty( $options['title'] )            ? $options['title']            : get_bloginfo( 'name' );
		$description = ! empty( $options['description'] )      ? $options['description']      : get_bloginfo( 'description' );
		$author      = ! empty( $options['author'] )           ? $options['author']           : get_bloginfo( 'name' );
		$email       = ! empty( $options['email'] )            ? $options['email']            : get_bloginfo( 'admin_email' );
		$language    = ! empty( $options['language'] )         ? $options['language']         : get_bloginfo( 'language' );
		$explicit    = ! empty( $options['explicit'] )         ? 'yes'                        : 'no';
		$cat_primary = ! empty( $options['category_primary'] ) ? $options['category_primary'] : '';
		$cat_second  = ! empty( $options['category_secondary'] ) ? $options['category_secondary'] : '';
		$artwork_id  = ! empty( $options['artwork_id'] )       ? absint( $options['artwork_id'] ) : 0;
		$artwork_url = $artwork_id ? wp_get_attachment_url( $artwork_id ) : '';

		echo "\t" . '<itunes:author>' . esc_html( $author ) . '</itunes:author>' . "\n";
		echo "\t" . '<itunes:summary>' . esc_html( $description ) . '</itunes:summary>' . "\n";
		echo "\t" . '<itunes:explicit>' . esc_html( $explicit ) . '</itunes:explicit>' . "\n";
		echo "\t" . '<language>' . esc_html( $language ) . '</language>' . "\n";

		if ( $email ) {
			echo "\t" . '<itunes:owner>' . "\n";
			echo "\t\t" . '<itunes:name>' . esc_html( $author ) . '</itunes:name>' . "\n";
			echo "\t\t" . '<itunes:email>' . esc_html( $email ) . '</itunes:email>' . "\n";
			echo "\t" . '</itunes:owner>' . "\n";
		}

		if ( $artwork_url ) {
			echo "\t" . '<itunes:image href="' . esc_url( $artwork_url ) . '" />' . "\n";
		}

		if ( $cat_primary ) {
			// If a subcategory was chosen that lives under a parent, we need both.
			$parent = $this->get_parent_category( $cat_primary );
			if ( $parent ) {
				echo "\t" . '<itunes:category text="' . esc_attr( $parent ) . '">' . "\n";
				echo "\t\t" . '<itunes:category text="' . esc_attr( $cat_primary ) . '" />' . "\n";
				echo "\t" . '</itunes:category>' . "\n";
			} else {
				echo "\t" . '<itunes:category text="' . esc_attr( $cat_primary ) . '">';
				if ( $cat_second ) {
					echo "\n\t\t" . '<itunes:category text="' . esc_attr( $cat_second ) . '" />' . "\n\t";
				}
				echo '</itunes:category>' . "\n";
			}
		}
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Guess a MIME type from a file URL's extension.
	 *
	 * @param string $url Media URL.
	 * @return string     MIME type string.
	 */
	private function guess_mime_type( $url ) {
		$ext = strtolower( pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );

		$map = array(
			'mp3'  => 'audio/mpeg',
			'mp4'  => 'video/mp4',
			'm4a'  => 'audio/mp4',
			'm4v'  => 'video/mp4',
			'ogg'  => 'audio/ogg',
			'oga'  => 'audio/ogg',
			'ogv'  => 'video/ogg',
			'webm' => 'video/webm',
			'wav'  => 'audio/wav',
			'flac' => 'audio/flac',
			'aac'  => 'audio/aac',
			'mov'  => 'video/quicktime',
		);

		return isset( $map[ $ext ] ) ? $map[ $ext ] : 'audio/mpeg';
	}

	/**
	 * Determine file size.  For files in the WordPress uploads directory the
	 * local path is resolved; for remote URLs a HEAD request is attempted.
	 *
	 * @param string $url Media URL.
	 * @return int        File size in bytes, or 0 on failure.
	 */
	private function get_file_size( $url ) {
		$upload_dir = wp_upload_dir();
		$base_url   = $upload_dir['baseurl'];
		$base_dir   = $upload_dir['basedir'];

		// Local file shortcut.
		if ( strpos( $url, $base_url ) === 0 ) {
			$local_path = str_replace( $base_url, $base_dir, $url );
			if ( file_exists( $local_path ) ) {
				return (int) filesize( $local_path );
			}
		}

		// Remote file: HEAD request.
		$response = wp_remote_head( $url, array( 'timeout' => 10 ) );
		if ( ! is_wp_error( $response ) ) {
			$length = wp_remote_retrieve_header( $response, 'content-length' );
			if ( $length ) {
				return (int) $length;
			}
		}

		return 0;
	}

	/**
	 * Return the parent top-level category for a given iTunes subcategory,
	 * or empty string if $cat is already a top-level category.
	 *
	 * @param string $cat Category value.
	 * @return string
	 */
	private function get_parent_category( $cat ) {
		$all = Podcast_Blocks_Shared::get_itunes_categories();
		
		foreach ( $all as $parent => $subcats ) {
			if ( in_array( $cat, $subcats, true ) ) {
				return $parent;
			}
		}

		return '';
	}
}

// eof
<?php
/**
 * Podcast Blocks – podcast-only RSS 2.0 feed template.
 *
 *   Pretty permalinks : https://example.com/feed/podcast
 *   Query string      : https://example.com/?feed=podcast
 *
 * The main query is pre-filtered (via the pre_get_posts hook in
 * podcast-blocks.php) to only include posts that have a non-empty
 * _podcast_media_url meta value, meaning posts saved with a Podcast Episode
 * block.
 *
 * WordPress categories and comments are intentionally excluded from every
 * <item> element. Only standard RSS 2.0 fields and iTunes podcast extensions
 * are output.
 */

defined( 'ABSPATH' ) || exit;

/**
 * podcast blocks RSS feed
 */
function podcast_blocks_feed() {

	// ── HTTP headers ──────────────────────────────────────────────────────────────
	header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );

	// ── Pull podcast settings ──────────────────────────────────────────────────────
	$podcast_blocks_options = get_option( 'podcast_blocks_options', array() );
	$pb_title    = ! empty( $podcast_blocks_options['title'] )              ? $podcast_blocks_options['title']              : get_bloginfo( 'name' );
	$pb_desc     = ! empty( $podcast_blocks_options['description'] )        ? $podcast_blocks_options['description']        : get_bloginfo( 'description' );
	$pb_author   = ! empty( $podcast_blocks_options['author'] )             ? $podcast_blocks_options['author']             : get_bloginfo( 'name' );
	$pb_email    = ! empty( $podcast_blocks_options['email'] )              ? $podcast_blocks_options['email']              : '';
	$pb_website  = ! empty( $podcast_blocks_options['website'] )            ? $podcast_blocks_options['website']            : home_url();
	$pb_language = ! empty( $podcast_blocks_options['language'] )           ? $podcast_blocks_options['language']           : get_bloginfo( 'language' );
	$pb_explicit = ! empty( $podcast_blocks_options['explicit'] )           ? 'yes'                          				: 'no';
	$pb_cat1     = ! empty( $podcast_blocks_options['category_primary'] )   ? $podcast_blocks_options['category_primary']   : '';
	$pb_cat_p    = ! empty( $podcast_blocks_options['category_primary'] )   ? $podcast_blocks_options['category_primary']   : '';
	$pb_cat2     = ! empty( $podcast_blocks_options['category_secondary'] ) ? $podcast_blocks_options['category_secondary'] : '';
	$pb_cat_s    = ! empty( $podcast_blocks_options['category_secondary'] ) ? $podcast_blocks_options['category_secondary'] : '';
	$pb_art_id   = ! empty( $podcast_blocks_options['artwork_id'] )         ? absint( $podcast_blocks_options['artwork_id'] ) : 0;
	$pb_art_url  = $pb_art_id ? wp_get_attachment_url( $pb_art_id ) : '';
	$feed_url   = get_feed_link( 'podcast' );
	$last_build = mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false );

	// ── XML declaration ───────────────────────────────────────────────────────────
	echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?>' . "\n";

	?>
<rss version="2.0"
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
	xmlns:atom="http://www.w3.org/2005/Atom"
>
<channel>
	<title><?php echo esc_html( $pb_title ); ?></title>
	<link><?php echo esc_url( $pb_website ); ?></link>
	<description><![CDATA[<?php echo mb_substr( $pb_desc, 0, 10000 ); ?>]]></description>
	<language><?php echo esc_html( $pb_language ); ?></language>
	<lastBuildDate><?php echo esc_html( $last_build ); ?></lastBuildDate>
	<generator>Podcast Blocks <?php echo esc_html( PODCAST_BLOCKS_VERSION ); ?> (https://www.podcastblocks.com)</generator>
	<atom:link href="<?php echo esc_url( $feed_url ); ?>" rel="self" type="application/rss+xml" />
	<itunes:type>episodic</itunes:type>
	<itunes:author><?php echo esc_html( $pb_author ); ?></itunes:author>
	<itunes:explicit><?php echo esc_html( $pb_explicit ); ?></itunes:explicit>
	<?php if ( $pb_art_url ) : ?><itunes:image href="<?php echo esc_url( $pb_art_url ); ?>" />
	<?php endif; ?>
	<?php if ( $pb_author || $pb_email ) : ?><itunes:owner>
<?php if ( $pb_author ) : ?>
		<itunes:name><?php echo esc_html( $pb_author ); ?></itunes:name>
<?php endif; 
	if ( $pb_email ) : ?>
		<itunes:email><?php echo esc_html( $pb_email ); ?></itunes:email>
<?php endif; ?>
	</itunes:owner>
<?php endif;

	$pb_cats = array($pb_cat_p, $pb_cat_s);
	$itunes_all_cats = Podcast_Blocks_Shared::get_itunes_categories();
	foreach( $pb_cats as $bp_cat) {
		if ( $bp_cat ) {
			// Check if $bp_cat is a subcategory and find its parent.
			$cat_parent = '';
			foreach ( $itunes_all_cats as $top => $subs ) {
				if ( in_array( $bp_cat, $subs, true ) ) {
					$cat_parent = $top;
					break;
				}
			}

			if ( $cat_parent ) {
				// pb_cat1 is itself a subcategory — wrap it inside its parent.
?>	<itunes:category text="<?php echo esc_attr( $cat_parent ); ?>">
		<itunes:category text="<?php echo esc_attr( $bp_cat ); ?>" />
	</itunes:category>
<?php
			} else {
				// pb_cat1 is a top-level category; pb_cat2 (if set) goes inside it.
				?>	<itunes:category text="<?php echo esc_attr( $bp_cat ); ?>" />
				<?php
			}
		}
	}
?>
		<?php while ( have_posts() ) : the_post(); ?>
		<?php
			$post_id      = get_the_ID();
			$permalink    = get_permalink();
			$post_title   = get_the_title();
			$pub_date     = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false );
			$guid         = get_the_guid( $post_id );

			// ── Excerpt / description ──────────────────────────────────────
			// Use the manual excerpt if available, otherwise generate one from
			// post content. wp_strip_all_tags() ensures plain text for RSS.
			$excerpt = get_the_excerpt();
			$excerpt = wp_strip_all_tags( $excerpt );
			if ( empty( $excerpt ) ) {
				$excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 55, '&hellip;' );
			}
			$excerpt = mb_substr( $excerpt, 0, 10000 );

			// ── Enclosure data from WordPress meta ─────────────────────────
			// The `enclosure` post meta is written by class-enclosure.php on
			// save_post. Format: url\nfilesize\nmimetype
			$enc_url    = '';
			$enc_length = '0';
			$enc_type   = 'audio/mpeg';
			$enclosure_raw = get_post_meta( $post_id, 'enclosure', true );
			if ( $enclosure_raw ) {
				$enc_parts  = array_map( 'trim', explode( "\n", $enclosure_raw ) );
				$enc_url    = isset( $enc_parts[0] ) ? $enc_parts[0] : '';
				$enc_length = isset( $enc_parts[1] ) && is_numeric( $enc_parts[1] ) ? $enc_parts[1] : '0';
				$enc_type   = isset( $enc_parts[2] ) && $enc_parts[2] ? $enc_parts[2] : 'audio/mpeg';
			}

			// Fallback: read directly from block meta if enclosure not yet set.
			if ( empty( $enc_url ) ) {
				$enc_url = get_post_meta( $post_id, '_podcast_media_url', true );
				if ( $enc_url ) {
					$enc_type   = get_post_meta( $post_id, '_podcast_media_mime', true ) ?: 'audio/mpeg';
					$enc_length = (string) ( get_post_meta( $post_id, '_podcast_media_size', true ) ?: 0 );
				}
			}

			// ── Episode artwork ────────────────────────────────────────────
			// Use the post's featured image if available, else fall back to the
			// channel artwork set on the plugin settings page.
			$episode_img = has_post_thumbnail( $post_id )
				? get_the_post_thumbnail_url( $post_id, 'full' )
				: $pb_art_url;

			// ── Optional itunes:duration from post meta ────────────────────
			// Stored as H:MM:SS or seconds. Output only if the meta is set.
			$duration = get_post_meta( $post_id, '_podcast_episode_duration', true );
		?>

	<item>
		<title><?php echo esc_html( $post_title ); ?></title>
		<link><?php echo esc_url( $permalink ); ?></link>
		<description><![CDATA[<?php echo $excerpt; ?>]]></description>
		<pubDate><?php echo esc_html( $pub_date ); ?></pubDate>
		<guid isPermaLink="true"><?php echo esc_url( $permalink ); ?></guid>

		<?php if ( $enc_url ) : ?>
		<enclosure
			url="<?php echo esc_url( $enc_url ); ?>"
			length="<?php echo esc_attr( $enc_length ); ?>"
			type="<?php echo esc_attr( $enc_type ); ?>"
		/>
		<?php endif; ?>

		<itunes:title><?php echo esc_html( $post_title ); ?></itunes:title>
		<itunes:author><?php echo esc_html( $pb_author ); ?></itunes:author>
		<itunes:explicit><?php echo esc_html( $pb_explicit ); ?></itunes:explicit>
		<?php if ( $episode_img ) : ?>
		<itunes:image href="<?php echo esc_url( $episode_img ); ?>" />
		<?php endif; ?>
		<?php if ( $duration ) : ?>
		<itunes:duration><?php echo esc_html( $duration ); ?></itunes:duration>
		<?php endif; ?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>
<?php
}

// eof
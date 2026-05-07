<?php
/**
 * Admin settings class for Podcast Blocks.
 *
 * Handles the wp-admin settings page, field rendering, sanitisation, and
 * enqueueing the media-uploader script used for artwork selection.
 */

defined( 'ABSPATH' ) || exit;

class Podcast_Blocks_Admin {

	const OPTION_GROUP = 'podcast_blocks_settings';
	const OPTION_NAME  = 'podcast_blocks_options';

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
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_menu_page() {
		add_menu_page(
			__( 'Podcast Blocks', 'podcast-blocks' ),
			__( 'Podcast Blocks', 'podcast-blocks' ),
			'manage_options',
			'podcast-blocks',
			array( $this, 'render_settings_page' ),
			'dashicons-microphone',
			30
		);
	}

	public function render_settings_page() {
		require_once PODCAST_BLOCKS_PLUGIN_DIR . 'admin/settings-page.php';
		Podcast_Blocks_Feed_Settings::page();
	}

	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
			)
		);

		// ── Section 1: Podcast Settings ──────────────────────────────────
		add_settings_section(
			'podcast_blocks_general',
			__( 'Podcast Settings', 'podcast-blocks' ),
			'__return_false',
			'podcast-blocks'
		);

		$info_fields = array(
			'title'              => __( 'Podcast Title', 'podcast-blocks' ),
			'description'        => __( 'Podcast Description', 'podcast-blocks' ),
			'author'             => __( 'Author / Artist Name', 'podcast-blocks' ),
			'email'              => __( 'Contact Email', 'podcast-blocks' ),
			'website'            => __( 'Website URL', 'podcast-blocks' ),
			'language'           => __( 'Language (e.g. en-US)', 'podcast-blocks' ),
			'explicit'           => __( 'Explicit Content', 'podcast-blocks' ),
			'category_primary'   => __( 'Primary Category', 'podcast-blocks' ) . ' <span class="required">'. __('required', 'podcast-blocks') .'</span>',
			'category_secondary' => __( 'Secondary Category', 'podcast-blocks' ),
			'artwork_id'         => __( 'Podcast Artwork', 'podcast-blocks' ) . ' <span class="required">'. __('required', 'podcast-blocks') .'</span>',
		);

		foreach ( $info_fields as $key => $label ) {
			add_settings_field(
				"podcast_blocks_{$key}",
				$label,
				array( $this, "render_field_{$key}" ),
				'podcast-blocks',
				'podcast_blocks_general'
			);
		}

		add_settings_section(
			'podcast_blocks_subscribe',
			__( 'Subscribe Links', 'podcast-blocks' ),
			array( $this, 'render_subscribe_section_intro' ),
			'podcast-blocks'
		);

		$subscribe_fields = array(
			'subscribe_apple'   => __( 'Apple Podcasts URL', 'podcast-blocks' ),
			'subscribe_spotify' => __( 'Spotify URL', 'podcast-blocks' ),
			'subscribe_audible' => __( 'Amazon Music URL', 'podcast-blocks' ),
			'subscribe_youtube' => __( 'YouTube URL', 'podcast-blocks' ),
		);

		foreach ( $subscribe_fields as $key => $label ) {
			add_settings_field(
				"podcast_blocks_{$key}",
				$label,
				array( $this, "render_field_{$key}" ),
				'podcast-blocks',
				'podcast_blocks_subscribe'
			);
		}
	}

	public function sanitize_options( $input ) {
		$sanitized   = array();

		// Sanitize email fields
		$email_fields = array( 'email' );
		foreach ( $email_fields as $field ) {
			$sanitized[ $field ] = isset( $input[ $field ] ) ? sanitize_email( $input[ $field ] ) : '';
		}
		
		// Sanitize text fields
		$text_fields = array( 'title', 'author', 'language', 'category_primary', 'category_secondary' );
		foreach ( $text_fields as $field ) {
			$sanitized[ $field ] = isset( $input[ $field ] ) ? sanitize_text_field( $input[ $field ] ) : '';
		}

		// Sanitize textarea fields
		$sanitized['description'] = isset( $input['description'] ) ? sanitize_textarea_field( $input['description'] ) : '';

		// Sanitize checkbox fields
		$sanitized['explicit'] = ! empty( $input['explicit'] ) ? sanitize_key('1') : sanitize_key('0');

		// Sanitize artwork ID
		$sanitized['artwork_id'] = isset( $input['artwork_id'] ) ? sanitize_key( absint( $input['artwork_id'] ) ) : 0;

		// Sanitize URL fields
		$url_fields = array( 'website', 'subscribe_apple', 'subscribe_spotify', 'subscribe_audible', 'subscribe_youtube');
		foreach ( $url_fields as $field ) {
			$sanitized[ $field ] = isset( $input[ $field ] ) ? sanitize_url( $input[ $field ] ) : '';
		}

		// If it wasn't sanitized, it is not in $sanitized array, so we can ignore it. This prevents unexpected options from being saved.

		// Let the user know we are saving these settings.
		add_settings_error(
				'podcast_blocks_messages',
				'podcast_blocks_saved',
				__( 'Settings saved.', 'podcast-blocks' ),
				'updated'
			);

		return $sanitized;
	}

	public static function get_option( $key, $default = '' ) {
		$options = get_option( self::OPTION_NAME, array() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	public function render_field_title() {
		printf(
			'<input type="text" name="%s[title]" value="%s" class="regular-text" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $this->get_option( 'title' ) )
		);
		echo '<p class="description">' . esc_html__( 'Use a clear, concise name for your podcast. Apple Podcasts uses this field for search.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'Your site title will be used if blank.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'Note: If you include a long list of keywords in an attempt to game podcast search, your show may be removed from the Apple directory.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_description() {
		printf(
			'<textarea name="%s[description]" rows="5" class="large-text">%s</textarea>',
			esc_attr( self::OPTION_NAME ),
			esc_textarea( $this->get_option( 'description' ) )
		);
		echo '<p class="description">' . esc_html__( 'Your site description will be used if blank.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'The maximum amount of text allowed for this tag is 4000 bytes and may include HTML <a> tags.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_author() {
		printf(
			'<input type="text" name="%s[author]" value="%s" class="regular-text" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $this->get_option( 'author' ) )
		);
		echo '<p class="description">' . esc_html__( 'Show author most often refers to the parent company or network of a podcast, but it can also be used to identify the host(s) if none exists.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_email() {
		printf(
			'<input type="email" name="%s[email]" value="%s" class="regular-text" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $this->get_option( 'email' ) )
		);
		echo '<p class="description">' . esc_html__( 'Used for podcast feed verification.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'Leave blank unless needed temporarily to verify podcast ownership.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_website() {
		$this->render_url_field( 'website', 'https://example.com/podcast' );
		echo '<p class="description">' . esc_html__( 'Leave blank to use this website.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_language() {
		printf(
			'<input type="text" name="%s[language]" value="%s" class="small-text" placeholder="en-US" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $this->get_option( 'language', 'en-US' ) )
		);
		echo '<p class="description">' . esc_html__( 'ISO 639 2-letter language code in lowercase letters with ISO 3166-1 2-letter country code in capital letters separated with a dash. e.g. en-US, es-MX, fr-FR.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'Invalid language codes will cause your feed to fail Apple validation.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_explicit() {
		$checked = $this->get_option( 'explicit' ) ? true : false;
		printf(
			'<label><input type="checkbox" name="%s[explicit]" value="1" %s /> %s</label>',
			esc_attr( self::OPTION_NAME ),
			checked(1, $checked, false),
			esc_html__( 'This podcast contains explicit content', 'podcast-blocks' )
		);
		echo '<p class="description">' . esc_html__( 'When checked, Apple Podcasts displays an Explicit parental advisory graphic for your podcast.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'When unchecked, Apple Podcasts displays a Clean parental advisory graphic for your podcast.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_category_primary() {
		$this->render_category_select( 'category_primary', 'category-primary' );
		echo '<p class="description">' . esc_html__( 'Required. Select the category that best reflects the content of your show. You can also select a subcategory if appropriate.', 'podcast-blocks' ) . '</p>';
	}

	public function render_field_category_secondary() {
		$this->render_category_select( 'category_secondary', 'category-secondary' );
		echo '<p class="description">' . esc_html__( 'Optional. Subcategory or second top-level category.', 'podcast-blocks' ) . '</p>';
		echo '<p class="description">' . esc_html__( 'Note: Although you can specify more than one category and subcategory in your RSS feed, Apple Podcasts only recognizes the first category and subcategory.', 'podcast-blocks' ) . '</p>';
	}

	private function render_category_select( $field, $class ) {
		$categories = Podcast_Blocks_Shared::get_itunes_categories();
		$translated = Podcast_Blocks_Shared::get_itunes_categories_translated();
		$selected   = $this->get_option( $field );

		echo '<select id="'. esc_attr($class)  .'" name="' . esc_attr( self::OPTION_NAME ) . '[' . esc_attr( $field ) . ']">';
		echo '<option value="">' . esc_html__( '— Select a Category —', 'podcast-blocks' ) . '</option>';

		foreach ( $categories as $cat => $subcats ) {
			echo '<optgroup label="' . esc_attr( $translated[ $cat ] ) . '">';
			echo '<option value="' . esc_attr( $cat ) . '"' . selected( $selected, $cat, false ) . '>'
				. esc_html( $translated[ $cat ] ) . '</option>';

			foreach ( $subcats as $subcat ) {
				echo '<option value="' . esc_attr( $subcat ) . '"' . selected( $selected, $subcat, false ) . '>'
					. '&nbsp;&nbsp;&nbsp;' . esc_html( $translated[ $subcat ] ) . '</option>';
			}

			echo '</optgroup>';
		}

		echo '</select>';
	}

	public function render_field_artwork_id() {
		$artwork_id  = absint( $this->get_option( 'artwork_id', 0 ) );
		$artwork_url = $artwork_id ? wp_get_attachment_url( $artwork_id ) : '';
		$has_artwork = ! empty( $artwork_url );
		?>
		<div id="podcast-artwork-wrapper">
			<img
				id="podcast-artwork-preview"
				src="<?php echo $has_artwork ? esc_url( $artwork_url ) : ''; ?>"
				style="max-width:200px;height:auto;display:<?php echo $has_artwork ? 'block' : 'none'; ?>;margin-bottom:10px;border:1px solid #ddd;"
			/>
			<input
				type="hidden"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[artwork_id]"
				id="podcast-artwork-id"
				value="<?php echo esc_attr( $artwork_id ); ?>"
			/>
			<button type="button" class="button" id="podcast-upload-artwork">
				<?php esc_html_e( 'Upload / Select Image', 'podcast-blocks' ); ?>
			</button>
			<button
				type="button"
				class="button"
				id="podcast-remove-artwork"
				style="display:<?php echo $has_artwork ? 'inline-block' : 'none'; ?>;"
			>
				<?php esc_html_e( 'Remove Image', 'podcast-blocks' ); ?>
			</button>
			<p class="description">
				<?php esc_html_e( 'Square image, 600x600 to 1400x1400 pixels, PNG or JPG format. Required for Apple Podcasts listing.', 'podcast-blocks' ); ?>
			</p>
		</div>
		<?php
	}

	public function render_subscribe_section_intro() {
		echo '<p>' . esc_html__(
			'Enter the URLs for each platform where listeners can subscribe to your podcast. ',	'podcast-blocks') . '</p>';
		echo '<p>' . esc_html__(
			'Only platforms with a URL entered here will appear in the Subscribe popup on the Podcast Player block.', 'podcast-blocks') . '</p>';
	}

	public function render_field_subscribe_apple() {
		$this->render_url_field( 'subscribe_apple', 'https://podcasts.apple.com/…' );
		echo '<p class="description">' . esc_html__( 'Your Apple Podcasts show page URL.', 'podcast-blocks' ) . '</p>';
		$link = 'https://podcasters.apple.com/';
		echo '<p class="description">'
			. '<a href="' . esc_url( $link ) . '" target="_blank">'
			. esc_html__( 'Submit podcast to Apple Podcasts', 'podcast-blocks' )
			. '</a></p>';
	}

	public function render_field_subscribe_spotify() {
		$this->render_url_field( 'subscribe_spotify', 'https://open.spotify.com/show/…' );
		echo '<p class="description">' . esc_html__( 'Your Spotify show page URL.', 'podcast-blocks' ) . '</p>';
		$link = 'https://creators.spotify.com/';
		echo '<p class="description">'
			. '<a href="' . esc_url( $link ) . '" target="_blank">'
			. esc_html__( 'Submit podcast to Spotify', 'podcast-blocks' )
			. '</a></p>';
	}

	public function render_field_subscribe_audible() {
		$this->render_url_field( 'subscribe_audible', 'https://www.audible.com/pd/…' );
		echo '<p class="description">' . esc_html__( 'Your Audible Channels or Amazon Music podcast URL.', 'podcast-blocks' ) . '</p>';
		$link = 'https://podcasters.amazon.com/';
		echo '<p class="description">'
			. '<a href="' . esc_url( $link ) . '" target="_blank">'
			. esc_html__( 'Submit podcast to Amazon Music', 'podcast-blocks' )
			. '</a></p>';
	}

	public function render_field_subscribe_youtube() {
		$this->render_url_field( 'subscribe_youtube', 'https://www.youtube.com/@…' );
		echo '<p class="description">' . esc_html__( 'Your YouTube channel or playlist URL.', 'podcast-blocks' ) . '</p>';
		$link = 'https://www.youtube.com/creators/podcasts/#t-lockup-5339516823011328-header-4';
		echo '<p class="description">'
			. '<a href="' . esc_url( $link ) . '" target="_blank">'
			. esc_html__( 'Submit podcast to YouTube', 'podcast-blocks' )
			. '</a></p>';
	}

	/**
	 * Render a generic URL input field.
	 *
	 * @param string $key         Option key (without option name prefix).
	 * @param string $placeholder Placeholder text.
	 */
	private function render_url_field( $key, $placeholder = '' ) {
		printf(
			'<input type="url" name="%s[%s]" value="%s" class="regular-text" placeholder="%s" pattern="https://.*" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $key ),
			esc_attr( $this->get_option( $key ) ),
			esc_attr( $placeholder )
		);
	}

	/**
	 * Enqueue Scripts
	 * 
	 *  @param string $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_podcast-blocks' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'podcast-blocks-admin',
			PODCAST_BLOCKS_PLUGIN_URL . 'admin/admin.js',
			array( 'jquery' ),
			PODCAST_BLOCKS_VERSION,
			true
		);
		wp_enqueue_style(
			'podcast-blocks-admin',
			PODCAST_BLOCKS_PLUGIN_URL . 'admin/admin.css',
			array(),
			PODCAST_BLOCKS_VERSION
		);
	}
}

// eof
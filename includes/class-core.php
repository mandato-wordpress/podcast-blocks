<?php
/**
 * Podcast Blocks Core plugin
 */
defined( 'ABSPATH' ) || exit;

class Podcast_Blocks_Core {

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

    /**
     * Constructor
     */
    function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

    /**
     * WordPress add feed callback function
     */
    public function init() {
        add_feed( 'podcast', array( $this, 'add_feed_callback' ) );

        // Flush rewrite rules once after activation.
        if ( get_option( 'podcast_blocks_flush_rewrite' ) ) {
            delete_option( 'podcast_blocks_flush_rewrite' );
            flush_rewrite_rules( false );
        }

        /**
         * Register all Podcast Blocks blocks.
         */
        register_block_type(
            PODCAST_BLOCKS_PLUGIN_DIR . 'build/podcast-episode',
            array(
                'render_callback' => array( $this, 'podcast_episode_render_callback' ),
            )
        );

        /**
         * Register post meta fields exposed to the REST API so the block editor can
         * read and write them directly.
         */
        $shared_args = array(
            'show_in_rest'  => true,
            'single'        => true,
            'auth_callback' => function () {
                return current_user_can( 'edit_posts' );
            },
        );

        register_post_meta(
            'post',
            '_podcast_media_url',
            array_merge( $shared_args, array(
                'type'    => 'string',
                'default' => '',
            ) )
        );

        register_post_meta(
            'post',
            '_podcast_media_type',
            array_merge( $shared_args, array(
                'type'    => 'string',
                'default' => 'audio',
            ) )
        );

        register_post_meta(
            'post',
            '_podcast_media_mime',
            array_merge( $shared_args, array(
                'type'    => 'string',
                'default' => '',
            ) )
        );

        register_post_meta(
            'post',
            '_podcast_media_size',
            array_merge( $shared_args, array(
                'type'    => 'integer',
                'default' => 0,
            ) )
        );
    }

    /**
     * WordPress add_feed callback function
     */
    public function add_feed_callback() {
        require PODCAST_BLOCKS_PLUGIN_DIR . 'includes/podcast-feed.php';
        podcast_blocks_feed();
    }

    /**
     * WordPress action pre_get_posts
     */
    public function pre_get_posts( $query ) {
        if ( ! $query->is_main_query() || ! $query->is_feed( 'podcast' ) ) {
            return;
        }

        $query->set( 'post_type', 'post' );
        $query->set( 'post_status', 'publish' );
        $query->set(
            'meta_query',
            array(
                array(
                    'key'     => '_podcast_media_url',
                    'value'   => '',
                    'compare' => '!=',
                ),
            )
        );
    }

    /**
     * Build the list of configured subscribe services from the plugin options.
     * Only services with a non-empty URL are returned.
     *
     * @param array $options  Value of the 'podcast_blocks_options' option.
     * @return array[]        Each item: [ id, label, url, icon ].
     */
    private function get_subscribe_services( $options ) {
        $all = array(
            'apple'   => __( 'Apple Podcasts', 'podcast-blocks' ),
            'spotify' => __( 'Spotify', 'podcast-blocks' ),
            'audible' => __( 'Audible', 'podcast-blocks' ),
            'youtube' => __( 'YouTube', 'podcast-blocks' ),
            'rss'     => __( 'RSS', 'podcast-blocks' ),
        );

        $configured = array();
        foreach ( $all as $id => $label ) {
            if( $id == 'rss' ) {
                $url = get_feed_link( 'podcast' );
            } else {
                $url = isset( $options[ "subscribe_{$id}" ] ) ? $options[ "subscribe_{$id}" ] : '';
            }
            if ( ! empty( $url ) ) {
                $configured[] = array(
                    'id'    => $id,
                    'label' => $label,
                    'url'   => $url,
                    'icon'  => Podcast_Blocks_Shared::service_icon( $id ),
                );
            }
        }

        return $configured;
    }

    /**
     * Render callback for the podcast-episode block.
     *
     * Outputs the WordPress native audio/video player (via wp_audio_shortcode /
     * wp_video_shortcode), a Download link, a Subscribe button, and the
     * subscribe modal markup populated from the plugin settings.
     *
     * @param array  $attributes Block attributes.
     * @param string $content    Inner content (unused – dynamic block).
     * @return string            Rendered HTML.
     */
    public function podcast_episode_render_callback( $attributes, $content ) {
        // Unique modal ID — safe to call render_player multiple times per page.
        static $player_instance = 0;

        $media_url  = isset( $attributes['mediaUrl'] ) ? $attributes['mediaUrl'] : '';
        $media_type = isset( $attributes['mediaType'] ) ? $attributes['mediaType'] : 'audio';

        if ( empty( $media_url ) ) {
            return '';
        }

        $options  = get_option( 'podcast_blocks_options', array() );
        // $subscribe_url  = get_option( 'podcast_subscribe_url', '' ); // Future option
        $subscribe_url = '';

        $services = $this->get_subscribe_services( $options );
        $has_subs = ! empty( $services );

        $player_instance++;
        $modal_id = 'pb-subscribe-modal-' . get_the_ID() . '-' . $player_instance;

        // Derive a download filename from the URL path.
        $filename = sanitize_file_name( basename( (string) wp_parse_url( $media_url, PHP_URL_PATH ) ) );

        // ── Inline SVG for the link-bar icons ────────────────────────────────
        $icon_close = Podcast_Blocks_Shared::service_icon('close');
        ob_start();
        ?>
        <div class="wp-block-podcast-blocks-podcast-episode podcast-episode">

            <?php
            if ( 'video' === $media_type ) {
                echo do_shortcode('[video src="' . esc_url($media_url) . '"]');
            } else {
                echo do_shortcode('[audio src="' . esc_url($media_url) . '"]');
            }
            ?>

            <div class="podcast-episode-links">
                <a
                    href="<?php echo esc_url( $media_url ); ?>"
                    download="<?php echo esc_attr( $filename ); ?>"
                ><?php esc_html_e( 'Download episode', 'podcast-blocks' ); ?></a>
                <?php if ( $has_subs ) : ?> | 
                <a
                    <?php if( $subscribe_url ) : ?>
                    href="<?php echo esc_url( $subscribe_url ); ?>"
                    <?php else : ?>
                    style="cursor: pointer;"
                    <?php endif; ?>
                    class="pb-subscribe-btn"
                    data-modal="<?php echo esc_attr( $modal_id ); ?>"
                    aria-haspopup="dialog"
                    aria-expanded="false"
                >
                    <?php esc_html_e( 'Subscribe to podcast', 'podcast-blocks' ); ?>
                </a>
                <?php endif; ?>
            </div>

            <?php if ( $has_subs ) : ?>
            <div
                class="pb-subscribe-modal"
                id="<?php echo esc_attr( $modal_id ); ?>"
                role="dialog"
                aria-modal="true"
                aria-label="<?php esc_attr_e( 'Subscribe to this podcast', 'podcast-blocks' ); ?>"
                aria-hidden="true"
            >
                <div class="pb-subscribe-modal-overlay" tabindex="-1"></div>

                <div class="pb-subscribe-modal-box">

                    <button
                        class="pb-subscribe-modal-close"
                        type="button"
                        aria-label="<?php esc_attr_e( 'Close subscribe panel', 'podcast-blocks' ); ?>"
                    >
                        <?php echo $icon_close; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    </button>

                    <h3 class="pb-subscribe-modal-title">
                        <?php esc_html_e( 'Subscribe to podcast', 'podcast-blocks' ); ?>
                    </h3>
                    <p class="pb-subscribe-modal-subtitle">
                        <?php esc_html_e( 'Choose your preferred podcast app:', 'podcast-blocks' ); ?>
                    </p>

                    <div class="pb-subscribe-modal-services">
                        <?php foreach ( $services as $service ) : ?>
                        <a
                            href="<?php echo esc_url( $service['url'] ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="pb-service pb-service-<?php echo esc_attr( $service['id'] ); ?>"
                        >
                            <span class="pb-service-icon">
                                <?php echo $service['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
                            </span>
                            <span class="pb-service-label">
                                <?php echo esc_html( $service['label'] ); ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php
        return ob_get_clean();
    }

}

// eof
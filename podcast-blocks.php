<?php
/**
 * Plugin Name:       Podcast Blocks
 * Plugin URI:        https://www.podcastblocks.com/
 * Description:       Create and manage podcast episodes using Gutenberg blocks. Includes Apple Podcasts-compatible RSS feed.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Angelo Mandato
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       podcast-blocks
 */

defined( 'ABSPATH' ) || exit;

define( 'PODCAST_BLOCKS_VERSION', '1.0.0' );
define( 'PODCAST_BLOCKS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PODCAST_BLOCKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Podcast_Blocks activation and deactivation hooks.
 * These functions need to be in the plugin entry file.
 */
class Podcast_Blocks {

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

    function __construct() {
		// ── Activation / deactivation ─────────────────────────────────────────────────
		// Flush rewrite rules on activation so the /feed/podcast URL works immediately
		// without requiring the admin to visit Settings → Permalinks manually.
        register_activation_hook( __FILE__, array( $this, 'register_activation_hook' ) );
        register_deactivation_hook( __FILE__, array( $this, 'register_deactivation_hook' ) );
    }

    function register_activation_hook() {
        // Schedule a rewrite flush on the very next request; calling
        // flush_rewrite_rules() directly inside an activation hook can be
        // unreliable because rewrite rules haven't been registered yet.
        update_option( 'podcast_blocks_flush_rewrite', '1' );
    }

    function register_deactivation_hook() {
        flush_rewrite_rules();
    }
}
Podcast_Blocks::get_instance();

// Shared code used throughout the plugin
require_once PODCAST_BLOCKS_PLUGIN_DIR . 'includes/class-shared.php';

// RSS / enclosure handler (needed on frontend for feeds).
require_once PODCAST_BLOCKS_PLUGIN_DIR . 'includes/class-enclosure.php';
Podcast_Blocks_Enclosure::get_instance();

// Core plugin
require_once PODCAST_BLOCKS_PLUGIN_DIR . 'includes/class-core.php';
Podcast_Blocks_Core::get_instance();

// Admin class, inside the wp-admin area only.
if ( is_admin() ) {
	require_once PODCAST_BLOCKS_PLUGIN_DIR . 'admin/class-admin.php';
	Podcast_Blocks_Admin::get_instance();
}

// eof
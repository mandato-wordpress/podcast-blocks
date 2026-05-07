<?php
/**
 * Settings page view template.
 *
 * Rendered via Podcast_Blocks_Admin::render_settings_page().
 */

defined( 'ABSPATH' ) || exit;

class Podcast_Blocks_Feed_Settings {

	public static function page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'podcast-blocks' ) );
		}

		$podcast_artwork_created = false;
		$podcast_artwork_id = Podcast_Blocks_Admin::get_option('artwork_id');
		if( $podcast_artwork_id )
			$podcast_artwork_created = true;
		$category_selected = false;
		$primary_category = Podcast_Blocks_Admin::get_option('category_primary');
		if( $primary_category )
			$category_selected = true;

		settings_errors( 'podcast_blocks_messages' );
		?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php if( $podcast_artwork_created == false || $category_selected == false ) { ?>
	<p class="description">
		<?php esc_html_e( 'You are only a few steps away from having a podcast!', 'podcast-blocks' ); ?>
	</p>
	<?php } else { ?>
	<p class="description">
		<?php esc_html_e( 'Hello podcaster!', 'podcast-blocks' ); ?>
	</p>
	<?php } ?>

	<h2><?php echo esc_html( __('Getting Started', 'podcast-blocks') );?></h2>
	<div class="podcast-blocks-status-cards" style="display:flex;gap:20px;flex-wrap:wrap;margin-top:20px;">
		<div class="podcast-blocks-card" style="background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;min-width:220px;box-shadow:0 1px 2px rgba(0,0,0,.07);">
			<h2 style="margin-top:0;font-size:14px;text-transform:uppercase;color:#646970;letter-spacing:.5px;">
				<?php esc_html_e( 'Podcast Artwork', 'podcast-blocks' ); ?>
			</h2>
			<p class="podcast-blocks-card-value podcast-blocks-<?php echo esc_attr($podcast_artwork_created ? 'green': 'red'); ?>">
				<span class="dashicons dashicons-<?php echo esc_attr($podcast_artwork_created ? 'yes': 'warning'); ?> podcast-blocks-dashicon-md"></span>
				<?php echo esc_html( $podcast_artwork_created  ? __('Yes', 'podcast-blocks') : __('No', 'podcast-blocks') ); ?>
			</p>
			<?php if( $podcast_artwork_created == false ) { ?>
			<p class="description">
				<?php esc_html_e( 'You need podcast artwork.', 'podcast-blocks' ); ?>
			</p>
			<p>
				<a href="#podcast-artwork-wrapper">
					<?php esc_html_e( 'Upload podcast artwork', 'podcast-blocks' ); ?>
				</a>
			</p>
			<?php } else { ?>
			<p class="description">
				<?php esc_html_e( 'Perfect!', 'podcast-blocks' ); ?>
			</p>
			<?php } ?>
		</div>
		<div class="podcast-blocks-card" style="background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;min-width:220px;box-shadow:0 1px 2px rgba(0,0,0,.07);">
			<h2 style="margin-top:0;font-size:14px;text-transform:uppercase;color:#646970;letter-spacing:.5px;">
				<?php esc_html_e( 'Category', 'podcast-blocks' ); ?>
			</h2>
			<p class="podcast-blocks-card-value podcast-blocks-<?php echo esc_attr($category_selected ? 'green': 'red'); ?>">
				<span class="dashicons dashicons-<?php echo esc_attr($category_selected ? 'yes': 'warning'); ?> podcast-blocks-dashicon-md"></span>
				<?php echo esc_html( $category_selected  ? __('Selected', 'podcast-blocks') : __('Not Selected', 'podcast-blocks') ); ?>
			</p>
			<?php if( $category_selected == false ) { ?>
			<p class="description">
				
				<?php esc_html_e( 'You must select a primary category.', 'podcast-blocks' ); ?>
			</p>
			<p>
				<a href="#category-primary">
					<?php esc_html_e( 'Select Primary Category', 'podcast-blocks' ); ?>
				</a>
			</p>
			<?php } else { ?>
			<p class="description">
				<?php esc_html_e( 'Awesome!', 'podcast-blocks' ); ?>
			</p>
			<?php } ?>
		</div>
	</div>

	<div style="background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;margin-top:20px;box-shadow:0 1px 2px rgba(0,0,0,.07);">
		<h2 style="margin-top:0;"><?php esc_html_e( 'Podcast RSS Feed', 'podcast-blocks' ); ?></h2>
		<p>
			<a href="<?php echo esc_url(get_feed_link('podcast')); ?>" target="_blank">
				<?php echo esc_html(get_feed_link('podcast')); ?>
			</a>
		</p>
		<p class="description">
			<?php esc_html_e( 'Submit this URL to Apple Podcasts, Spotify, and other podcast directories.', 'podcast-blocks' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'You must have at least 1 episode to submit your podcast.', 'podcast-blocks' ); ?>
		</p>
	</div>

	<form action="options.php" method="post" enctype="multipart/form-data">
		<?php
		settings_fields( Podcast_Blocks_Admin::OPTION_GROUP );
		do_settings_sections( 'podcast-blocks' );
		submit_button( __( 'Save Settings', 'podcast-blocks' ) );
		?>
	</form>
</div>
	<?php
	}

}

// eof
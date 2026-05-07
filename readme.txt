=== Podcast Blocks ===
Contributors: amandato
Tags: podcast, gutenberg, blocks, itunes, apple
Requires at least: 6.5
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 8.1
License: GPL-2.0-or-later
Donate link: https://www.podcastblocks.com
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create and manage podcast episodes using Gutenberg blocks with full Apple Podcasts-compatible RSS feed support.

== Description ==

**Podcast Blocks** makes it easy to turn any WordPress site into a full-featured podcast website. Add the *Podcast Episode* Gutenberg block to any post, upload or link your audio or video file, and the site will take over from there.

This truly is a simple podcasting plugin with the intent to stay simple and lightweight and does not require a paid subscription to use it.

ATTENTION: This version is intended for new podcasts. Migrating from an existing podcasting WordPress plugin such as PowerPress is not available in this release.

= Features =

* **Podcast Episode block** — Upload or link audio and video files directly from the block editor. Uploaded files are saved in `wp-content/uploads` organized by year and month, exactly the same way the standard WordPress media library works.
* **Apple Podcasts RSS extensions** — The plugin automatically adds the iTunes XML namespace and channel-level tags (author, owner, artwork, categories, explicit flag) to your RSS2 feed so your podcast appears correctly in Apple Podcasts, Spotify, and other podcast directories.
* **Settings page** — Configure podcast title, description, author, contact email, website URL, language, explicit-content flag, primary and secondary iTunes categories, and your show artwork — all from a dedicated *Podcast Blocks* menu in wp-admin.
* **Easy Show artwork upload** — Upload a square show image (600×600 to 1400×1400 px, PNG or JPG) via the native WordPress media uploader. A size warning is shown if the selected image is outside the Apple Podcasts recommended dimensions.

== Installation ==

1. Upload the `podcast-blocks` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Podcast Blocks** in the left-hand admin menu and fill in your podcast information including show artwork.
4. Create or edit any post, insert the **Podcast Episode** block, and upload or link your audio or video file.
5. Your Podcast feed at `https://example.com/feed/podcast/` includes iTunes-compatible channel tags and a proper `<enclosure>` element on each episode post.

== Screenshots == 


== Frequently Asked Questions ==

= Didn't you cofound Blubrry Podcasting and are the software architect who created PowerPress? Why make another podcasting plugin? =

Yes, that is me! After leaving Blubrry in 2022, I spent time talking to podcasters about what frustrated them most. The most common answer was clear: podcasting on WordPress should be simple. So I built Podcast Blocks with exactly that goal.

= How easy is it to podcast with Podcast Blocks? =

**INSANELY EASY!**

1 - Install and enable the plugin, fill out the required settings, and click save.

2 - Create a blog post, add the Podcast Episode block to your page where you want the player to appear, select your media file then publish.

3 - Submit your podcast feed to the podcast directories.

= Which RSS feed URL do I submit to podcast directories? =

Use the podcast only feed: `https://example.com/feed/podcast/` (or `?feed=podcast`).

= Can I link to a file hosted on a CDN, podcast service, or another server? =

Yes. Enter any publicly accessible URL in the Media URL field in the block sidebar.

= What audio and video formats are supported? =

**Audio** — MP3, M4A, AAC, OGG/OGA, FLAC, WAV

**video** — MP4, M4V, WebM, OGV, MOV.

= Can I use more than one Podcast Episode block per post? =

The plugin stores one enclosure per post (the most recently saved block's media URL). For podcast feeds, each post should contain one episode.

= Where is the show artwork stored? =

The artwork is uploaded to the WordPress media library and referenced by attachment ID, so it benefits from all standard WordPress image management and CDN offloading.

= Can I migrate to your plugin from another Podcasting plugin? =

Not currently. This version of Podcast Blocks is intended for a brand new podcast.

== Changelog ==

= 1.0.0 =

* First public release of Podcast Blocks plugin.

= 0.0.1 =

* Initial release.

== Upgrade Notice ==

= 0.0.1 =

Initial release — no upgrade steps required.

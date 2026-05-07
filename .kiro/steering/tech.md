# Technology Stack

## Core Technologies

- **WordPress**: 6.5+ (Gutenberg block editor)
- **PHP**: 8.1+
- **JavaScript**: ES6+ (WordPress global `wp.*` objects)
- **Build Tool**: `@wordpress/scripts` (webpack-based)

## WordPress APIs Used

- Block API (register_block_type, dynamic rendering)
- Post Meta API (register_post_meta with REST support)
- Settings API (register_setting, add_settings_section, add_settings_field)
- RSS Feed API (add_feed, rss2_ns, rss2_head hooks)
- Media Library API (wp_enqueue_media, attachment handling)

## Project Structure

- **PHP**: Main plugin file, admin classes, includes (enclosure handler, feed template)
- **JavaScript**: Block editor scripts (src/), compiled to build/
- **No external dependencies**: Uses WordPress core libraries only

## Common Commands

```bash
# Development (watch mode with hot reload)
npm start

# Production build (minified)
npm run build

# Linting
npm run lint:js
npm run lint:css
```

## Build Output

- Source: `src/podcast-episode/`
- Compiled: `build/podcast-episode/`
- Block outputs: `index.js`, `index.css`, `index-rtl.css`, `view.js`, `block.json`

## WordPress Coding Standards

- Follow WordPress PHP Coding Standards
- Use WordPress escaping functions (esc_html, esc_attr, esc_url)
- Prefix all functions/classes with `podcast_blocks_` or `Podcast_Blocks_`
- Text domain: `podcast-blocks`

# Smart Description Formatting

The RSS `<description>` field is produced by the Smart Podcast Description feature. It formats the episode description optimized for podcast applications by only including the supported HTML tags.

The Smart Podcast Description feature uses the Apple Podcasts character limit and will return only valid HTML. HTML without a closing tag is trimmed.

## Processing pipeline

1. **Convert links** — `<a href="url">text</a>` is rewritten to `[text](url)` so hyperlinks survive in plain-text RSS readers. Apple podcasts will make these links clickable.
2. **Strip tags** —Removes every HTML tag except `<p>`, `<ol>`, `<ul>`, and `<li>`. Attributes are stripped from allowed tags.
3. **Truncate** — Caps the string at the configured character limit.
4. **Clean up incomplete tags** — Runs when the string was actually truncated (see table below).

## Character limits

| Context | Limit |
|---|---|
| Channel `<description>` | 4,000 characters |
| Item `<description>` | 10,000 characters |

## Truncation clean-up — scenario table

When the <description> exceeds the character limit, two passes run in sequence to remove any tag structure cut off mid-content.

| Scenario | Pass 1 result | Pass 2 result |
|---|---|---|
| `<p>para</p><p>cut off mid` | trims to `<p>para</p>` | no change |
| `<ul><li>ok</li><li>cut off` | trims to `<ul><li>ok</li>` | `<ul>` unclosed → removed, leaving `""` |
| `<p>para</p><ul><li>ok</li>` (missing `</ul>`) | no change (ends at `</li>`) | `<ul>` unclosed → trims to `<p>para</p>` |
| No closing tag found anywhere | sets `""` | no change |
| Content is under the limit | skipped entirely | skipped entirely |

# Smart Description Formatting

The RSS `<description>` field is produced by `podcast_blocks_format_description()` in `includes/podcast-feed.php`. It accepts raw HTML and a character limit, and returns clean, truncation-safe text.

## Processing pipeline

1. **Convert links** — `<a href="url">text</a>` is rewritten to `[text](url)` so hyperlinks survive in plain-text RSS readers.
2. **Strip tags** — `wp_kses` removes every HTML tag except `<p>`, `<ol>`, `<ul>`, and `<li>`. Attributes are stripped from allowed tags.
3. **Truncate** — `mb_substr` caps the string at the configured character limit.
4. **Clean up incomplete tags** — only runs when the string was actually truncated (see table below).

## Character limits

| Context | Limit |
|---|---|
| Channel `<description>` | 4,000 characters |
| Item `<description>` | 10,000 characters |

## Truncation clean-up — scenario table

When the string is exactly `$limit` characters long after `mb_substr`, two passes run in sequence to remove any tag structure cut off mid-content.

| Scenario after `mb_substr` | Pass 1 result | Pass 2 result |
|---|---|---|
| `<p>para</p><p>cut off mid` | trims to `<p>para</p>` | no change |
| `<ul><li>ok</li><li>cut off` | trims to `<ul><li>ok</li>` | `<ul>` unclosed → removed, leaving `""` |
| `<p>para</p><ul><li>ok</li>` (missing `</ul>`) | no change (ends at `</li>`) | `<ul>` unclosed → trims to `<p>para</p>` |
| No closing tag found anywhere | sets `""` | no change |
| Content is under the limit | skipped entirely | skipped entirely |

**Pass 1** uses a greedy regex (`/^(.*<\/(?:p|li|ol|ul)>)/su`) to find the last complete closing tag and discards everything after it. If no closing tag exists at all, the result is set to an empty string.

**Pass 2** compares the opening and closing counts for `<ol>` and `<ul>`. If any list container is still unclosed after pass 1 (its `</ol>` or `</ul>` was in the discarded tail), the entire list — from its opening tag to the end of the string — is removed.

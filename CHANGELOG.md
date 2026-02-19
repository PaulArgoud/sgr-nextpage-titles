# Changelog

All notable changes to the Multipage plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.5.15] - Unreleased

### Fixed

- **XSS**: subpage title now escaped with `esc_html()` in `mpp_the_content()` output
- **XSS**: translated strings in quicktag JS now escaped with `esc_js()` instead of raw `_e()`
- **PHP 8.2**: re-declared `$admin` property on `Multipage` class to prevent dynamic property deprecation warning
- `mpp_pre_handle_404()` returned `null` instead of `true`, failing to short-circuit the 404 handler
- `versions()` called `get_option( null, 'multipage' )` with swapped arguments — corrected to `get_option( 'multipage' )`
- `$comments_link` potentially undefined before use in `mpp_the_content()` — now initialized
- Admin CSS enqueued without version string, preventing cache busting — now uses `MPP_VERSION`
- `save_post` hook ran on revisions and autosaves — now filtered with `wp_is_post_revision()` / `wp_is_post_autosave()`
- `save_post()` could crash if `get_post()` returned `null` — added guard
- `mpp_the_title()` loaded the `wordpress-seo` text domain even when Yoast SEO was not active — now guarded with `defined( 'WPSEO_VERSION' )`
- Double slash in `hide_comments()` path (`MPP_PLUGIN_DIR . '/index.php'`)
- Navigation CSS class not sanitized — now uses `sanitize_html_class()`
- Missing text domain on `__( 'Next page' )`, `__( 'Previous page' )`, and `__( 'Comments' )` in `mpp-template.php`
- `_mpp_link_page_url()` could crash if `get_post()` returned `null` — added guard
- Duplicate `$handle` assignment in `MPP_Admin::enqueue_scripts()`
- Plugin URI used `http://` instead of `https://`
- `MPP_VERSION` constant was `'1.5.14'` while plugin header declared `1.5.15`
- Comments link in TOC was missing `<li>` wrapper, producing invalid HTML
- `multipage_return_array()` returned an empty array instead of `false` when no valid shortcodes were found
- "Build Multipage postmetas" only found shortcode posts — now also detects Gutenberg block posts
- Plugin action links detection used fragile `/trunk` path hack — now uses `plugin_basename()`
- **Security**: missing `exit` after `wp_safe_redirect()` in both settings save functions — PHP continued executing after redirect
- `esc_html()` used instead of `esc_attr()` on radio input `value` attributes in settings page
- Operator precedence ambiguity in `versions()` — `(int)` cast applied to condition instead of ternary result
- Incorrect `@since 1.6` docblock tags corrected to `@since 1.5` (version 1.6 does not exist)
- Copy-paste docblock errors: `mpp_hide_intro_title()`, `mpp_toc_only_on_the_first_page()`, and `mpp_hide_toc_header()` incorrectly described as "standard WordPress pagination"
- **SEO**: `rel=next` link on the first page pointed to itself instead of page 2 — normalized page index in `mpp_rel_links()`
- Comments TOC link for `first-page` / `last-page` was missing `#comments` URL fragment — `'comments'` was passed as `$rel` instead of `$p` parameter to `_mpp_link_page()`
- Swapped file headers: `admin-settings.php` said "Admin Advanced" and `admin-advanced-settings.php` said "Admin Settings"
- `mpp_get_comments_on_page()` docblock declared `@return int` but the function returns a `string`
- `mpp_the_content` filter docblock had wrong parameter names (`$toc_labels` instead of `$toc`) and types (`array` instead of `string`)
- `mpp_link_pages_args` filter docblock had `@since 3.0.0` copied from WordPress core — corrected to `@since 1.4`
- Typo « fitler » corrected to « filter » in `setup_actions()` comment
- `mpp_toc()` default position was `'top_right'` (underscore) instead of `'top-right'` (hyphen), producing an invalid CSS class
- `mpp_toc_pages_row` filter docblock documented 2 parameters but 3 are passed (`$row`, `$title`, `$i`)
- Typo « positon » corrected to « position » in `mpp_get_toc_position()` filter docblock
- `@param bool|string` corrected to `@param int` for priority option defaults in `mpp-options.php`
- Missing `$multipage` parameter documented in `mpp_link_pages()` and `mpp_toc()` docblocks
- Redundant `$post &&` check removed in `mpp_pre_handle_404()` — already validated above
- **JS TypeError**: cancelling the quicktag subpage prompt caused `null.length` crash — now checks for `null` explicitly
- **CSS handle**: `enqueue_styles()` used full filesystem path (`get_stylesheet_directory()`) as handle identifier — replaced with `get_stylesheet()` / `get_template()` slug
- `mpp_get_admin_tabs()` docblock declared `@return string` but the function returns an `array`
- `mpp_get_db_version()` and `mpp_get_db_version_raw()` docblocks declared `@return string` but the functions return an `int`
- Self-referencing `@uses enqueue_styles()` tag removed from `enqueue_styles()` docblock
- `_mpp_link_page_url()` docblock `@param string $p Paragraph id` corrected to « Fragment identifier »
- `in_array()` missing strict mode (`true`) in `mpp_modify_admin_menu_highlight()`
- `mpp_toc()` docblock for `$pagelink` incorrectly described as "Link text for the previous page link" — corrected to "Page number template; `%` is replaced by the page number"
- `mpp_link_pages()` docblock declared wrong defaults: `$before` = `<p> Pages:` / `$after` = `</p>` / `$echo` = `1|true` — corrected to `<div>` / `</div>` / `0|false`
- `mpp_toc()` docblock declared `$echo` default as `1|true` — corrected to `0|false`
- `mpp_toc()` docblock for `$comments` declared `@type int|bool` but actually receives an opening `<a>` tag or `0` — corrected to `string|int`
- Unused `global $numpages` declaration removed from `mpp_toc()`
- Dead JavaScript variable `shortcode` removed from quicktag `prompt_subtitle()` function
- **Security**: missing `current_user_can( 'manage_options' )` check in `mpp_admin_settings_save()` and `mpp_admin_advanced_settings_save()` — added for defense-in-depth

### Added

- Docblock for `_mpp_link_page()` helper function in `mpp-template.php`
- `uninstall.php` to clean up all plugin options and `_mpp_data` post meta on deletion
- `Requires at least: 5.0` and `Requires PHP: 7.4` headers in plugin file
- History section in `README.md` documenting the plugin's origins and fork lineage
- `aria-label` attributes on TOC and navigation `<nav>` elements for accessibility

### Changed

- `_mpp_link_page_url()` called `get_permalink()` up to 3 times — now stored in a local variable
- `$this->version` now reads from `MPP_VERSION` constant instead of a hardcoded string
- All `require()` calls replaced with `require_once` (without parentheses) in `class-mpp.php` and `class-mpp-admin.php`
- `sanitize_html_class()` applied to TOC container position class
- `mpp_is_block_editor_active()` simplified — removed unnecessary WP < 5.0 and Gutenberg plugin checks
- TinyMCE disable setting description updated (no longer references WordPress 3.9)
- `mpp_get_continue_or_prev_next()` called once and stored instead of twice
- `mpp_get_toc_position()` called once and stored instead of three times in `mpp_the_content()`
- `mpp_get_comments_on_page()` called once and stored instead of twice in `mpp_post()`
- `constant('MPP_PLUGIN_DIR')` / `constant('MPP_PLUGIN_URL')` replaced with direct constant access in `setup_globals()`
- `strip_tags()` string allowlist updated to array form (recommended since PHP 7.4) in `Multipage_Parser`
- Redundant `trailingslashit()` removed from `setup_globals()` — `plugin_dir_path()` / `plugin_dir_url()` already add a trailing slash
- Redundant `is_null( $post ) || empty( $post )` simplified to `empty( $post )` in `mpp_post()`
- `json_encode()` replaced with `wp_json_encode()` in TinyMCE `languages.php`
- Minor formatting: added missing space in `$this->page - 1` arithmetic expression
- Removed trailing semicolons (`};`) after ABSPATH guard closing braces in `class-mpp.php` and `mpp-update.php`
- Gutenberg block description replaced (was "lorem ipsum" placeholder)
- Gutenberg block script dependency changed from deprecated `wp-editor` to `wp-block-editor`
- Frontend CSS header updated to current author and HTTPS Plugin URI

### Removed

- Obsolete WP < 4.4 title fallback in `mpp_the_title()`
- Orphaned `<input type="checkbox">` and commented-out toggle HTML in TOC template
- Dead `mpp_admin_settings_callback_excerpt_on_all_pages()` (called non-existent `mpp_excerpt_all_pages()`)
- Dead `mpp_admin_settings_callback_prettylinks()` (referenced old `envire.it` URL)
- Unused `$options` property from `Multipage` class
- Dead widget files: `class-mpp-table-of-contents-widget.php` and `mpp-widgets.php` (never loaded, non-functional)
- Dead `mpp_uninstall()` function in `mpp-update.php` (replaced by `uninstall.php`)
- Orphaned `.toctogglespan` CSS rule (toggle was removed from PHP)
- Dead `mpp_get_default_options()` function (never called, option keys used underscores instead of hyphens)
- Unused `MPP__MINIMUM_WP_VERSION` constant (minimum WP version already declared in plugin header)
- Redundant `MPP__PLUGIN_DIR` constant (same value as `MPP_PLUGIN_DIR`, only used once — inlined)

## [1.5.14] - 2026-02-19

### Fixed

- Uninitialized `$custom_intro` variable causing PHP 8.4 warnings (`class-mpp.php`)
- Array bounds check missing for `$_mpp_page_keys` access in `mpp_pre_handle_404()`
- Ambiguous operator precedence in comments display condition (added explicit parentheses)
- Integer settings sanitized with `sanitize_text_field()` instead of `intval()` in advanced settings
- Missing `esc_attr()` on `<option>` values in admin settings forms (4 occurrences)
- Options cache never invalidated after saving — `mpp_clear_options_cache()` now resets the static cache
- Deprecated `current_time('timestamp')` replaced with `time()` in `mpp-update.php` and `admin-advanced-settings.php`
- Loose `==` / `!=` comparisons replaced with `===` / `!==` across all PHP files
- `array_search()` without `false` check in TinyMCE button insertion (`class-mpp-admin.php`)
- Operator precedence ambiguity in TOC active page condition (`mpp-template.php`)
- Preview link parameters now properly sanitized with `intval()` / `sanitize_text_field()` (`mpp-template.php`)
- Incorrect `@since 1.9.0` docblock tags corrected to `1.4`

### Added

- `LICENSE` file (GPL v3 full text)
- `CONTRIBUTING.md` with coding standards, project structure, and contribution guidelines
- `ARCHITECTURE.md` describing the plugin file structure and data flow

### Changed

- Replaced `&$this` with `$this` in all callback arrays (unnecessary since PHP 5.4)
- Updated Codex link to `developer.wordpress.org` in advanced settings
- Extracted content parser into `Multipage_Parser` class (`inc/mpp-parser.php`) from the main `Multipage` class

### Removed

- Dead debug code in table of contents widget
- Unused `admin-premium.php` page placeholder
- Dead `is_gutenberg_page()` method in `class-mpp.php` (replaced by `mpp_is_block_editor_active()`)

## [1.5.13] - 2026-02-19

### Fixed

- Quotes in subpage titles (e.g. `"F48"`) breaking page parsing — titles are now extracted from Gutenberg block JSON attributes first, with shortcode fallback for the classic editor; the block save function also escapes `"` as `&quot;` in shortcode output
- PHP 8.2+ deprecated dynamic property warnings — all class properties are now explicitly declared
- Catastrophic backtracking in regex pattern `MPP_GUTENBERG_PATTERN` (`(.|\s)*?` replaced with `[\s\S]*?`)
- Page 2+ not accessible due to WordPress `redirect_canonical` running before the plugin injects `<!--nextpage-->` markers
- Improperly quoted `rel` attribute in navigation links (`rel=next` → `rel="next"`)
- "Advaced" typo in admin submenu registration

### Added

- SEO `rel=prev` / `rel=next` links in `<head>` for multipage posts
- `sanitize_text_field` callbacks on all `register_setting()` calls
- `sanitize_text_field()` / `wp_unslash()` on all `$_POST` values in settings save functions
- `wp_kses_post()` escaping for subpage titles in TOC and navigation output
- In-memory options caching (`mpp_get_option()`) to reduce repeated `get_option()` database queries
- Strict mode on all `in_array()` calls in update routines
- `README.md` with badges, requirements, usage documentation, and filters reference
- `CHANGELOG.md` following [Keep a Changelog](https://keepachangelog.com/) format

### Changed

- Replaced `$_REQUEST` with explicit `$_GET` / `$_POST` checks in `mpp_is_deactivation()`
- Updated minimum WordPress version from 3.9 to 5.0
- Tested with PHP 8.4
- Regenerated `.pot` translation template (73 strings, was outdated since v1.3)
- Default branch renamed from `master` to `main`

### Removed

- Dead code: commented-out widget/premium requires, unused premium menu items, tabs, and settings
- Italian comments and obsolete TODO annotations
- Closing `?>` tag in `admin-functions.php`
- `readme.txt` (replaced by `README.md`)
- Obsolete "To Do" sections from old readme

## [1.5.12] - 2021-06-16

### Changed

- Compatibility check

## [1.5.11] - 2021-02-16

### Fixed

- Gutenberg: setting a custom subtitle on the first page could produce an empty first page due to bad behavior when Classic Editor was enabled

## [1.5.10] - 2021-02-16

### Added

- Gutenberg subpage block with updated syntax

### Fixed

- Gutenberg: setting a custom subtitle on the first page could produce an empty first page in some conditions

## [1.5.9] - 2021-02-14

### Fixed

- Gutenberg: setting a custom subtitle on the first page could produce an empty first page in some conditions

## [1.5.8] - 2020-08-22

### Changed

- Minor change to the "Continue" option layout

## [1.5.7] - 2020-06-21

### Fixed

- Non-working case with the Multipage legacy shortcode introduced in 1.5.6

## [1.5.6] - 2020-07-30

### Changed

- Compatibility with WordPress 5.5
- Minor CSS enhancements

## [1.5.5] - 2020-06-21

### Fixed

- Function call error happening when updating Multipage

## [1.5.4] - 2020-04-25

### Added

- `mpp_the_content` filter to customize the multipage content output

## [1.5.3] - 2020-04-22

### Fixed

- Added missing JS file for the block editor

## [1.5.2] - 2020-03-09

### Changed

- Fully tested on WordPress 5.4
- Removed an `<h1>` tag inside the post navigation links

## [1.5.1] - 2019-12-13

### Fixed

- Removed the "hide" link on the table of contents
- Added missing files for the block editor

## [1.5.0] - 2019-12-12

### Added

- Block editor support: new "Subpage" Gutenberg block

### Changed

- Fully tested on WordPress 5.3 and PHP 7.4
- Minor enhancements

## [1.4.4] - 2019-05-15

### Changed

- Fully tested on WordPress 4.1

## [1.4.3] - 2019-01-17

### Fixed

- Multipage on posts with `future` and `private` status

## [1.4.2] - 2019-01-09

### Fixed

- Post preview on first-time saved drafts

### Changed

- Renamed "Multipage Plugin" to "Multipage"

## [1.4.1] - 2018-12-09

### Fixed

- CSS fix for TOC top and bottom on mobile devices

## [1.4.0] - 2018-11-18

> **Note:** Major release — please make sure to have a full website backup before updating.

### Added

- New settings pages with new options
- Two navigation types: "Continue" and "Next/Previous"

### Changed

- Completely rewritten codebase
- Processing speed improvements
- Fully tested on WordPress 4.9.8
- Default TOC position changed (upgrades keep old default)

### Fixed

- Document title now reports the subpage title on WordPress > 4.4

## [1.3.6] - 2017-01-02

### Changed

- Fully tested on WordPress 4.7

## [1.3.5] - 2016-09-17

### Changed

- Fully tested on WordPress 4.6

## [1.3.4] - 2016-04-10

### Added

- New setting: unhide the default WordPress pagination

## [1.3.3] - 2016-04-05

### Added

- Advanced setting: disable TinyMCE button (for WordPress < 3.9 compatibility)
- Advanced settings for title and content rewrite priority
- Settings link on the plugins page

## [1.3.2] - 2015-12-08

### Added

- New languages

## [1.3.1] - 2015-11-19

### Changed

- Fully tested on WordPress 4.4
- Updated minimum requirement to WordPress 3.9

## [1.3.0] - 2015-05-17

### Added

- "Subpage" button in the WordPress visual & HTML editors

### Changed

- Text domain updated to match plugin slug `sgr-nextpage-titles`

## [1.2.4] - 2015-05-04

### Changed

- Fully tested on WordPress 4.2

### Fixed

- Compatibility with latest WordPress SEO versions

## [1.2.3]

### Changed

- Fully tested on WordPress 4.1

### Fixed

- `multipage_subtitle`, `multipage_navigation`, `multipage_content` filters not working

## [1.2.2]

### Fixed

- Overflow pages now redirect to the first page, consistent with default WordPress behavior

## [1.2.1]

### Added

- `rel` attributes on navigation links

### Changed

- Minor CSS changes
- New WordPress 4.0 plugin icons and banner

### Fixed

- Check existence of `$post` variable that could generate errors in some conditions

## [1.2.0]

### Added

- `toc` option on the nextpage shortcode to auto-scroll to the table of contents

## [1.1.4]

### Changed

- Tag title now works with WordPress SEO by Yoast (`%%page%%` variable)

## [1.1.3]

### Fixed

- Tag title on non-English WordPress installations

## [1.1.2]

### Fixed

- Settings page

## [1.1.1]

### Fixed

- Incompatibility with servers running PHP < 5.3

## [1.1.0]

### Added

- Tag title now reports the subpage title instead of the page number
- Three new filters: `multipage_subtitle`, `multipage_navigation`, `multipage_content`

### Changed

- Renamed to "Multipage" (CSS filenames changed to `multipage.css` / `multipage.min.css`)

## [1.0.1]

### Fixed

- Load default values even if settings were never saved

## [1.0.0]

### Added

- Option to hide comments on all subpages except the first
- TOC options: hide header, comments link, first page only, label choices, position, visibility

### Changed

- Completely rewritten core code for improved performance
- Settings menu renamed to "Multipage"

### Fixed

- Multipage posts display correctly on non-`is_single()` pages without `<!--more-->`

## [0.94]

### Fixed

- 404 error on the last page when there is no intro title

## [0.93]

### Added

- Settings page under "Settings" for summary appearance options

## [0.92]

### Fixed

- Returns 404 error if the requested page number doesn't exist

## [0.91]

### Added

- German language support

### Changed

- Tested with WordPress 3.8

## [0.90]

### Added

- RTL support
- Custom CSS auto-load from theme directory

### Fixed

- Page navigation in previews

## [0.85]

### Fixed

- Permalink bug with structures without a trailing slash

## [0.82]

### Fixed

- Removed a deprecated function that generated errors in some conditions

## [0.8]

### Added

- Initial code for configuration page (not active yet)

### Fixed

- 404 error caused by pretty URLs — now uses native page numbers

## [0.7]

### Added

- Custom intro title via nextpage shortcode on the first line

### Fixed

- Summary incorrectly displayed on loop pages

## [0.6]

### Changed

- Almost completely rewritten to use WordPress core nextpage functionality
- Subpage URLs now use `subpage-` prefix (due to attachment page conflicts)
- Works with all post types (pretty URLs on `post` only)

### Fixed

- Many bug fixes

## [0.55]

### Fixed

- Empty page no longer returns notices in debug mode
- Blocked usage on non-post post types (permalinks not yet supported)

## [0.50]

### Added

- Summary linked as a page
- `rel=next` / `rel=prev` links in `<head>`
- Previous/next page links at the bottom

### Changed

- Rewritten to request parts via page number

### Fixed

- Incorrect page title now returns 404

## [0.38]

### Added

- Previous/next page links at the bottom
- Language file support and Italian translation

## [0.30]

### Added

- Subpage title now part of the page title (head & HTML)
- Translation loading code

### Changed

- No longer needed to flush rewrite rules after activation

## [0.22]

- Initial beta release
# Changelog

All notable changes to the Multipage plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

### Changed

- Replaced `$_REQUEST` with explicit `$_GET` / `$_POST` checks in `mpp_is_deactivation()`
- Updated minimum WordPress version from 3.9 to 5.0
- Tested with PHP 8.4

### Removed

- Dead code: commented-out widget/premium requires, unused premium menu items, tabs, and settings
- Italian comments and obsolete TODO annotations
- Closing `?>` tag in `admin-functions.php`

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

# Multipage for WordPress

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.5.14-blue.svg)](https://github.com/PaulArgoud/sgr-nextpage-titles/releases/tag/1.5.14)
[![License: GPL v3](https://img.shields.io/badge/license-GPLv3-green.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-0073aa.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)](https://www.php.net/)
[![Tested up to](https://img.shields.io/badge/tested%20up%20to-WordPress%206.7-0073aa.svg)](https://wordpress.org/)

Split your WordPress posts into multiple subpages, each with its own title and a table of contents.

## Features

- **Subpage block** for the Gutenberg block editor
- **`[nextpage title="..."]` shortcode** for the classic editor
- **Table of contents** with configurable position, labels, and header visibility
- **SEO-friendly**: subpage titles in `<title>`, `rel=prev`/`rel=next` links in `<head>`
- **Navigation**: "Continue" or "Previous/Next" navigation between subpages
- **Custom CSS**: override styles from your theme by placing a `multipage.css` in your theme's `/css/` directory
- **RTL support**
- **Internationalized**: translatable via `.pot` file (German, Italian, Spanish, French included)

## Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| WordPress   | 5.0     | 6.x        |
| PHP         | 7.4     | 8.2+       |

Tested with PHP 8.4 and WordPress 6.7.

## Installation

### From GitHub

1. Download the [latest release](https://github.com/PaulArgoud/sgr-nextpage-titles/releases/latest)
2. Upload the `sgr-nextpage-titles` folder to `/wp-content/plugins/`
3. Activate **Multipage** through the **Plugins** menu in WordPress
4. Configure the plugin under **Settings > Multipage**

### From WordPress.org

Search for **Multipage** in **Plugins > Add New** and click **Install Now**.

## Usage

### Block editor (Gutenberg)

Add a **Subpage** block to mark the beginning of a new subpage. Each block has a title field.

### Classic editor

Insert the shortcode anywhere in your post content:

```
[nextpage title="My subpage title"]
```

The content before the first shortcode becomes the intro page (titled "Intro" by default).

## Settings

Navigate to **Settings > Multipage** to configure:

| Setting | Description |
|---------|-------------|
| **Intro title** | Show or hide the default intro title |
| **Comments** | Display comments on all pages, first page only, or last page only |
| **Navigation** | Choose between "Continue/Back" or "Previous/Next" navigation |
| **Standard pagination** | Show or hide the default WordPress page numbers |
| **Table of contents** | Position (top-left, top-right, top, bottom, hidden), row labels, header visibility |
| **Comments link** | Add a link to comments inside the table of contents |

Advanced settings are available under the **Advanced** tab (title/content rewrite priority, TinyMCE buttons).

## Custom CSS

Multipage loads a minimal stylesheet. To override it, create one of these files in your theme:

- `css/multipage.css` (or `css/multipage.min.css`)
- `css/multipage-rtl.css` for RTL layouts

The plugin checks the child theme first, then the parent theme, then falls back to its own styles.

## Filters & Hooks

| Filter | Description |
|--------|-------------|
| `mpp_the_content` | Modify the full multipage content output |
| `mpp_page_title_template` | Change the subpage title HTML template (default: `<h2>%s</h2>`) |
| `mpp_link_pages` | Modify the navigation links HTML |
| `mpp_toc` | Modify the table of contents HTML |
| `mpp_toc_pages_row` | Modify individual TOC row HTML |
| `mpp_link_pages_args` | Filter navigation link arguments |
| `mpp_toc_args` | Filter TOC arguments |
| `mpp_get_admin_tabs` | Add or remove admin settings tabs |

## Languages

Translation files are located in `/languages/`. Available languages:

- English (default)
- German (de_DE)
- Spanish (es_ES)
- French (fr_FR)
- Italian (it_IT)

To contribute a translation, use the `.pot` file as a template.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed history of changes.

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

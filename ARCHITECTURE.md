# Architecture

This document describes the internal structure and data flow of the Multipage plugin.

## File Tree

```
sgr-nextpage-titles/
│
├── sgr-nextpage-titles.php          # Plugin entry point: defines constants, loads class-mpp.php
├── class-mpp.php                    # Main Multipage class (singleton): bootstrap,
│                                    #   frontend rendering, SEO, redirects
│
├── classes/
│   ├── class-mpp-admin.php          # MPP_Admin: admin menus, settings registration,
│   │                                #   TinyMCE integration, Gutenberg block registration
│   ├── class-mpp-shortcodes.php     # Multipage_Plugin_Shortcodes: registers [nextpage] shortcode
│   └── class-mpp-table-of-contents-widget.php  # MPP_Table_of_Contents_Widget (WP_Widget)
│
├── inc/
│   ├── mpp-admin.php                # Bootstraps MPP_Admin (creates the instance)
│   ├── mpp-functions.php            # Version getters, text domain loading
│   ├── mpp-options.php              # Option getters with in-memory caching, default values
│   ├── mpp-parser.php              # Multipage_Parser: content parsing (Gutenberg + shortcodes)
│   ├── mpp-shortcodes.php           # Initializes Multipage_Plugin_Shortcodes
│   ├── mpp-template.php             # Navigation links (mpp_link_pages) and TOC (mpp_toc) rendering
│   ├── mpp-update.php               # Version migration (1.3 → 1.4), activation/deactivation hooks
│   ├── mpp-widgets.php              # Widget registration (currently disabled)
│   │
│   └── admin/
│       ├── admin-actions.php        # Hooks admin_init → mpp_admin_init → settings registration
│       ├── admin-functions.php      # Admin URL helpers, tab navigation, block editor detection
│       ├── admin-settings.php       # Main settings page: UI callbacks and save function
│       ├── admin-advanced-settings.php  # Advanced settings page: UI callbacks and save function
│       └── tinymce/
│           ├── languages.php        # TinyMCE i18n strings
│           ├── js/plugin[.min].js   # TinyMCE "Subpage" button plugin
│           ├── css/multipage[.min].css  # TinyMCE editor styles
│           └── images/              # TinyMCE button icons
│
├── admin/
│   ├── js/mpp-editor-blocks.js      # Gutenberg "Subpage" block (registerBlockType)
│   └── css/mpp-editor-blocks[.min].css  # Gutenberg block editor styles
│
├── css/
│   ├── multipage.css                # Frontend styles
│   └── multipage.min.css            # Minified frontend styles
│
└── languages/
    ├── sgr-nextpage-titles.pot      # Translation template (73 strings)
    └── sgr-nextpage-titles-{locale}.mo  # Compiled translations (de, es, fr, it)
```

## Boot Sequence

```
1. WordPress loads sgr-nextpage-titles.php
   │
   ├── Defines: MPP_VERSION, MPP__MINIMUM_WP_VERSION, MPP__PLUGIN_DIR
   ├── require class-mpp.php
   └── Calls multipage() → Multipage::instance()
       │
       ├── constants()       → Defines MPP_PLUGIN_DIR, MPP_PLUGIN_URL, MPP_PATTERN, MPP_GUTENBERG_PATTERN
       ├── setup_globals()   → Sets $this->version, paths, etc.
       ├── includes()        → Loads all inc/ and classes/ files, runs versions()
       │   └── if is_admin() → schedules mpp_admin() on 'init' hook
       ├── setup_actions()   → Registers save_post, pre_handle_404, redirect_canonical
       └── frontend_init()   → Hooks mpp_post() on 'wp' action
```

## Request Lifecycle (Frontend)

### 1. Early: `pre_handle_404` (priority 100)

`Multipage::mpp_pre_handle_404()` runs before WordPress decides if a page is a 404.

- Reads `_mpp_data` post meta (an associative array of `slug => title`)
- Determines the current page index from `$wp_query->query_vars['page']`
- Sets `$this->mpp_data`, `$this->page`, `$this->mpp_index`, `$this->mpp_pagename`
- If the page index is out of bounds, returns `$preempt` (let WP handle 404)

### 2. Early: `redirect_canonical` (priority 10)

`Multipage::mpp_redirect_canonical()` prevents WordPress from redirecting valid multipage URLs back to the base permalink. WordPress checks `<!--nextpage-->` markers in raw `post_content`, but the plugin injects them dynamically later.

### 3. Main: `wp` action

`Multipage::mpp_post()` is the core frontend method:

```
mpp_post()
├── Replaces %%intro%% placeholder with localized "Intro" title
├── Conditionally hides standard WP pagination
├── Conditionally hides comments (based on first-page / last-page setting)
├── Rewrites post_content:
│   ├── Replaces Gutenberg <!-- wp:multipage/subpage --> blocks with <!--nextpage-->
│   └── Replaces [nextpage] shortcodes with <!--nextpage-->
├── Updates $post->post_content and $wp_query->post
├── Registers filters:
│   ├── wp_title / pre_get_document_title / document_title_parts → mpp_the_title()
│   ├── the_content → mpp_the_content()
│   ├── wp_enqueue_scripts → enqueue_styles()
│   └── wp_head → mpp_rel_links()
```

### 4. Rendering: `the_content` filter

`Multipage::mpp_the_content()` builds the final output:

```
mpp_the_content($content)
├── Builds page title (via mpp_page_title_template filter)
├── Builds navigation links (mpp_link_pages() from mpp-template.php)
├── Builds table of contents (mpp_toc() from mpp-template.php)
├── Assembles: title + TOC + content + navigation
└── Applies mpp_the_content filter
```

### 5. SEO: `wp_head` action

`Multipage::mpp_rel_links()` outputs `<link rel="prev">` and `<link rel="next">` in `<head>`.

## Content Parsing

`Multipage_Parser::multipage_return_array($content)` (in `inc/mpp-parser.php`) extracts subpage data from post content. It returns an associative array (`slug => title`) or `false`.

```
Multipage_Parser::multipage_return_array($content)
│
├── Try Gutenberg first:
│   ├── Regex: /<!-- wp:multipage\/subpage\s+(\{.*?\})\s*-->/s
│   ├── json_decode() each match to extract title and slug
│   ├── If content exists before the first block → add "intro" entry
│   └── Return array if non-empty
│
└── Fallback to shortcode:
    ├── Strip HTML tags, remove HTML comments
    ├── Regex: MPP_PATTERN = /\[nextpage[^\]]*\]/
    ├── shortcode_parse_atts() on each match
    ├── If content exists before the first shortcode → add "intro" entry
    └── Return array
```

This array is stored as `_mpp_data` post meta on every `save_post`.

## Data Model

### Post Meta

| Key | Type | Description |
|-----|------|-------------|
| `_mpp_data` | `array` | Associative array of `slug => title` for all subpages |

### Options (wp_options)

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `mpp-hide-intro-title` | bool | `false` | Hide the default "Intro" title |
| `mpp-comments-on-page` | string | `all` | Where to show comments: `all`, `first-page`, `last-page` |
| `mpp-continue-or-prev-next` | string | `continue` | Navigation type: `continue`, `next-previous`, `hidden` |
| `mpp-disable-standard-pagination` | bool | `true` | Hide default WP page numbers |
| `mpp-toc-only-on-the-first-page` | bool | `false` | Show TOC only on page 1 |
| `mpp-toc-position` | string | `top-right` | TOC position: `top-left`, `top-right`, `top`, `bottom`, `hidden` |
| `mpp-toc-row-labels` | string | `number` | Row labels: `number`, `page`, `hidden` |
| `mpp-hide-toc-header` | bool | `false` | Hide "Contents" header |
| `mpp-comments-toc-link` | bool | `false` | Show comments link in TOC |
| `_mpp-rewrite-title-priority` | int | `20` | Filter priority for title rewrite |
| `_mpp-rewrite-content-priority` | int | `20` | Filter priority for content rewrite |
| `mpp-disable-tinymce-buttons` | bool | `false` | Disable TinyMCE subpage button |

All options are accessed through `mpp_get_option()` which provides in-memory caching. Cache is cleared via `mpp_clear_options_cache()` after settings are saved.

## Filters and Actions

### Content Filters

| Filter | Location | Description |
|--------|----------|-------------|
| `mpp_the_content` | `class-mpp.php` | Final multipage output (title + TOC + content) |
| `mpp_page_title_template` | `class-mpp.php` | Subpage title HTML template (default: `<h2>%s</h2>`) |
| `mpp_link_pages` | `mpp-template.php` | Navigation links HTML |
| `mpp_link_pages_args` | `mpp-template.php` | Navigation link arguments |
| `mpp_link_pages_link` | `mpp-template.php` | Individual navigation link |
| `mpp_toc` | `mpp-template.php` | Table of contents HTML |
| `mpp_toc_args` | `mpp-template.php` | TOC arguments |
| `mpp_toc_pages_row` | `mpp-template.php` | Individual TOC row HTML |

### Admin Filters

| Filter | Location | Description |
|--------|----------|-------------|
| `mpp_get_admin_tabs` | `admin-functions.php` | Add or remove admin settings tabs |
| `mpp_get_default_options` | `mpp-options.php` | Default option values |

## Editors Integration

### Gutenberg (Block Editor)

The "Subpage" block is registered in `admin/js/mpp-editor-blocks.js`:
- Block name: `multipage/subpage`
- Attributes: `title` (string), `slug` (string)
- Saved as HTML comment: `<!-- wp:multipage/subpage {"title":"..."} -->` with a `[nextpage]` shortcode inside for backward compatibility

### Classic Editor (TinyMCE)

- Button added to TinyMCE toolbar via `mce_buttons` filter
- Plugin JS: `inc/admin/tinymce/js/plugin[.min].js`
- HTML editor quicktag also available

### Shortcode

`[nextpage title="My Title" slug="my-slug"]` is registered via `Multipage_Plugin_Shortcodes`. At runtime, it renders as `<!--nextpage-->` (WordPress core handles the page splitting).

## CSS Loading

The plugin checks for theme overrides in this order:
1. Child theme: `css/multipage[.min].css`
2. Parent theme: `css/multipage[.min].css`
3. Plugin default: `css/multipage[.min].css`

RTL layouts use `multipage-rtl[.min].css` instead.

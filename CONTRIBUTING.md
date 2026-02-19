# Contributing to Multipage

Thank you for your interest in contributing to the Multipage plugin! This document provides guidelines and instructions for contributing.

## Requirements

| Tool       | Version          |
|------------|------------------|
| WordPress  | 5.0+             |
| PHP        | 7.4+ (8.2+ recommended) |
| Node.js    | 14+ (for Gutenberg block development) |

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/<your-username>/sgr-nextpage-titles.git
   ```
3. Set up a local WordPress development environment (e.g. [wp-env](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/), [Local](https://localwp.com/), or [DDEV](https://ddev.com/))
4. Symlink or copy the plugin folder into `wp-content/plugins/`
5. Activate **Multipage** in the WordPress admin

## Project Structure

```
sgr-nextpage-titles/
  class-mpp.php                 # Main plugin class (core logic)
  sgr-nextpage-titles.php       # Plugin entry point
  classes/
    class-mpp-admin.php         # Admin settings registration
    class-mpp-shortcodes.php    # Shortcode handling
    class-mpp-table-of-contents-widget.php
  inc/
    mpp-options.php             # Option getters with caching
    mpp-parser.php              # Content parsing (Gutenberg + shortcodes)
    mpp-template.php            # Navigation & TOC rendering
    mpp-update.php              # Version migration routines
    mpp-functions.php           # Helper functions
    admin/                      # Admin pages & settings UI
  admin/js/
    mpp-editor-blocks.js        # Gutenberg "Subpage" block
  languages/                    # Translation files (.pot, .mo)
  css/                          # Frontend stylesheets
```

## Coding Standards

- Follow the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/) for PHP
- Use strict comparisons (`===` / `!==`) instead of loose ones (`==` / `!=`)
- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- Sanitize all input: `sanitize_text_field()`, `intval()`, `wp_unslash()`
- Use `$this` (not `&$this`) in callback arrays
- Avoid deprecated WordPress functions (e.g. use `time()` instead of `current_time('timestamp')`)

## Making Changes

1. Create a feature branch from `main`:
   ```bash
   git checkout -b fix/short-description
   ```
2. Make your changes, keeping commits focused and atomic
3. Test your changes on a local WordPress installation
4. Verify compatibility with PHP 7.4 and PHP 8.4
5. Update `CHANGELOG.md` under the current `[Unreleased]` or version section

## Translations

- The translation template is at `languages/sgr-nextpage-titles.pot`
- To regenerate the `.pot` file after adding or modifying translatable strings:
  ```bash
  xgettext --language=PHP --keyword=__ --keyword=_e --keyword=_x:1,2c \
    --keyword=_n:1,2 --keyword=esc_html__ --keyword=esc_html_e \
    --keyword=esc_attr__ --keyword=esc_attr_e \
    --from-code=UTF-8 --sort-by-file \
    --package-name="Multipage" \
    --msgid-bugs-address="https://github.com/PaulArgoud/sgr-nextpage-titles/issues" \
    -o languages/sgr-nextpage-titles.pot \
    *.php classes/*.php inc/*.php inc/admin/*.php inc/admin/tinymce/*.php
  ```
- Use [Poedit](https://poedit.net/) or a similar tool to create `.po` / `.mo` files from the template
- Text domain: `sgr-nextpage-titles`

## Submitting a Pull Request

1. Push your branch to your fork
2. Open a pull request against the `main` branch
3. Provide a clear description of the changes and the problem they solve
4. Reference any related issues (e.g. `Fixes #42`)

## Reporting Issues

- Use [GitHub Issues](https://github.com/PaulArgoud/sgr-nextpage-titles/issues)
- Include your WordPress version, PHP version, and any relevant error messages
- Describe the steps to reproduce the problem

## License

By contributing, you agree that your contributions will be licensed under the [GNU General Public License v3.0](LICENSE).

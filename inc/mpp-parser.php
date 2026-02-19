<?php
/**
 * Multipage Content Parser.
 *
 * Extracts subpage structure (slugs and titles) from post content,
 * supporting both Gutenberg blocks and classic editor shortcodes.
 *
 * @package Multipage
 * @subpackage Parser
 * @since 1.5.14
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Multipage_Parser {

	/**
	 * Parse post content and return an associative array of subpage slugs to titles.
	 *
	 * Tries Gutenberg block JSON attributes first, then falls back to
	 * [nextpage] shortcode parsing for classic editor content.
	 *
	 * @since 1.5
	 *
	 * @param string $content The post content.
	 * @return array|false Associative array of slug => title, or false if not a multipage post.
	 */
	public static function multipage_return_array( $content ) {
		// Initialize the array
		$_multipage = array();

		// Try Gutenberg block JSON attributes first (handles quotes in titles correctly).
		if ( preg_match_all( '/<!-- wp:multipage\/subpage\s+(\{.*?\})\s*-->/s', $content, $block_matches ) ) {
			// Check if there is content before the first block (intro page).
			$first_block_pos = strpos( $content, $block_matches[0][0] );
			$content_before = trim( strip_tags( substr( $content, 0, $first_block_pos ) ) );
			// Remove any remaining HTML comments from the intro check.
			$content_before = trim( preg_replace( '/<!--.*?-->/', '', $content_before ) );

			if ( ! empty( $content_before ) ) {
				$_multipage['intro'] = '%%intro%%';
			}

			foreach ( $block_matches[1] as $json_str ) {
				$attrs = json_decode( $json_str, true );
				if ( $attrs && isset( $attrs['title'] ) ) {
					$title = $attrs['title'];
					$subpage_slug = $subpage_slug_temp = isset( $attrs['slug'] ) ? sanitize_title( $attrs['slug'] ) : sanitize_title( $title );
					for ( $i = 1; array_key_exists( $subpage_slug, $_multipage ) && $i < 100; $i++ ) {
						$subpage_slug = $subpage_slug_temp . '-' . $i;
					}
					$_multipage[ $subpage_slug ] = $title;
				}
			}

			if ( ! empty( $_multipage ) ) {
				return $_multipage;
			}
		}

		// Fall back to shortcode parsing for classic editor content.
		$content_temp = self::multipage_clean_content( $content );

		$matches = self::parse_nextpage_shortcode( $content_temp );
		foreach ( $matches[0] as $key=>$match ) {
			$atts = shortcode_parse_atts( str_replace( array( '[', ']' ), '', $match ) );
			if ( ! array_key_exists( 'title', $atts ) )
				continue;

			// Check if the intro has a Title
			if ( 0 === count( $_multipage ) && 0 !== strpos( $content_temp, $match ) )
				$_multipage['intro'] = '%%intro%%';

			$subpage_slug = $subpage_slug_temp = isset( $atts["slug"] ) ? sanitize_title( $atts["slug"] ) : sanitize_title( $atts["title"] );
			for ( $i = 1; array_key_exists ( $subpage_slug, $_multipage ) && $i < 100; $i++ ) {
				$subpage_slug = $subpage_slug_temp . '-' . $i;
			}
			$_multipage[ $subpage_slug ] = $atts["title"];
		}

		if ( isset( $_multipage ) && is_array( $_multipage ) )
			return $_multipage;

		return false;
	}

	/**
	 * Clean post content for shortcode parsing.
	 *
	 * Strips HTML tags (except inline formatting) and removes HTML comments
	 * to expose raw [nextpage] shortcodes.
	 *
	 * @since 1.4
	 *
	 * @param string $content The post content.
	 * @return string Cleaned content.
	 */
	public static function multipage_clean_content( $content ) {
		if ( ! $content )
			return $content;

		// The shortcodes could be closed inside p tags, so we remove them from inside the content.
		$content = strip_tags( $content, '<br><img><b><strong><i><code><blockquote>' );

		// Also remove HTML comments (Gutenberg)
		$result = preg_replace( '/<!--[\s\S]*?-->/', '', $content );
		if ( null !== $result ) {
			$content = $result;
		}

		return trim( $content );
	}

	/**
	 * Find all [nextpage] shortcodes in the content.
	 *
	 * @since 1.4
	 *
	 * @param string $content The post content.
	 * @return array Regex matches array.
	 */
	public static function parse_nextpage_shortcode( $content ) {
		preg_match_all( MPP_PATTERN, $content, $matches );
		return $matches;
	}
}

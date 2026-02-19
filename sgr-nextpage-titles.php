<?php /*
Plugin Name: Multipage
Plugin URI: https://wordpress.org/plugins/sgr-nextpage-titles/
Description: Split your WordPress posts into multiple subpages, each with its own title and an automatic table of contents. Supports Gutenberg blocks, classic editor shortcodes, SEO-friendly markup, and customizable navigation.
Author: Paul ARGOUD, based on an original idea by SGr33n (aka Sergio De Falco, Envire Web Solutions).
Version: 1.5.15
Author URI: https://paul.argoud.net
Text Domain: sgr-nextpage-titles
Domain Path: /languages/
License: GPL v3
Requires at least: 5.0
Requires PHP: 7.4
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'MPP_VERSION', '1.5.15' );
define( 'MPP__PLUGIN_FILE', __FILE__ );

/**
 * The main function.
 *
 * @return Multipage instance.
 */
function multipage() {
	return Multipage::instance();
}

require_once plugin_dir_path( __FILE__ ) . 'class-mpp.php';

// Start
multipage();
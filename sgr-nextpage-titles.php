<?php

/*
Plugin Name: Multipage
Plugin URI: http://wordpress.org/plugins/sgr-nextpage-titles/
Description: Split your WordPress posts into multiple subpages, each with its own title and an automatic table of contents. Supports Gutenberg blocks, classic editor shortcodes, SEO-friendly markup, and customizable navigation.
Author: Paul ARGOUD, based on an original idea by Sergio De Falco (aka <a href="https://github.com/wp-plugins/sgr-nextpage-titles" target="_blank">SGr33n</a>).
Version: 1.5.14
Author URI: https://www.envire.it
Text Domain: sgr-nextpage-titles
Domain Path: /languages/
License: GPL v3
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'MPP_VERSION', '1.5.14' );
define( 'MPP__MINIMUM_WP_VERSION', '5.0' );
define( 'MPP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPP__PLUGIN_FILE', __FILE__ );

/**
 * The main function.
 *
 * @return Multipage instance.
 */
function multipage() {
	return Multipage::instance();
}

require_once( MPP__PLUGIN_DIR . 'class-mpp.php' );

// Start
multipage();
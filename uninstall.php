<?php
/**
 * Multipage Uninstall.
 *
 * Removes all plugin data when the plugin is deleted via the WordPress admin.
 *
 * @package Multipage
 * @since 1.5.15
 */

// Exit if not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete plugin options.
$option_names = array(
	'mpp-hide-intro-title',
	'mpp-comments-on-page',
	'mpp-continue-or-prev-next',
	'mpp-disable-standard-pagination',
	'mpp-toc-only-on-the-first-page',
	'mpp-toc-position',
	'mpp-toc-row-labels',
	'mpp-hide-toc-header',
	'mpp-comments-toc-link',
	'mpp-disable-tinymce-buttons',
	'_mpp-rewrite-title-priority',
	'_mpp-rewrite-content-priority',
	'_mpp_db_version',
	'_mpp-postmeta-built',
);

foreach ( $option_names as $option ) {
	delete_option( $option );
}

// Delete all _mpp_data post meta entries.
$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_mpp_data' ) );

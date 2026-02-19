<?php
/**
 * Multipage Admin Advanced Settings.
 *
 * @package Multipage
 * @since 1.4
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main settings section description for the settings page.
 *
 * @since 1.4
 */
function mpp_admin_advanced_settings_callback_main_section() { }

/**
 * Sets the rewrite title priority.
 *
 * @since 1.4
 *
 */
function mpp_admin_advanced_callback_rewrite_title_priority() {
	// Set the priority choice values.
	$priority_choices = array( 'highest' => 100, 'high' => 50, 'normal' => 20, 'low' => 10, 'lowest' => 5 );
?>

	<select id="rewrite-title-priority" name="_mpp-rewrite-title-priority" class="rewrite-title-priority">
		<?php foreach ( $priority_choices as $priority => $value) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, mpp_get_rewrite_title_priority() ); ?>><?php echo esc_html( $priority ); ?></option>
		<?php endforeach; ?>
	</select>
	<p id="rewrite-title-description" class="description"><?php esc_html_e( 'Some plugins need this higher in order to correctly show the subpage title instead of "Page # of #". If the title works good please leave this to normal.', 'sgr-nextpage-titles' ); ?></p>
		
<?php
}

/**
 * Sets the rewrite content priority.
 *
 * @since 1.4
 *
 */
function mpp_admin_advanced_callback_rewrite_content_priority() {
	// Set the priority choice values.
	$priority_choices = array( 'highest' => 100, 'high' => 50, 'normal' => 20, 'low' => 10, 'lowest' => 5 );
?>

	<select id="rewrite-content-priority" name="_mpp-rewrite-content-priority" class="rewrite-content-priority">
		<?php foreach ( $priority_choices as $priority => $value) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, mpp_get_rewrite_content_priority() ); ?>><?php echo esc_html( $priority ); ?></option>
		<?php endforeach; ?>
	</select>
	<p id="rewrite-content-description" class="description"><?php printf( __( 'This value affects the position where the table of contents is displayed, referring to other plugins that use <code><a href="%1$s" target="_blank">the_content</a></code> filter. e.g. above or below social buttons or related posts. If the table of contents position looks good please leave this to normal.', 'sgr-nextpage-titles' ), 'https://developer.wordpress.org/reference/hooks/the_content/' ); ?></p>
	
<?php
}

/**
 * Disable the TinyMCE buttons in order to preserve the compatibilty with older WP versions.
 *
 * @since 1.4
 *
 */
function mpp_admin_advanced_callback_disable_tinymce_buttons() {
?>

	<input id="disable-tinymce-buttons" name="mpp-disable-tinymce-buttons" type="checkbox" value="1" <?php checked( mpp_disable_tinymce_buttons() ); ?> />
	<label for="disable-tinymce-buttons"><?php esc_html_e( 'Disable TinyMCE Buttons', 'sgr-nextpage-titles' ); ?></label>
	<p id="disable-tinymce-description" class="description"><?php esc_html_e( 'Disable the TinyMCE Subpage button in the classic editor. Leave unchecked unless you experience editor conflicts.', 'sgr-nextpage-titles' ); ?></p>

<?php
}

/**
 * Rebuild the Multipage posts with the missing postmeta data.
 *
 * @since 1.4
 *
 */
function mpp_admin_advanced_callback_build_mpp_postmeta_data() {
?>

	<input id="postmeta-built" name="_mpp-postmeta-built" type="checkbox" value="<?php echo time(); ?>" />
	<label for="postmeta-built"><?php esc_html_e( 'Build Multipage postmetas', 'sgr-nextpage-titles' ); ?></label>
	<p id="postmeta-built-description" class="description"><?php esc_html_e( 'Please check this to build the Multipage postsmetas. If you see this option and were running a Multipage version < 1.4, please check this to build the required Multipage postmetas. Then this option will not display anymore.', 'sgr-nextpage-titles' ); ?></p>

<?php
}

/** Advanced settings Page *************************************************************/

/**
 * The advanced settings page
 *
 * @since 1.4
 *
 */
function mpp_admin_advanced() {
	// We're saving our own options, until the WP Settings API is updated to work with Multisite.
	$form_action = add_query_arg( 'page', 'mpp-advanced-settings', mpp_get_admin_url( 'options-general.php' ) );

	?>

	<div class="wrap">

		<h1><?php esc_html_e( 'Multipage Settings', 'sgr-nextpage-titles' ); ?></h1>
		
		<h2 class="nav-tab-wrapper"><?php mpp_admin_tabs( __( 'Advanced', 'sgr-nextpage-titles' ) ); ?></h2>
		
		<h2><?php esc_html_e( 'Advanced Settings', 'sgr-nextpage-titles' ); ?></h2>
		
		<p><?php esc_html_e( 'Please leave this settings to their default values, change only if you really know what to do.', 'sgr-nextpage-titles' ); ?></p>

		<form action="<?php echo esc_url( $form_action ) ?>" method="post">
		
			<?php settings_fields( 'mpp-advanced-settings' ); ?>

			<?php do_settings_sections( 'mpp-advanced-settings' ); ?>
		
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'sgr-nextpage-titles' ); ?>" />
			</p>
		</form>
		
	</div><!-- .wrap -->
	
<?php
}

/**
 * Save our settings.
 *
 * @since 1.4
 */
function mpp_admin_advanced_settings_save() {
	global $wp_settings_fields;

	if ( isset( $_GET['page'] ) && 'mpp-advanced-settings' === $_GET['page'] && !empty( $_POST['submit'] ) ) {
		check_admin_referer( 'mpp-advanced-settings-options' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Because many settings are saved with checkboxes, and thus will have no values
		// in the $_POST array when unchecked, we loop through the registered settings.
		if ( isset( $wp_settings_fields['mpp-advanced-settings'] ) ) {
			foreach( (array) $wp_settings_fields['mpp-advanced-settings'] as $section => $settings ) {
				foreach( $settings as $setting_name => $setting ) {	
					if ( $setting_name === '_mpp-postmeta-built' && isset( $_POST[$setting_name] ) ) {
						// Launch the Multipage postmetas building process.
						mpp_add_post_multipage_meta( false );
					}
					$value = isset( $_POST[$setting_name] ) ? intval( wp_unslash( $_POST[$setting_name] ) ) : 0;

					update_option( $setting_name, $value );
				}
			}
		}

		mpp_clear_options_cache();

		wp_safe_redirect( add_query_arg( array( 'page' => 'mpp-advanced-settings', 'updated' => 'true' ), mpp_get_admin_url( 'options-general.php' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'mpp_admin_advanced_settings_save', 100 );
<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */

class Sgdd_Activator {
	public static function activate() {
		if (!isset( $GLOBALS['wp_version'] ) || version_compare($GLOBALS['wp_version'], '5.0', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(esc_html__('Google Drive Documents requires at least WordPress 5.0', 'skaut-google-drive-documents'));
		}
		if (version_compare(phpversion(), '7.0', '<')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(esc_html__('Google Drive Documents requires at least PHP 7.0', 'skaut-google-drive-documents'));
		}
		set_transient('sgdd_activation_notice', true, 60);
	}
}

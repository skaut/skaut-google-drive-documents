<?php
/**
 * Main plugin file
 *
 * Function which runs plugin
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd;

/**
 * Plugin Name: Google Drive Documents
 * Plugin URI:  https://github.com/skaut/skaut-google-drive-documents
 * Description: A WordPress plugin to display and edit documents using Google Drive as file storage
 * Version:     0.1-beta
 * Author:      Kristián Kosorín
 * Author URI:  https://github.com/xkosorin
 * License:     GPLv3
 * License URI: https://raw.githubusercontent.com/skaut/skaut-google-drive-documents/master/plugin/license.txt
 * Text Domain: skaut-google-drive-documents
 *
 * Google Drive Documents is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Google Drive Documents is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Google Drive Documents. If not, see https://raw.githubusercontent.com/skaut/skaut-google-drive-documents/master/plugin/license.txt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'None of your business' );
}

require_once __DIR__ . '/admin/admin-page.php';
require_once __DIR__ . '/public/block.php';
require_once __DIR__ . '/admin/options/class-options.php';

require_once __DIR__ . '/admin/google-api.php';
require_once __DIR__ . '/includes/includes.php';

/**
 * Main init function of plugin
 */
function init() {
	register_activation_hook( __FILE__, '\\Sgdd\\activate' );
	add_action( 'admin_notices', '\\Sgdd\\activation_info' );
	add_action( 'plugins_loaded', [ '\\Sgdd\\Admin\\Options\\Options', 'init' ] );
	\Sgdd\Admin\AdminPage\register();
	\Sgdd\Pub\Block\register();
}

/**
 * Function that checks WP version and PHP version upon activation
 */
function activate() {
	if ( ! isset( $GLOBALS['wp_version'] ) || version_compare( $GLOBALS['wp_version'], '5.0', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'Google Drive Documents requires at least WordPress 5.0', 'skaut-google-drive-documents' ) );
	}

	if ( version_compare( phpversion(), '7.0', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html__( 'Google Drive Documents requires at least PHP 7.0', 'skaut-google-drive-documents' ) );
	}

	set_transient( 'sgdd_activation_info', true, 60 );
}

/**
 * Function that shows setup notice after activation
 */
function activation_info() {
	if ( get_transient( 'sgdd_activation_info' ) ) {
		$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-documents';

		echo '<div class="notice notice-info is-dismissible"><p>';
		// translators: 1: Start of a link to the settings 2: End of the link to the settings 3: Start of a help link 4: End of the help link.
		printf( esc_html__( 'Google Drive Documents needs to be %1$sconfigured%2$s before it can be used. See the %3$sdocumentation%4$s for more information.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdd_basic' ) ) . '">', '</a>', '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' );
		echo '</p></div>';
		delete_transient( 'sgdd_activation_info' );
	}
}

/**
 * Helper function for enqueuing scrtipts
 *
 * @param string $handle Name of the script.
 * @param string $src Full URL of the script.
 * @param array  $deps An array of registered script handles this script depends on.
 */
function enqueue_script( $handle, $src, $deps = [] ) {
	wp_enqueue_script( $handle, plugins_url( '/skaut-google-drive-documents' . $src ), $deps, filemtime( WP_PLUGIN_DIR . '/skaut-google-drive-documents' . $src ), true );
}

/**
 * Helper function for enqueuing styles
 *
 * @param string $handle Name of the script.
 * @param string $src Full URL of the script.
 * @param array  $deps An array of registered script handles this script depends on.
 */
function enqueue_style( $handle, $src, $deps = [] ) {
	wp_enqueue_style( $handle, plugins_url( '/skaut-google-drive-documents' . $src ), $deps, filemtime( WP_PLUGIN_DIR . '/skaut-google-drive-documents' . $src ) );
}

init();


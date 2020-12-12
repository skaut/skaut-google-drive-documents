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
 * Plugin Name: Files and Docs from Google Drive
 * Plugin URI:  https://github.com/skaut/skaut-google-drive-documents
 * Description: A WordPress plugin to display and edit documents using Google Drive as file storage
 * Version:     1.0.0
 * Author:      Kristián Kosorín
 * Author URI:  https://github.com/xkosorin
 * License:     MIT
 * License URI: https://raw.githubusercontent.com/skaut/skaut-google-drive-documents/master/plugin/license.txt
 * Text Domain: skaut-google-drive-documents
 *
 * MIT License
 * Copyright (c) 2019 Kristián Kosorín, Junák – český skaut, z. s.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
	add_action( 'plugins_loaded', array( '\\Sgdd\\Admin\\Options\\Options', 'init' ) );
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
	if ( false !== get_transient( 'sgdd_activation_info' ) ) {
		echo '<div class="notice notice-info is-dismissible"><p>';
		// translators: 1: Start of a link to the settings 2: End of the link to the settings 3: Start of a help link 4: End of the help link.
		printf( esc_html__( 'Google Drive Documents needs to be %1$sconfigured%2$s before it can be used. See the %3$sdocumentation%4$s for more information.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdd_basic' ) ) . '">', '</a>', '<a href="' . esc_url( 'https://github.com/skaut/skaut-google-drive-documents/wiki/N%C3%A1vod-na-nastavenie' ) . '" target="_blank">', '</a>' );
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
function enqueue_script( $handle, $src, $deps = array() ) {
	$dir = plugin_dir_path( __FILE__ );
	wp_enqueue_script( $handle, plugins_url( basename( __DIR__ ) . $src ), $deps, filemtime( $dir . $src ), true );
}

/**
 * Helper function for enqueuing styles
 *
 * @param string $handle Name of the script.
 * @param string $src Full URL of the script.
 * @param array  $deps An array of registered script handles this script depends on.
 */
function enqueue_style( $handle, $src, $deps = array() ) {
	$dir = plugin_dir_path( __FILE__ );
	wp_enqueue_style( $handle, plugins_url( basename( __DIR__ ) . $src ), $deps, filemtime( $dir . $src ) );
}

init();


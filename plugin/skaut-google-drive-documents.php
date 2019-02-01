<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Google Drive Documents
 * Plugin URI:        https://github.com/skaut/skaut-google-drive-documents
 * Description:       A WordPress plugin to display and edit documents using Google Drive as file storage
 * Version:           0.1
 * Author:            Kristián Kosorín
 * Author URI:        https://github.com/xkosorin
 * License:           MIT
 * License URI:       https://raw.githubusercontent.com/skaut/skaut-google-drive-documents/master/plugin/license.txt
 * Text Domain:       skaut-google-drive-documents
 * Domain Path:       /languages
 * 
 * MIT License
 *
 * Copyright (c) 2018 Kristián Kosorín
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

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die('None of your business');
}

define( 'PLUGIN_NAME_VERSION', '0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_sgdd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sgdd-activator.php';
	Sgdd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_sgdd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sgdd-deactivator.php';
	Sgdd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sgdd' );
register_deactivation_hook( __FILE__, 'deactivate_sgdd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sgdd.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_sgdd() {

	$plugin = new Sgdd();
	$plugin->run();

}

run_sgdd();

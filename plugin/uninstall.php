<?php
/**
 * Uninstall function
 *
 * Functions that runs on plugin deletion
 *
 * @package SGDD
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( 'None of your business' );
}

delete_option( 'sgdd_redirect_uri' );
delete_option( 'sgdd_client_id' );
delete_option( 'sgdd_client_secret' );

delete_option( 'sgdd_access_token' );

delete_option( 'sgdd_root_path' );

<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die('None of your business');
}

delete_option( 'sgdd_clientId' );
delete_option( 'sgdd_clientSecret' );
delete_option( 'sgdd_accessToken' );
delete_option( 'sgdd_redirectUri' );
delete_option( 'sgdd_rootPath' );
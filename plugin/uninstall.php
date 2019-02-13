<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die('None of your business');
}

delete_option( 'sgdg_client_id' );
delete_option( 'sgdg_client_secret' );
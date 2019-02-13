<?php

namespace Sgdd\Admin\Options;

require_once  __DIR__ . '/option-types/class-string-field.php';

class Options {
	public static $authorized_domain;
	public static $authorized_origin;
	public static $redirect_uri;
	public static $client_id;
	public static $client_secret;
	public static $root_path;
  
	public static function init() {
		$url                     = wp_parse_url( get_site_url() );
		self::$authorized_domain = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorized_domain', __( 'Authorized Domain', 'sgdd' ), 'settings', 'auth', $_SERVER['HTTP_HOST'] );
		self::$authorized_origin = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorized_origin', __( 'Authorized Origin', 'sgdd' ), 'settings', 'auth', $url['scheme'] . '://' . $url['host'] );
		self::$redirect_uri 		 = new \Sgdd\Admin\Options\OptionTypes\StringField( 'redirect_uri', __( 'Redirect Uri', 'sgdd' ), 'settings', 'auth', '' );
		self::$client_id 				 = new \Sgdd\Admin\Options\OptionTypes\StringField( 'client_id', __( 'Client ID', 'sgdd' ), 'settings', 'auth', '' );
		self::$client_secret 		 = new \Sgdd\Admin\Options\OptionTypes\StringField( 'client_secret', __( 'Client Secret', 'sgdd' ), 'settings', 'auth', '' );
	}
}
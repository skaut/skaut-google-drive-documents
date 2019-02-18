<?php
namespace Sgdd\Admin\Options;

require_once  __DIR__ . '/option-types/class-string-field.php';

class Options {
	public static $authorizedDomain;
	public static $authorizedOrigin;
	public static $redirectUri;
	public static $clientId;
	public static $clientSecret;
	public static $rootPath;
  
	public static function init() {
		$url                    = wp_parse_url( get_site_url() );
		self::$authorizedDomain = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorizedDomain', __( 'Authorized Domain', 'skaut-google-drive-documents' ), 'settings', 'auth', $_SERVER['HTTP_HOST'] );
		self::$authorizedOrigin = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorizedOrigin', __( 'Authorized Origin', 'skaut-google-drive-documents' ), 'settings', 'auth', $url['scheme'] . '://' . $url['host'] );
		self::$redirectUri 		  = new \Sgdd\Admin\Options\OptionTypes\StringField( 'redirectUri', __( 'Redirect Uri', 'skaut-google-drive-documents' ), 'settings', 'auth', esc_url_raw( admin_url( 'admin.php?page=sgdd_settings&action=oauth_redirect' ) ) );
		self::$clientId				  = new \Sgdd\Admin\Options\OptionTypes\StringField( 'clientId', __( 'Client ID', 'skaut-google-drive-documents' ), 'settings', 'auth', '' );
		self::$clientSecret 		= new \Sgdd\Admin\Options\OptionTypes\StringField( 'clientSecret', __( 'Client Secret', 'skaut-google-drive-documents' ), 'settings', 'auth', '' );
	}
}
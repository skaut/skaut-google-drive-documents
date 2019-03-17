<?php
namespace Sgdd\Admin\Options;

require_once  __DIR__ . '/option-types/class-string-field.php';
require_once  __DIR__ . '/option-types/class-path-field.php';

class Options {
  public static $authorizedDomain;
  public static $authorizedOrigin;
  public static $redirectUri;
  public static $clientId;
  public static $clientSecret;
  public static $rootPath;

  public static function init() {
    $url                    = wp_parse_url( get_site_url() );
    self::$authorizedDomain = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorizedDomain', __( 'Authorised domain', 'skaut-google-drive-documents' ), 'basic', 'auth', $_SERVER['HTTP_HOST'] );
    self::$authorizedOrigin = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorizedOrigin', __( 'Authorised JavaScript origin', 'skaut-google-drive-documents' ), 'basic', 'auth', $url['scheme'] . '://' . $url['host'] );
    self::$redirectUri      = new \Sgdd\Admin\Options\OptionTypes\StringField( 'redirectUri', __( 'Authorised redirect URI', 'skaut-google-drive-documents' ), 'basic', 'auth', esc_url_raw( admin_url( 'admin.php?page=sgdd_basic&action=oauth_redirect' ) ) );
    self::$clientId         = new \Sgdd\Admin\Options\OptionTypes\StringField( 'clientId', __( 'Client ID', 'skaut-google-drive-documents' ), 'basic', 'auth', '' );
    self::$clientSecret     = new \Sgdd\Admin\Options\OptionTypes\StringField( 'clientSecret', __( 'Client secret', 'skaut-google-drive-documents' ), 'basic', 'auth', '' );

    self::$rootPath         = new \Sgdd\Admin\Options\OptionTypes\PathField( 'rootPath', '', 'basic', 'pathSelection', [ ] );
  }
}

<?php
/**
 * Options class
 *
 * @package SGDD
 * @since 1.0.0
 */
namespace Sgdd\Admin\Options;

require_once __DIR__ . '/option-types/class-stringfield.php';
require_once __DIR__ . '/option-types/class-pathfield.php';
require_once __DIR__ . '/option-types/class-integerfield.php';
require_once __DIR__ . '/option-types/class-selectfield.php';

/**
 * A class that contain all configuration settings of plugin.
 */
class Options {
	/**
	 * Show authorized domain for registering Google app.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\StringField $authorized_domain
	 */
	public static $authorized_domain;

	/**
	 * Show authorized JavaScript origin for registering Google app.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\StringField $authorized_origin
	 */
	public static $authorized_origin;

	/**
	 * Show redirect uri for registering Google app.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\StringField $redirect_uri
	 */
	public static $redirect_uri;

	/**
	 * Client ID of Google app.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\StringField $client_id
	 */
	public static $client_id;

	/**
	 * Client secret of Google app.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\StringField $client_secret
	 */
	public static $client_secret;

	/**
	 * Root path of plugin. This is the "highest" point in hierarchy of drive folders accessible.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\PathField $root_path
	 */
	public static $root_path;

	/**
	 * The width of embeded file.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\IntegerField $embed_width
	 */
	public static $embed_width;

	/**
	 * The height of embeded file.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\IntegerField $embed_width
	 */
	public static $embed_height;

	/**
	 * Whether show folder as list or grif.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\SelectField $folder_type
	 */
	public static $folder_type;

	/**
	 * How to order files from folder.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\SelectField $order_by
	 */
	public static $order_by;

	/**
	 * Width of list when displaying folder content as list.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\IntegerField $list_width
	 */
	public static $list_width;

	/**
	 * Number of columns when displaying folder content as grid.
	 *
	 * @var \Sgdd\Admin\Options\OptionTypes\IntegerField $grid_cols
	 */
	public static $grid_cols;

	/**
	 * Class initializer function
	 */
	public static function init() {
		$url                     = wp_parse_url( get_site_url() );
		self::$authorized_domain = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorized_domain', __( 'Authorised domain', 'skaut-google-drive-documents' ), 'basic', 'auth', $_SERVER['HTTP_HOST'] );
		self::$authorized_origin = new \Sgdd\Admin\Options\OptionTypes\StringField( 'authorized_origin', __( 'Authorised JavaScript origin', 'skaut-google-drive-documents' ), 'basic', 'auth', $url['scheme'] . '://' . $url['host'] );
		self::$redirect_uri      = new \Sgdd\Admin\Options\OptionTypes\StringField( 'redirect_uri', __( 'Authorised redirect URI', 'skaut-google-drive-documents' ), 'basic', 'auth', esc_url_raw( admin_url( 'admin.php?page=sgdd_basic&action=oauth_redirect' ) ) );
		self::$client_id         = new \Sgdd\Admin\Options\OptionTypes\StringField( 'client_id', __( 'Client ID', 'skaut-google-drive-documents' ), 'basic', 'auth', '' );
		self::$client_secret     = new \Sgdd\Admin\Options\OptionTypes\StringField( 'client_secret', __( 'Client secret', 'skaut-google-drive-documents' ), 'basic', 'auth', '' );

		self::$root_path = new \Sgdd\Admin\Options\OptionTypes\PathField( 'root_path', '', 'basic', 'path_selection', [] );

		self::$embed_width  = new \Sgdd\Admin\Options\OptionTypes\StringField( 'embed_width', __( 'Width', 'skaut-google-drive-documents' ), 'advanced', 'file', '100%' );
		self::$embed_height = new \Sgdd\Admin\Options\OptionTypes\StringField( 'embed_height', __( 'Height', 'skaut-google-drive-documents' ), 'advanced', 'file', '600px' );

		self::$folder_type = new \Sgdd\Admin\Options\OptionTypes\SelectField( 'folder_type', __( 'Folder view type', 'skaut-google-drive-documents' ), 'advanced', 'folder', 'list' );

		self::$order_by = new \Sgdd\Admin\Options\OptionTypes\SelectField( 'order_by', __( 'Order files by', 'skaut-google-drive-documents' ), 'advanced', 'folder', 'name_asc' );

		self::$list_width = new \Sgdd\Admin\Options\OptionTypes\StringField( 'list_width', __( 'List width', 'skaut-google-drive-documents' ), 'advanced', 'folder', '100%' );
		self::$grid_cols  = new \Sgdd\Admin\Options\OptionTypes\IntegerField( 'grid_cols', __( 'Grid columns', 'skaut-google-drive-documents' ), 'advanced', 'folder', '3' );
	}
}

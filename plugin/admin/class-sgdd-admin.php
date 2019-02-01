<?php
/**
 * The admin-specific functionality of the plugin.
 */
class Sgdd_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	private $authorized_domain;
	private $authorized_origin;
	private $redirect_uri;
	private $client_id;
	private $client_secret;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function activation_notice() {
		if ( get_transient( 'sgdd_activation_notice' ) ) {
			echo( '<div class="notice notice-info is-dismissible"><p>' );
			$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-documents';
			// translators: 1: Start of a link to the settings 2: End of the link to the settings 3: Start of a help link 4: End of the help link
			printf( esc_html__( 'Google Drive Documents needs to be %1$sconfigured%2$s before it can be used. See the %3$sdocumentation%4$s for more information.', 'skaut-google-drive-gallery' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdg_basic' ) ) . '">', '</a>', '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' );
			echo( '</p></div>' );
			delete_transient( 'sgdd_activation_notice' );
		}
	}

	public function add_menu() {
		add_menu_page( 
			__('Google Drive Documents Settings', 'skaut-google-drive-documents'),
			__('Google Drive Documents', 'skaut-google-drive-documents'),
			'manage_options',
			'sgdd_settings',
			array( $this, 'display_options_page' ),
			plugins_url('/skaut-google-drive-documents/admin/icon.png')
		);
	}

	public function display_options_page() {
		require_once __DIR__ . '/partials/sgdd-admin-display.php';
	}

	/**
	 * Register and add settings
	 */
	public function auth_page_init()
	{     
		require_once __DIR__ . '/class-sgdd-options.php';
		add_settings_section( 'sgdd_auth', esc_html__( 'Step 1: Authorization', 'sgdd' ), array( $this, 'print_section_info' ), 'sgdd_setting' );
		
		//if not authorized
		$this->oauth_grant();

		//if authorized
		//$this->oauth_revoke();
	}


	public function show_help() {
		$help_link = 'https://napoveda.skaut.cz/dobryweb/' . substr( get_locale(), 0, 2 ) . '-skaut-google-drive-gallery';
		add_settings_error( 'general', 'help', sprintf( esc_html__( 'See the %1$sdocumentation%2$s for more information about how to configure the plugin.', 'sgdd' ), '<a href="' . esc_url( $help_link ) . '" target="_blank">', '</a>' ), 'notice-info' );
		settings_errors();
	}
		
	public function print_section_info() {
		//if not authorized
		$this->show_help();
		echo( '<p>' . __( 'Create a Google app and provide the following details:', 'sgdd' ) . '</p>' );
		echo( '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdg_basic&action=oauth_grant' ) ), 'oauth_grant' ) . '">' . __( 'Grant Permission', 'sgdd' ) . '</a>' );

		//if authorized
		/*echo( '<p>' . __( 'Create a Google app and provide the following details:', 'sgdd' ) . '</p>' );
		echo( '<a class="button button-primary" href="' . esc_url_raw( wp_nonce_url( admin_url( 'admin.php?page=sgdg_basic&action=oauth_grant' ) ), 'oauth_revoke' ) . '">' . __( 'Revoke Permission', 'sgdd' ) . '</a>' );*/		
	}

	public function init_options() {
		require_once 'class-sgdd-options.php';
		Options::init();
	}

	public function oauth_grant() {
		require_once __DIR__ . '/class-sgdd-options.php';

		Options::$authorized_domain->add_field( true );
		Options::$authorized_origin->add_field( true );
		Options::$redirect_uri->add_field( true );
		Options::$client_id->add_field();
		Options::$client_secret->add_field();
	}

	public function oauth_revoke() {
		require_once __DIR__ . '/class-sgdd-options.php';

		Options::$authorized_domain->add_field( true );
		Options::$authorized_origin->add_field( true );
		Options::$redirect_uri->add_field( true );
		Options::$client_id->add_field( true );
		Options::$client_secret->add_field( true );
	}
}

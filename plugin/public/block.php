<?php
namespace Sgdd\Pub\Block;

if ( ! is_admin() ) {
	return;
}

function register() {
	if ( function_exists( 'register_block_type' ) ) {
		add_action( 'init', '\\Sgdd\\Pub\\Block\\add_block' );
	}
}

function add_block() {
	\Sgdd\enqueue_script( 'sgdd_block_js', '/public/js/block.js', [ 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element', 'wp-i18n', 'sgdd_file_selection_js' ] );
	\Sgdd\enqueue_script( 'sgdd_file_selection_js', '/public/js/file-selection.js', [ 'wp-components', 'wp-element', 'sgdd_inspector_js', 'sgdd_settings_base_js' ] );
	\Sgdd\enqueue_script( 'sgdd_inspector_js', '/public/js/inspector.js', [ 'wp-element', 'sgdd_integer_settings_js', 'sgdd_select_settings_js', 'sgdd_bool_settings_js' ] );
	\Sgdd\enqueue_script( 'sgdd_settings_base_js', '/public/js/settings-base.js', [ 'wp-element' ] );
	\Sgdd\enqueue_script( 'sgdd_integer_settings_js', '/public/js/integer-setting.js', [ 'wp-element', 'sgdd_settings_base_js' ] );
	\Sgdd\enqueue_script( 'sgdd_select_settings_js', '/public/js/select-setting.js', [ 'wp-element', 'sgdd_settings_base_js' ] );
	\Sgdd\enqueue_script( 'sgdd_bool_settings_js', '/public/js/bool-setting.js', [ 'wp-element', 'sgdd_settings_base_js' ] );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );

	wp_localize_script(
		'sgdd_block_js',
		'sgddBlockJsLocalize',
		[
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'sgdd_block_js' ),
			'blockName'        => esc_html__( 'Google Drive Documents', 'skaut-google-drive-documents' ),
			'blockDescription' => esc_html__( 'Embed your files from Google Drive', 'skaut-google-drive-documents' ),
			'root'             => esc_html__( 'Google Drive', 'skaut-google-drive-documents' ),
			'rootPath'         => \Sgdd\Admin\Options\Options::$root_path->get(),
			'embedWidth'       => [ esc_html__( 'Width', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$embed_width->get() ],
			'embedHeight'      => [ esc_html__( 'Height', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$embed_height->get() ],
			'units'            => [ esc_html__( 'Units', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$size_unit->get() ],
			'pixels'           => esc_html__( 'Pixels', 'skaut-google-drive-documents' ),
			'percentage'       => esc_html__( 'Percentage', 'skaut-google-drive-documents' ),
			'folder'           => [ esc_html__( 'List folder', 'skaut-google-drive-documents' ), 'false' ],
			'permissions'      => [ 'Set permissions' ],
		]
	);

	\Sgdd\enqueue_style( 'sgdd_block', '/public/css/block.css' );
	register_block_type(
		'sakut-google-drive-documents/block',
		[
			'editor_script'   => 'sgdd_block_js',
			'render_callback' => '\\Sgdd\\Pub\\Block\\display',
		]
	);
}

/* -- WIP -- */
function display( $attr ) {
	if ( 'pixels' === $attr['units'] ) {
		$unit = 'px';
	} else {
		$unit = '%';
	}

	$size = 'width="' . $attr['embedWidth'] . $unit . '" height="' . $attr['embedHeight'] . $unit . '"';

	$test   = print_file( $attr['fileId'] );
	$link   = $test['id'];

	if ( 'true' === $attr['folder'] ) {
		$result = '<iframe src="https://drive.google.com/embeddedfolderview?id=' . $link . '#list" style="' . $size . ' border:0;"></iframe>';
	} else {
		$result = '<iframe src="https://docs.google.com/viewer?srcid=' . $link . '&pid=explorer&efh=false&a=v&chrome=false&embedded=true" ' . $size . '></iframe>';
	}

	return $result;
}

function print_file( $file_id ) {
	try {
		$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();

		$domain_permission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
			[
				'role' => 'reader',
				'type' => 'anyone',
			]
		);

		$request     = $service->permissions->create( $file_id, $domain_permission, [ 'supportsTeamDrives' => true ] );
		$get_options = [
			'supportsAllDrives'         => true,
			'fields'             => '*',
		];

		$response = $service->files->get( $file_id, $get_options );

		return $response;
	} catch ( \Exception $e ) {
		return 'Chyba!! ' . $e->getMessage();
	}
}

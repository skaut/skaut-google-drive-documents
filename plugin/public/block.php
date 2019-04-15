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
	\Sgdd\enqueue_script( 'sgdd_file_selection_js', '/public/js/file-selection.js', [ 'wp-components', 'wp-element' ] );
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
	$test   = print_file( $attr['fileId'] );
	$link   = $test['id'];
	$result = '<iframe src="https://docs.google.com/viewer?srcid=' . $link . '&pid=explorer&efh=false&a=v&chrome=false&embedded=true" width="47%" height="500px" style="border:none;"></iframe>';

	return $result;
}

function print_file( $file_id ) {
	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();

	$domain_permission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
		[
			'role' => 'reader',
			'type' => 'anyone',
		]
	);

	$request     = $service->permissions->create( $file_id, $domain_permission, [ 'supportsTeamDrives' => true ] );
	$get_options = [
		'supportsTeamDrives' => true,
		'fields'             => '*',
	];

	$response = $service->files->get( $file_id, $get_options );

	return $response;
}

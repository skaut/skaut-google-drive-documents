<?php
namespace Sgdd\Admin\Block;

if ( ! is_admin() ) {
	return;
}

function register() {
	if ( function_exists( 'register_block_type' ) ) {
		add_action( 'init', '\\Sgdd\\Admin\\Block\\add_block' );
	}
}

function add_block() {
	\Sgdd\enqueue_script( 'sgdd_block_js', '/admin/js/block.js', [ 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element' ] );

	register_block_type(
		'sakut-google-drive-documents/block',
		[
			'editor_script'   => 'sgdd_block_js',
			'render_callback' => '\\Sgdd\\Admin\\Block\\display',
		]
	);
}

function display( $attr ) {
	$test = printFile();
	$link = $test['id'];
	//return '<embed width="500px" height="500px" src="' . $link2 . '">';
	return '<iframe src="https://docs.google.com/viewer?srcid=' . $link . '&pid=explorer&efh=false&a=v&chrome=false&embedded=true" width="47%" height="500px" style="border:none;"></iframe>';
}

function printFile() {
	$fileId = '1cXBcZ-XE2PyyMKYulVyeMik7pJaqj03cCxsb13SXXQs';
	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();

	$domainPermission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
		[
			'role' => 'reader',
			'type' => 'anyone',
		]
	);

	$request = $service->permissions->create( $fileId, $domainPermission, [ 'supportsTeamDrives' => true ] );
	
	$get_options = [
		'supportsTeamDrives'    => true,
		'fields'                => '*',
	];

	$response = $service->files->get( $fileId, $get_options );

	return $response;
}

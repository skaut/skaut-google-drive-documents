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
	\Sgdd\enqueue_script( 'sgdd_inspector_js', '/public/js/inspector.js', [ 'wp-element', 'sgdd_integer_settings_js', 'sgdd_select_settings_js' ] );
	\Sgdd\enqueue_script( 'sgdd_settings_base_js', '/public/js/settings-base.js', [ 'wp-element' ] );
	\Sgdd\enqueue_script( 'sgdd_integer_settings_js', '/public/js/integer-setting.js', [ 'wp-element', 'sgdd_settings_base_js' ] );
	\Sgdd\enqueue_script( 'sgdd_select_settings_js', '/public/js/select-setting.js', [ 'wp-element', 'sgdd_settings_base_js' ] );
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
			'list'             => esc_html__( 'List', 'skaut-google-drive-documents' ),
			'grid'             => esc_html__( 'Grid', 'skaut-google-drive-documents' ),
			'folderType'       => [ esc_html__( 'List folder as', 'skaut-google-drive-documents' ) ],
		]
	);

	\Sgdd\enqueue_style( 'sgdd_block', '/public/css/block.css' );
	register_block_type(
		'skaut-google-drive-documents/block',
		[
			'editor_script'   => 'sgdd_block_js',
			'render_callback' => '\\Sgdd\\Pub\\Block\\display',
		]
	);
}

/* -- WIP -- */
function display( $attr ) {
	$size = '';

	if ( isset( $attr['embedWidth'] ) ) {
		$size .= 'width:' . $attr['embedWidth'] . 'px;';
	}

	if ( isset( $attr['embedHeight'] ) ) {
		$size .= 'height:' . $attr['embedHeight'] . 'px;';
	}
	
	if ( isset( $attr['folderType'] ) ) {
		$list = show_folder_list( $attr['folderId'] );

		$result = '<table>'
						. '<tbody>';

		if ( 'list' === $attr['folderType'] ) {
			//LIST - almost done
			foreach( $list as $file ) {
				//var_dump($file);
				$result .= '<tr>';
				$result .= '<td><img src="' . $file['iconLink'] . '"></td>';
				$result .= '<td><a href="' . $file['webViewLink'] . '" target="_blank">' . $file['name'] . '</a></td>';
				$result .= '</tr>';
			}
		} else {
			//GRID - add setting - num of columns
			//		 - add thumbnails
			//		 - fix empty space on first row
			$i = 0;
			foreach( $list as $file ) {
				//var_dump($file);
				$i++;
				$i % 3 == 0 ? $result .= '<tr>' : $result .= '';
				$result .= '<td><a href="' . $file['webViewLink'] . '" target="_blank"><img src="' . $file['thumbnailLink'] . '"></a></td>';
				$i % 3 == 2 ? $result .= '</tr>' : $result .= '';
			}
		}

		$result .= '</tbody>'
						. '</table>';
	} else {
		$test = print_file( $attr['fileId'] );
		$link   = $test['id'];
		$result = '<iframe src="https://docs.google.com/viewer?srcid=' . $link . '&pid=explorer&efh=false&a=v&chrome=false&embedded=true" style="' . $size . ' border:0;"></iframe>';
	}

	return $result;
}

function print_file( $file_id ) {
	//TODO: make efficient and more simple

	//Only to set permissions to display file
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
			'supportsAllDrives' => true,
			'fields'            => '*',
		];

		$response = $service->files->get( $file_id, $get_options );

		return $response;
	} catch ( \Exception $e ) {
		return $e->getMessage();
	}
}

function show_folder_list( $folderId ) {
	//TODO

	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();

	if ( ! isset( $folderId ) ) {
		$folderId = end( \Sgdd\Admin\Options\Options::$root_path->get() );
	}

	$result     = [];
	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $folderId . "' in parents",
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
				'pageToken'                 => $page_token,
				'pageSize'                  => 1000,
				'fields'                    => '*',
			)
		);

		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $response;
}
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
	/*wp_enqueue_script( 'thickbox' );
	wp_enqueue_style( 'thickbox' );*/

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
			'folderType'       => [ esc_html__( 'List folder as', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$folder_type->get() ],
			'listWidth'        => [ esc_html__( 'Width', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$list_width->get() ],
			'gridCols'         => [ esc_html__( 'Grid columns', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$grid_cols->get() ],
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

function display( $attr ) {
	if ( isset( $attr['folderType'] ) || !isset( $attr['fileId'] ) ) {
		//display folder
		$folderId;
		$folderType;
		$content;
		$width;
		$cols;
		$result;

		//set folderId variable
		if ( isset( $attr['folderId'] ) ) {
			$folderId = $attr['folderId'];
		} else {
			$root_path_array = \Sgdd\Admin\Options\Options::$root_path->get();
			$folderId = end( $root_path_array );
		}

		if ( isset( $attr['folderType'] ) ) {
			$folderType = $attr['folderType'];
		} else {
			$folderType = \Sgdd\Admin\Options\Options::$folder_type->get();
		}

		//gdrive request to fetch content of folder
		try {
			$content = fetch_folder_content( $folderId );
		} catch ( \Exception $e ) {
			return '<div class="notice notice-error">Error while fetching folder content! <br> ' . $e->getErrors()[0]['message'] . '</div>';
		}

		if ( 'list' === $folderType ) {
			//display list
			if ( isset( $attr['listWidth'] ) ) {
				$result = build_result( $content, 'list', array( 'width' => $attr['listWidth'] ) );
			} else {
				$result = build_result( $content, 'list', array() );
			}	
		} else {
			//display grid
			if ( isset( $attr['gridCols'] ) ) {
				$cols = $attr['gridCols'];
			} else {
				$cols = \Sgdd\Admin\Options\Options::$grid_cols->get();
			}

			if ( isset( $attr['listWidth'] ) ) {
				$result = build_result( $content, 'grid', array( 'width' => $attr['listWidth'], 'cols' => $cols ) );
			} else {
				$result = build_result( $content, 'grid', array( 'cols' => $cols ) );
			}
		}

		$result .= '</tbody>
								</table>';

		return $result;
	} else {
		//display file
		$size = '';
		$id = $attr['fileId'];

		try {
			$temp = set_file_permissions( $id );
		} catch ( \Exception $e ) {
			return '<div class="notice notice-error">Error while setting permissions! <br> ' . $e->getErrors()[0]['message'] . '</div>';
		}

		if ( isset( $attr['embedWidth'] ) ) {
			$size .= 'width:' . $attr['embedWidth'] . 'px; ';
		}

		if ( isset( $attr['embedHeight'] ) ) {
			$size .= 'height:' . $attr['embedHeight'] . 'px; ';
		}

		$result = '<iframe src="https://drive.google.com/file/d/' . $id . '/preview" style="' . $size . 'border:0;"></iframe>';

		return $result;
	}
}

function set_file_permissions( $fileId ) {
	try {
		$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();
		$domain_permission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
			[
				'role' => 'reader',
				'type' => 'anyone',
			]
		);
		$request     = $service->permissions->create( $fileId, $domain_permission, [ 'supportsTeamDrives' => true ] );
		$get_options = [
			'supportsAllDrives' => true,
			'fields'            => 'id',
		];
		$response = $service->files->get( $fileId, $get_options );
		return $response;
	} catch ( \Exception $e ) {
		return $e->getMessage();
	}
}

function fetch_folder_content( $folderId ) {
	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $folderId . "' in parents",
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
				'pageToken'                 => $page_token,
				'pageSize'                  => 1000,
				'fields'                    => 'files',
			)
		);

		if ( $response instanceof \Sgdd\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $response;
}

function build_result( $content, $type, $arg ) {
	$result;

	if ( empty( $content['files'] ) ) {
		return 'Vybraný priečinok neobsahuje žiadne položky!';
	}

	if ( $type === 'list' ) {
		//build list table
		if ( !empty( $arg ) ) {
			$result = '<table style="width:' . $arg['width'] . 'px"><tbody>';
		} else {
			$result = '<table><tbody>';
		}

		foreach ( $content as $element ) {
			$result .= '<tr>
				<td><img src="' . $element['iconLink'] . '"></td>
				<td><a href="' . $element['webViewLink'] . '" target="_blank">' . $element['name'] . '</a></td>
			</tr>';
		}

		return $result;
	} else {
		//build grid table
		$i = 0;
		$width;
		$cols = $arg['cols'];

		if ( array_key_exists( 'width', $arg ) ) {
			$result = '<table style="table-layout:fixed; border-collapse:separate; width:' . $arg['width'] . 'px"><tbody>';
		} else {
			$result = '<table style="table-layout:fixed; border-collapse:separate;"><tbody>';
		}

		foreach ( $content as $element ) {
			$i % $cols == 0 ? $result .= '<tr>' : $result .= '';

			if ( !$element['hasThumbnail'] || preg_match( '/\b(google-apps)/', $element['mimeType'] ) ){
				$result .= '<td><div class="element"><a href="' . $element['webViewLink'] . '" target="_blank"><div class="image"><img src="' . preg_replace('(16)', '128',$element['iconLink']) . '"></div><div class="caption">' . $element['name'] . '</div></a></div></td>';
			} else {
				$result .= '<td><div class="element"><a href="' . $element['webViewLink'] . '" target="_blank"><div class="image"><img src="' . $element['thumbnailLink'] . '"></div><div class="caption">' . $element['name'] . '</div></a></div></td>';
			}
			$i % $cols  == $cols - 1 ? $result .= '</tr>' : $result .= '';
			$i++;
		}

		return $result;
	}
}
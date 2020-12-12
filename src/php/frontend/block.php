<?php
/**
 * Block functionality
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Pub\Block;

if ( ! is_admin() ) {
	return;
}

/**
 * Registers actions into WordPress.
 */
function register() {
	if ( function_exists( 'register_block_type' ) ) {
		add_action( 'init', '\\Sgdd\\Pub\\Block\\add_block' );
	}

	add_action( 'wp_ajax_setPermissions', '\\Sgdd\\Pub\\Block\\ajax_handler' );
}

/**
 * Adds block into WordPress
 */
function add_block() {
	\Sgdd\enqueue_script( 'sgdd_block_js', '/public/js/block.js', array( 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element', 'wp-i18n', 'sgdd_file_selection_js' ) );
	\Sgdd\enqueue_script( 'sgdd_file_selection_js', '/public/js/file-selection.js', array( 'wp-components', 'wp-element', 'sgdd_inspector_js', 'sgdd_settings_base_js' ) );
	\Sgdd\enqueue_script( 'sgdd_inspector_js', '/public/js/inspector.js', array( 'wp-element', 'sgdd_integer_settings_js', 'sgdd_string_settings_js', 'sgdd_select_settings_js', 'sgdd_button_settings_js' ) );
	\Sgdd\enqueue_script( 'sgdd_settings_base_js', '/public/js/settings-base.js', array( 'wp-element' ) );
	\Sgdd\enqueue_script( 'sgdd_integer_settings_js', '/public/js/integer-setting.js', array( 'wp-element', 'sgdd_settings_base_js' ) );
	\Sgdd\enqueue_script( 'sgdd_string_settings_js', '/public/js/string-setting.js', array( 'wp-element', 'sgdd_settings_base_js' ) );
	\Sgdd\enqueue_script( 'sgdd_select_settings_js', '/public/js/select-setting.js', array( 'wp-element', 'sgdd_settings_base_js' ) );
	\Sgdd\enqueue_script( 'sgdd_button_settings_js', '/public/js/button-setting.js', array( 'wp-element' ) );

	wp_localize_script(
		'sgdd_block_js',
		'sgddBlockJsLocalize',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'sgdd_block_js' ),
			'noncePerm'        => wp_create_nonce( 'sgdd_block_js_permissions' ),
			'blockName'        => esc_html__( 'Google Drive Documents', 'skaut-google-drive-documents' ),
			'blockDescription' => esc_html__( 'Embed your files from Google Drive', 'skaut-google-drive-documents' ),
			'root'             => esc_html__( 'Google Drive', 'skaut-google-drive-documents' ),
			'rootPath'         => \Sgdd\Admin\Options\Options::$root_path->get(),
			'embedWidth'       => array( esc_html__( 'Width', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$embed_width->get() ),
			'embedHeight'      => array( esc_html__( 'Height', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$embed_height->get() ),
			'list'             => esc_html__( 'List', 'skaut-google-drive-documents' ),
			'grid'             => esc_html__( 'Grid', 'skaut-google-drive-documents' ),
			'folderType'       => array( esc_html__( 'List folder as', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$folder_type->get() ),
			'name_asc'         => esc_html__( 'Name (ascending)', 'skaut-google-drive-documents' ),
			'name_dsc'         => esc_html__( 'Name (descending)', 'skaut-google-drive-documents' ),
			'time_asc'         => esc_html__( 'Time (ascending)', 'skaut-google-drive-documents' ),
			'time_dsc'         => esc_html__( 'Time (descending)', 'skaut-google-drive-documents' ),
			'orderBy'          => array( esc_html__( 'Order files by', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$order_by->get() ),
			'listWidth'        => array( esc_html__( 'Width', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$list_width->get() ),
			'gridCols'         => array( esc_html__( 'Grid columns', 'skaut-google-drive-documents' ), \Sgdd\Admin\Options\Options::$grid_cols->get() ),
			'setPermissions'   => esc_html__( 'Set permissions', 'skaut-google-drive-documents' ),
		)
	);

	\Sgdd\enqueue_style( 'sgdd_block', '/public/css/block.css' );
	register_block_type(
		'skaut-google-drive-documents/block',
		array(
			'editor_script'   => 'sgdd_block_js',
			'render_callback' => '\\Sgdd\\Pub\\Block\\display',
		)
	);
}

/**
 * Parses input string with dimension, e.g.:
 *  - '100' -> '100px',
 *  - '100px' -> '100px',
 *  - '100%' -> '100%'.
 *
 * @param string $in Input string.
 *
 * @return string If $in contains only digits append to it 'px' otherwise return unmodified $in
 */
function parse_dimension( $in ) {
	$out = $in;

	if ( ctype_digit( $in ) ) {
		$out .= 'px';
	}

	return $out;
}

/**
 * Displays block into editor and returns HTML format to frontend.
 *
 * @param array $attr Attributes fetched from JS request.
 *
 * @return string HTML format to display on frontend
 */
function display( $attr ) {
	// display folder.
	if ( isset( $attr['folderType'] ) || ! isset( $attr['fileId'] ) ) {
		// set folderId variable.
		if ( isset( $attr['folderId'] ) ) {
			$folder_id = $attr['folderId'];
		} else {
			$root_path_array = \Sgdd\Admin\Options\Options::$root_path->get();
			$folder_id       = end( $root_path_array );
		}

		if ( isset( $attr['folderType'] ) ) {
			$folder_type = $attr['folderType'];
		} else {
			$folder_type = \Sgdd\Admin\Options\Options::$folder_type->get();
		}

		$order_by = $attr['orderBy'] ?? '';

		// gdrive request to fetch content of folder.
		try {
			$content = fetch_folder_content( $folder_id, $order_by );
		} catch ( \Exception $e ) {
			return '<div class="notice notice-error">' . __( 'Error while fetching folder content!', 'skaut-google-drive-documents' ) . '<br> ' . $e . '</div>';
		}

		$result_args = array();
		if ( isset( $attr['listWidth'] ) ) {
			$result_args['width'] = $attr['listWidth'];
		}
		if ( isset( $attr['gridCols'] ) ) {
			$result_args['cols'] = $attr['gridCols'];
		} else {
			$result_args['cols'] = \Sgdd\Admin\Options\Options::$grid_cols->get();
		}

		$result = build_result(
			$content,
			'list' === $folder_type ? 'list' : 'grid',
			$result_args
		);

		return $result;
	} else {
		// display file.
		$id    = $attr['fileId'];
		$style = 'style="border:0;';

		if ( isset( $attr['embedWidth'] ) ) {
			$style .= ' width:' . parse_dimension( $attr['embedWidth'] ) . '; ';
		}

		if ( isset( $attr['embedHeight'] ) ) {
			$style .= ' height:' . parse_dimension( $attr['embedHeight'] ) . '; ';
		}

		$style .= '"';

		$result = '<iframe class="sgdd-embedded-file" src="https://drive.google.com/file/d/' . $id . '/preview" ' . $style . '></iframe>';

		return $result;
	}
}

/**
 * Handles Ajax response from JS
 */
function ajax_handler() {
	try {
		set_permissions();
	} catch ( \Sgdd\Vendor\Google_Service_Exception $e ) {
		if ( 'userRateLimitExceeded' === $e->getErrors()[0]['reason'] ) {
			wp_send_json( array( 'error' => esc_html__( 'The maximum number of requests has been exceeded. Please try again in a minute.', 'skaut-google-drive-documents' ) ) );
		} else {
			wp_send_json( array( 'error' => $e->getErrors()[0]['message'] ) );
		}
	} catch ( \Exception $e ) {
		wp_send_json( array( 'error' => $e->getMessage() ) );
	}
}

/**
 * Process Ajax request from JS to set permissions of files.
 */
function set_permissions() {
	check_ajax_referer( 'sgdd_block_js_permissions' );

	if ( ! isset( $_GET['folderType'] ) || ! isset( $_GET['fileId'] ) || '' === $_GET['fileId'] ) {
		if ( ! isset( $_GET['folderId'] ) || '' === $_GET['folderId'] ) {
			$root_path = \Sgdd\Admin\Options\Options::$root_path->get();
			$folder_id = end( $root_path );
		} else {
			$folder_id = sanitize_text_field( wp_unslash( $_GET['folderId'] ) );
		}

		set_permissions_in_folder( $folder_id );
	} else {
		set_file_permissions( sanitize_text_field( wp_unslash( $_GET['fileId'] ) ) );
	}
}

/**
 * Sets permissions of file on Google Drive
 *
 * @param string $file_id Google Drive id of file which permissions will be modified.
 */
function set_file_permissions( $file_id ) {
	$service           = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$domain_permission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
		array(
			'role' => 'reader',
			'type' => 'anyone',
		)
	);
	$service->permissions->create( $file_id, $domain_permission, array( 'supportsTeamDrives' => true ) );
}

/**
 * Sets permissions of all files in folder.
 *
 * @param string $folder_id Google Drive id of folder in which permissions of all files will be modified.
 *
 * @throws \Sgdd\Vendor\Google_Service_Exception An error occured.
 *
 * @return array NULL
 */
function set_permissions_in_folder( $folder_id ) {
	$service    = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $folder_id . "' in parents and trashed = false",
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

	$service         = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$user_permission = new \Sgdd\Vendor\Google_Service_Drive_Permission(
		array(
			'role' => 'reader',
			'type' => 'anyone',
		)
	);

	$index = 0;

	$service->getClient()->setUseBatch( true );
	$batch = $service->createBatch();

	foreach ( $response as $file ) {
		$request = $service->permissions->create( $file['id'], $user_permission, array( 'supportsTeamDrives' => true ) );
		$batch->add( $request, 'perm' . $index );

		$index++;
	}

	$results = $batch->execute();
	return $results;
}

/**
 * Fetches the content of a Google Drive folder
 *
 * @param string $folder_id The ID of the folder.
 * @param string $order_by The ordering to use.
 *
 * @throws \Exception A error occured.
 */
function fetch_folder_content( $folder_id, $order_by ) {
	$service    = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$page_token = null;

	switch ( $order_by ) {
		case 'name_asc':
			$order_by = 'name';
			break;

		case 'name_dsc':
			$order_by = 'name desc';
			break;

		case 'time_asc':
			$order_by = 'modifiedTime';
			break;

		case 'time_dsc':
			$order_by = 'modifiedTime desc';
			break;

		default:
			$order_by = '';
	}

	do {
		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $folder_id . "' in parents and trashed = false",
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
				'pageToken'                 => $page_token,
				'pageSize'                  => 1000,
				'fields'                    => 'files',
				'orderBy'                   => 'folder' . ( ! empty( $order_by ) ? ',' . $order_by : '' ),
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

/**
 * Function that construct HTML for displaying folder content.
 *
 * @param array  $content Content of folder to be displayed.
 * @param string $type Type of displaying folder: list or grid.
 * @param array  $arg User specified options for displaying folder.
 *
 * @return string HTML format for frontend.
 */
function build_result( $content, $type, $arg ) {
	$style = '';

	if ( empty( $content['files'] ) ) {
		return '<div class="notice notice-info">' . __( 'The selected folder does not contain any items.', 'skaut-google-drive-documents' ) . '</div>';
	}

	// build list table.
	if ( 'list' === $type ) {
		if ( array_key_exists( 'width', $arg ) ) {
			$style = ' style="width:' . parse_dimension( $arg['width'] ) . ';"';
		}

		$result = '<table class="sgdd-embedded-folder-list"' . $style . '><tbody>';

		foreach ( $content as $element ) {
			$result .= '<tr>
					<td><img src="' . $element['iconLink'] . '"></td>
					<td><a href="' . $element['webViewLink'] . '" target="_blank">' . $element['name'] . '</a></td>
				</tr>';
		}
	} else {
		// build grid table.
		$i    = 0;
		$cols = $arg['cols'];

		$style = ' style="table-layout:fixed; border-collapse:separate;';
		if ( array_key_exists( 'width', $arg ) ) {
			$style .= ' width:' . parse_dimension( $arg['width'] ) . ';';
		}
		$style .= '"';

		$result = '<table class="sgdd-embedded-folder-grid"' . $style . '><tbody>';

		foreach ( $content as $element ) {
			if ( 0 === ( $i % $cols ) ) {
				$result .= '<tr>';
			}

			if ( ! boolval( $element['hasThumbnail'] ) || 1 === preg_match( '/\b(google-apps)/', $element['mimeType'] ) ) {
				$thumbnail = '<img src="' . preg_replace( '(16)', '128', $element['iconLink'] ) . '">';
			} else {
				$thumbnail = '<img src="' . $element['thumbnailLink'] . '">';
			}

			$result .= '<td><div class="sgdd-embedded-folder-element">
					<a href="' . $element['webViewLink'] . '" target="_blank">
						<div class="sgdd-embedded-folder-thumbnail">' . $thumbnail . '</div>
						<div class="sgdd-embedded-folder-caption">' . $element['name'] . '</div>
					</a>
				</div></td>';

			if ( ( $i % $cols === $cols - 1 ) || ( ( $i + 1 ) === count( $content ) ) ) {
				$result .= '</tr>';
			}

			$i++;
		}
	}

	$result .= '</tbody></table>';

	return $result;
}

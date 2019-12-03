<?php
/**
 * Handles root folder selection
 *
 * @package SGDD
 * @since 1.0.0
 */
namespace Sgdd\Admin\SettingsPages\Basic\PathSelection;

if ( ! is_admin() ) {
	return;
}

/**
 * Register actions into WordPress
 */
function register() {
	add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\add_settings' );
	add_action( 'admin_enqueue_scripts', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\register_script' );
	add_action( 'wp_ajax_listDrive', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\ajax_handler' );
}

/**
 * Add settings fields to path select section of basic settings page
 */
function add_settings() {
	add_settings_section( 'sgdd_path_select', __( 'Step 2: Root Path Selection', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\display', 'sgdd_basic' );

	\Sgdd\Admin\Options\Options::$root_path->register();
}

/**
 * Register script that handles Ajax request to list folders
 *
 * @param $hook Dynamic hook which refers to plugin settings page
 */
function register_script( $hook ) {
	\Sgdd\enqueue_style( 'sgdd_path_selection_css', '/admin/css/path-selection.css' );
	if ( 'toplevel_page_sgdd_basic' === $hook ) {
		\Sgdd\enqueue_script( 'sgdd_path_selection_ajax', '/admin/js/path-selection.js', [ 'jquery' ] );
		wp_localize_script(
			'sgdd_path_selection_ajax',
			'sgddRootPathLocalize',
			[
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'sgdd_path_selection' ),
				'path'      => \Sgdd\Admin\Options\Options::$root_path->get(),
				'driveList' => esc_html__( 'Shared drive list', 'skaut-google-drive-documents' ),
			]
		);
	}
}

/**
 * Handels Ajax response from JS
 */
function ajax_handler() {
	try {
		drive_path_selection();
	} catch ( \Sgdd\Vendor\Google_Service_Exception $e ) {
		if ( 'userRateLimitExceeded' === $e->getErrors()[0]['reason'] ) {
			wp_send_json( [ 'error' => esc_html__( 'The maximum number of requests has been exceeded. Please try again in a minute.', 'skaut-google-drive-documents' ) ] );
		} else {
			wp_send_json( [ 'error' => $e->getErrors()[0]['message'] ] );
		}
	} catch ( \Exception $e ) {
		wp_send_json( [ 'error' => $e->getMessage() ] );
	}
}

/**
 * Fetch availible folders on gdrive
 */
function drive_path_selection() {
	check_ajax_referer( 'sgdd_path_selection' );

	if ( ! current_user_can( 'manage_options' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-documents' ) );
	}

	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$path    = isset( $_GET['path'] ) ? $_GET['path'] : [];

	$result = [
		'pathNames' => [],
		'content'   => [],
	];

	if ( ! empty( $path ) ) {
		$result['pathNames'] = get_path_name( $service, $path );
		$result['content']   = get_drive_content( $service, end( $path ) );
	} else {
		$result['pathNames'] = [ 'Shared Drive List' ];
		$result['content']   = get_drives( $service );
	}

	wp_send_json( $result );
}

/**
 * Translates grive folder path to folder name
 *
 * @param $service Object of Google Drive Client
 * @param $path Array of folder ids specified as root
 * @return array List of folder names
 */
function get_path_name( $service, $path ) {
	$result = [];

	if ( count( $path ) > 0 ) {
		if ( 'root' === $path[0] ) {
			$result[] = __( 'My Drive', 'skaut-google-drive-documents' );
		} else {
			$response = $service->drives->get( $path[0], [ 'fields' => 'name' ] );
			$result[] = $response->getName();
		}
	}

	foreach ( array_slice( $path, 1 ) as $path_element ) {
		$get_options = [
			'supportsAllDrives' => true,
			'fields'            => 'name',
		];

		$response = $service->files->get( $path_element, $get_options );
		$result[] = $response->getName();
	}

	return $result;
}

/**
 * Fetch content of folder on gdrie
 *
 * @param $service Object of Google Drive Client
 * @param $root Root folder id specified in settings
 * @return array List of content in specified folder
 */
function get_drive_content( $service, $root ) {
	$result     = [];
	$page_token = null;

	do {

		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $root . "' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
				'pageToken'                 => $page_token,
				'pageSize'                  => 1000,
				'fields'                    => 'nextPageToken, files(id, name)',
			)
		);

		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		foreach ( $response->files as $file ) {
				$result[] = [
					'pathName' => $file->getName(),
					'pathId'   => $file->getId(),
				];
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $result;
}

/**
 * Fetch all google drive from account (main drive and all shared drives connected to account)
 *
 * @param $service Object of Google Drive Client
 * @return array List of all gdrives connected to account
 */
function get_drives( $service ) {
	$result     = [
		[
			'pathName' => __( 'My Drive', 'skaut-google-drive-documents' ),
			'pathId'   => 'root',
		],
	];
	$page_token = null;

	do {
		$response = $service->drives->listDrives(
			array(
				'pageToken' => $page_token,
				'pageSize'  => 100,
				'fields'    => 'nextPageToken, drives(id, name)',
			)
		);

		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		foreach ( $response->getDrives() as $drive ) {
				$result[] = [
					'pathName' => $drive->getName(),
					'pathId'   => $drive->getId(),
				];
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $result;
}

/**
 * Renders folder selection section of basic settings page
 */
function display() {
	?>

	<div id="rootPath">
		<table class="widefat">
			<thead>
				<tr>
					<th class="tablePath"></th>
				</tr>
			</thead>
			<tbody class="tableBody">
				<tr class="loadingCircle"></tr>
			</tbody>
			<tfoot>
				<tr>
					<th class="tablePath"></th>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php
	\Sgdd\Admin\Options\Options::$root_path->display();
}

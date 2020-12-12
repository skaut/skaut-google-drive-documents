<?php
/**
 * Gutenberg Editor functions
 *
 * Function that controll Gutenberg editor blocks
 *
 * @package SGDD
 * @since 1.0.0
 */

namespace Sgdd\Admin\Editor;

if ( ! is_admin() ) {
	return;
}

/**
 * Registers actions into WordPress.
 */
function register() {
	add_action( 'wp_ajax_selectFile', '\\Sgdd\\Admin\\Editor\\ajax_handler' );
}

/**
 * Handles Ajax response from JS
 */
function ajax_handler() {
	try {
		file_selection();
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
 * Fetch result of folder content and returns to ajax
 *
 * @throws \Exception An error occured.
 */
function file_selection() {
	check_ajax_referer( 'sgdd_block_js' );

	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-documents' ) );
	}
	if ( false === get_option( 'sgdd_access_token' ) ) {
		// translators: 1: Start of link to the settings 2: End of link to the settings.
		throw new \Exception( sprintf( esc_html__( 'Google Drive Documents hasn\'t been granted permissions yet. Please %1$sconfigure%2$s the plugin and try again.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdd_basic' ) ) . '">', '</a>' ) );
	}

	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$path    = isset( $_GET['idsPath'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET['idsPath'] ) ) : array();

	if ( 0 < count( $path ) ) {
		$result = get_folder_content( $service, end( $path ) );
	} else {
		$result = get_folder_content( $service );
	}

	wp_send_json( $result );
}

/**
 * Returns folder content
 *
 * @param \Sgdd\Vendor\Google_Service_Drive $service Object of Google Drive Client.
 * @param string                            $folder Folder id.
 *
 * @throws \Exception An error occured.
 *
 * @return array List of content in specified folder
 */
function get_folder_content( $service, $folder = null ) {
	if ( ! isset( $folder ) ) {
		$root_path = \Sgdd\Admin\Options\Options::$root_path->get();
		$folder    = end( $root_path );
	}

	$result     = array();
	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                         => "'" . $folder . "' in parents and trashed = false",
				'supportsAllDrives'         => true,
				'includeItemsFromAllDrives' => true,
				'pageToken'                 => $page_token,
				'pageSize'                  => 1000,
				'fields'                    => 'nextPageToken, files(id, name, mimeType)',
			)
		);

		if ( $response instanceof \Sgdd\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		foreach ( $response->getFiles() as $file ) {
				$result[] = array(
					'fileName' => $file->getName(),
					'fileId'   => $file->getId(),
					'folder'   => $file->getMimeType() === 'application/vnd.google-apps.folder' ? true : false,
				);
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $result;
}

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

function register() {
	add_action( 'wp_ajax_selectFile', '\\Sgdd\\Admin\\Editor\\ajax_handler' );
}

function ajax_handler() {
	try {
		file_selection();
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

function file_selection() {
	check_ajax_referer( 'sgdd_block_js' );

	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-documents' ) );
	}
	if ( ! get_option( 'sgdd_access_token' ) ) {
		// translators: 1: Start of link to the settings 2: End of link to the settings
		throw new \Exception( sprintf( esc_html__( 'Google Drive Documents hasn\'t been granted permissions yet. Please %1$sconfigure%2$s the plugin and try again.', 'skaut-google-drive-documents' ), '<a href="' . esc_url( admin_url( 'admin.php?page=sgdd_basic' ) ) . '">', '</a>' ) );
	}

	$service = \Sgdd\Admin\GoogleAPILib\get_drive_client();
	$path    = isset( $_GET['path'] ) ? $_GET['path'] : [];

	if ( 0 < count( $path ) ) {
		$folder_id = get_folder_id( $service, $path );
		$result    = get_folder_content( $service, $folder_id );
	} else {
		$result = get_folder_content( $service );
	}

	wp_send_json( $result );
}

function get_folder_id( $service, array $path, $root = null ) {
	if ( ! isset( $root ) ) {
		$root = end( \Sgdd\Admin\Options\Options::$root_path->get() );
	}

	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                     => "'" . $root . "' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
				'supportsTeamDrives'    => true,
				'includeTeamDriveItems' => true,
				'pageToken'             => $page_token,
				'pageSize'              => 1000,
				'fields'                => 'nextPageToken, files(id, name, mimeType)',
			)
		);

		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		foreach ( $response->files as $file ) {
			if ( $file->getName() === $path[0] ) {
				array_shift( $path );
				if ( 0 < count( $path ) ) {
					get_folder_id( $service, $path, $file->getId() );
				}
				return $file->getId();
			}
		}
	} while ( null !== $page_token );
	throw new \Exception( esc_html__( 'No such directory found - it may have been deleted or renamed. ', 'skaut-google-drive-documents' ) );
}

function get_folder_content( $service, $folder = null ) {
	if ( ! isset( $folder ) ) {
		$folder = end( \Sgdd\Admin\Options\Options::$root_path->get() );
	}

	$result     = [];
	$page_token = null;

	do {
		$response = $service->files->listFiles(
			array(
				'q'                     => "'" . $folder . "' in parents",
				'supportsTeamDrives'    => true,
				'includeTeamDriveItems' => true,
				'pageToken'             => $page_token,
				'pageSize'              => 1000,
				'fields'                => 'nextPageToken, files(id, name, mimeType)',
			)
		);

		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}

		foreach ( $response->files as $file ) {
				$result[] = [
					'fileName' => $file->getName(),
					'fileId'   => $file->getId(),
					'folder'   => $file->getMimeType() === 'application/vnd.google-apps.folder' ? true : false,
				];
		}

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$page_token = $response->pageToken;
	} while ( null !== $page_token );

	return $result;
}

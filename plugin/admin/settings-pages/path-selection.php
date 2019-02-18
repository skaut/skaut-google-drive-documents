<?php
namespace Sgdd\Admin\SettingsPages\PathSelection;

function register() {
  add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\PathSelection\\addSettings' );
}

function addSettings() {
  add_settings_section( 'sgdd_path_select', __( 'Step 2: Root Path Selection', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\PathSelection\\display', 'sgdd_settings' );
}

function display() {  
  $service = \Sgdd\Admin\GoogleAPILib\getDriveClient();
  
  $ret        = [];
  $page_token = null;
  $root = 'root';
	do {
		$params   = [
			'q'                     => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder"',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id, name)',
		];
		$response = $service->files->listFiles( $params );
		if ( $response instanceof \Sgdd\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$ret[] = [
				'name' => $file->getName(),
				'id'   => $file->getId(),
			];
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );

  foreach ( $ret as $res ) {
    var_dump ($res);
    echo '<br>';
  }

  echo count($ret);
}
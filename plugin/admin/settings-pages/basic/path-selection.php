<?php
namespace Sgdd\Admin\SettingsPages\Basic\PathSelection;

if ( ! is_admin() ) {
  return;
}

function register() {
  add_action( 'admin_init', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\addSettings' );
  add_action( 'admin_enqueue_scripts', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\registerScript' );
  add_action( 'wp_ajax_listDrive', '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\ajaxHandler' );
}

function addSettings() {
  add_settings_section( 'sgdd_path_select', __( 'Step 2: Root Path Selection', 'skaut-google-drive-documents' ), '\\Sgdd\\Admin\\SettingsPages\\Basic\\PathSelection\\display', 'sgdd_basic' );

  \Sgdd\Admin\Options\Options::$rootPath->register();
}

function registerScript( $hook ) {
  \Sgdd\enqueue_style( 'sgdd_path_selection_css', '/admin/css/path-selection.css' );
  if ( $hook === 'toplevel_page_sgdd_basic' ) {
    \Sgdd\enqueue_script( 'sgdd_path_selection_ajax', '/admin/js/path-selection.js', [ 'jquery' ] );
    wp_localize_script(
      'sgdd_path_selection_ajax',
      'sgddRootPathLocalize',
      [
        'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
        'nonce'         => wp_create_nonce( 'sgddPathSelection' ),
        'path'          => \Sgdd\Admin\Options\Options::$rootPath->get(),
        'teamDriveList' => esc_html__( 'Team drive list', 'skaut-google-drive-documents' ),
      ]
    );
  }
}

function ajaxHandler() {
  try {
    drivePathSelection();
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

function drivePathSelection() {
  check_ajax_referer( 'sgddPathSelection' );

  if ( ! current_user_can( 'manage_options' ) ) {
    throw new \Exception( esc_html__( 'Insufficient role for this action.', 'skaut-google-drive-documents' ) );
  }

  $service = \Sgdd\Admin\GoogleAPILib\getDriveClient();
  $path = isset( $_GET['path'] ) ? $_GET['path'] : [];

  $result = [
    'pathNames' => [],
    'content'  => [],
  ];

  if ( ! empty ( $path ) ) {
    $result['pathNames'] = getPathName( $path, $service );
    $result['content']   = getDriveContent( $service, end( $path ) );
  } else {
    $result['pathNames'] = [ 'Team Drive List' ];
    $result['content']   = getTeamDrives( $service );
  }

  wp_send_json( $result );
}

function getPathName( $path, $service ) {
  $result = [];

  if ( count ( $path ) > 0 ) {
    if ( $path[0] === 'root' ) {
      $result[] = __( 'My Drive', 'skaut-google-drive-documents' );
    } else {
      $response = $service->teamdrives->get( $path[0], [ 'fields' => 'name' ] );
      $result[] = $response->getName();
    }
  }

  foreach(array_slice( $path, 1 ) as $pathElement) {
    $response = $service->files->get( $pathElement, [ 'supportsTeamDrives' => true, 'fields' => 'name' ] );
    $result[] = $response->getName();
  }

  return $result;
}

function getDriveContent( $service, $root ) {
  $result = [];
  $pageToken = null;

  do {
    $response = $service->files->listFiles(array(
        'q' 										=> "'" . $root . "' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
        'supportsTeamDrives'    => true,
        'includeTeamDriveItems' => true,
        'pageToken' 						=> $pageToken,
        'pageSize'  						=> 1000,
        'fields'    						=> 'nextPageToken, files(id, name)',
    ));

    if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
      throw $response;
    }

    foreach ($response->files as $file) {
        $result[] = [
          'pathName' => $file->getName(),
          'pathId'   => $file->getId(),
        ];
    }

    $pageToken = $response->pageToken;
  } while ($pageToken != null);

  return $result;
}

function getTeamDrives( $service ) {
  $result = [
    [
      'pathName' => __( 'My Drive', 'skaut-google-drive-documents' ),
      'pathId'   => 'root',
    ],
  ];
  $pageToken = null;

  do {
    $response = $service->teamdrives->listTeamdrives(array(
        'pageToken' => $pageToken,
        'pageSize'  => 100,
        'fields'    => 'nextPageToken, teamDrives(id, name)',
    ));

    if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
      throw $response;
    }

    foreach ($response->getTeamDrives() as $drive) {
        $result[] = [
          'pathName' => $drive->getName(),
          'pathId'   => $drive->getId(),
        ];
    }

    $pageToken = $response->pageToken;
  } while ($pageToken != null);

  return $result;
}

function display() {  
?>

  <div id="rootPath">
    <div id="loadingCircle"></div>
    <table id="widefat fixed">
      <thead>
        <tr>
          <th class="tablePath"></th>
        </tr>
      </thead>
      <tbody class="tableBody"></tbody>
      <tfoot>
        <tr>
          <th class="tablePath"></th>
        </tr>
      </tfoot>
    </table>
  </div>

<?php
  \Sgdd\Admin\Options\Options::$rootPath->display();
}
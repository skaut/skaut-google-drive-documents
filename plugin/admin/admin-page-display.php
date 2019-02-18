<?php
namespace Sgdd\Admin\AdminPageDisplay;

function display() {
  settings_errors();
?>

  <div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form action="options.php" method="post">
  <?php settings_fields( 'sgdd_settings' ); ?>
    <div class="moja_setting_section">
      <?php do_settings_sections( 'sgdd_settings' ); ?>
    </div>
  <?php submit_button( __( 'Save Settings', 'skaut-google-drive-documents' ) ); ?>
  </form>
  </div>

<?php
}
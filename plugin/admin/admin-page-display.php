<?php

namespace Sgdd\Admin\AdminPageDisplay;

function display() {
?>

  <div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <form action="options.php" method="post">
  <?php
    settings_fields( 'sgdd_settings' );
    do_settings_sections( 'sgdd_settings' );
    submit_button( esc_html__( 'Save Settings' ) );
  ?>
  </form>
  </div>

<?php
}
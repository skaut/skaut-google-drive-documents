<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 */
?>

<div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
  settings_fields( 'sgdd_setting' );
  do_settings_sections( 'sgdd_setting' );
  submit_button( 'Save Settings' );
 ?>
 </form>
 </div>

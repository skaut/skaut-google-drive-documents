<?php
namespace Sgdd\Admin\Block;

if ( ! is_admin() ) {
	return;
}

function register() {
  if ( function_exists( 'register_block_type' ) ) {
    add_action( 'init', '\\Sgdd\\Admin\\Block\\add_block' );
  }
}

function add_block() {
  \Sgdd\enqueue_script( 'sgdd_block_js', '/admin/js/block.js', [ 'wp-blocks', 'wp-components', 'wp-editor', 'wp-element' ] );

  register_block_type(
    'sakut-google-drive-documents/block',
    [
      'editor_script' => 'sgdd_block_js',
      'render_callback' => '\\Sgdd\\Admin\\Block\\display',
    ]
  );
}

function display() {
  echo 'Hello world.';
}

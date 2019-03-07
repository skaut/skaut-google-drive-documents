jQuery( document ).ready( function( $ ) {
  function listGdriveDir( path ) {
		$( '#sgdd_root_selection_body' ).html( '' );
		$( '#submit' ).attr( 'disabled', 'disabled' );
		$.get( sgdgRootpathLocalize.ajax_url, {
			_ajax_nonce: sgdgRootpathLocalize.nonce, // eslint-disable-line camelcase
			action: 'list_gdrive_dir',
			path: path
			}, function( data ) {
				if ( data.directories ) {
					success( path, data );
				} else if ( data.error ) {
					error ( data.error );
				}
			});
}
});
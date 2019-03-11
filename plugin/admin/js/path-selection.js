jQuery( document ).ready( function( $ ) {
	function showDir( path ) {
			$.ajax({ 
				url: sgddRootPathLocalize.ajax_url,
				type: "GET",
				data: {
					action: 'listDrive',
					path: path,
					_ajax_nonce: sgddRootPathLocalize.nonce
				},
				beforeSend: function() {
					$( '#loadingCircle' ).fadeIn();
				},
				success: function( response ) {	
					$( '#loadingCircle' ).fadeOut();
					$( '#rootPath' ).fadeIn();
				},
				error: function() {
					alert("Error");
				}
			});
	}

	showDir( sgddRootPathLocalize.path );
});
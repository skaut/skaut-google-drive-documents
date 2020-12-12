'use strict';
/* global sgddRootPathLocalize */

( function ( $ ) {
	function showDir( path ) {
		/* If path is not initialized */
		if ( ! ( path instanceof Array ) ) {
			path = [];
		}

		$( '#rootPath tbody tr' ).not( '.loading-circle' ).fadeOut();
		$( '.loading-circle' ).fadeIn( 'slow' );

		$.ajax( {
			url: sgddRootPathLocalize.ajaxUrl,
			type: 'GET',
			data: {
				action: 'listDrive',
				path,
			},
			success( response ) {
				let html = '';
				let i;

				$( '.loading-circle' ).fadeOut();
				$( '#rootPath tbody tr' )
					.not( '.loading-circle' )
					.fadeIn( 'slow' );

				/* Print path */
				if ( 0 < path.length ) {
					html +=
						'<a data-id="">' +
						sgddRootPathLocalize.driveList +
						'</a> > ';
					for ( i = 0; i < response.pathNames.length; i++ ) {
						if ( 0 < i ) {
							html += ' > ';
						}
						html +=
							'<a data-id="' +
							path[ i ] +
							'">' +
							response.pathNames[ i ] +
							'</a>';
					}
				} else {
					html +=
						'<a data-id="">' +
						sgddRootPathLocalize.driveList +
						'</a>';
					$( '#submit' ).attr( 'disabled', 'disabled' );
				}
				$( '.table-path' ).html( html );

				/* Up directory dots */
				html = '<tr class="loading-circle"></tr>';
				if ( 0 < path.length ) {
					html += '<tr><td class="row-title"><label>..</label></tr>';
					$( '.tableBody' ).html( html );
				}

				/* List dir content */
				for ( i = 0; i < response.content.length; i++ ) {
					html += '<tr class="';

					if ( 0 === i % 2 ) {
						html += 'alternate';
					}

					html +=
						'"><td class="row-title"><label data-id="' +
						response.content[ i ].pathId +
						'">' +
						response.content[ i ].pathName +
						'</label>';
				}
				$( '.tableBody' ).html( html );

				$( '.tableBody label' ).click( function () {
					dirClick( path, this );
					$( '#submit' ).removeAttr( 'disabled' );
				} );

				$( '.table-path a' ).click( function () {
					pathClick( path, this );
					$( '#submit' ).removeAttr( 'disabled' );
				} );

				$( '#sgdd_root_path' ).val( JSON.stringify( path ) );
			},
			error( response ) {
				const html =
					'<div class="notice notice-error"><p>' +
					response.responseText +
					'</p></div>';
				$( '#rootPath' ).replaceWith( html );
			},
		} );
	}

	/**
	 * Reloads table content on path click
	 *
	 * @param {string[]} path - Array of folder ids from root
	 * @param {*} element
	 */
	function pathClick( path, element ) {
		const elementIndex = path.indexOf( $( element ).data( 'id' ) );
		const newPath = path.slice( 0, elementIndex + 1 );

		showDir( newPath );
	}

	/**
	 * Reloads table content on directory click
	 *
	 * @param {string[]} path - Array of folder ids from root
	 * @param {*} element
	 */
	function dirClick( path, element ) {
		const newID = $( element ).data( 'id' );

		if ( newID ) {
			path.push( newID );
		} else {
			path.pop();
		}

		showDir( path );
	}

	showDir( sgddRootPathLocalize.path );
} )( jQuery );

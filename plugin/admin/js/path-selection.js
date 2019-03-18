'use strict';

jQuery( document ).ready( function( $ ) {
  function showDir( path ) {
    $.ajax({ 
      url: sgddRootPathLocalize.ajaxUrl,
      type: "GET",
      data: {
        action: 'listDrive',
        path: path,
        _ajax_nonce: sgddRootPathLocalize.nonce
      },
      beforeSend: function() {
        $( '#loadingCircle' ).fadeIn();
        $( '#submit' ).attr( 'disabled', 'disabled' );
      },
      success: function( response ) {
        var html = '';

        /* Loading animation */
        $( '#loadingCircle' ).fadeOut();
        $( '#tableBody' ).fadeIn();

        /* debug log */
        console.log(response);

        /* Print path */
        if ( path.length > 0 ) {
          html += '<a data-id="">' + sgddRootPathLocalize.teamDriveList + '</a> > ';
          for (var i = 0; i < response.pathNames.length; i++) {
            if (i > 0) {
              html += ' > ';
            }
            html += '<a data-id="' + path[i] + '">' + response.pathNames[i] + '</a>';
          }
        } else {
          html += '<a data-id="">' + sgddRootPathLocalize.teamDriveList + '</a>';
          $( '#submit' ).attr( 'disabled', 'disabled' );
        }
        $( '.tablePath' ).html( html );

        /* Up directory dots */
        html = '';
        if ( path.length > 0 ) {
          html += '<tr><td class="row-title"><label>..</label></tr>';
          $( '.tableBody' ).html( html );
        }

        /* List dir content */
        for (var i = 0; i < response.content.length; i++) {
          html += '<tr class="';

          if (i % 2 == 0) {
            html += 'alternate'
          }

          html += '"><td class="row-title"><label data-id="' + response.content[i].pathId + '">' + response.content[i].pathName + '</label>';
        }
        $( '.tableBody' ).html( html );

        $( '.tableBody label' ).click( function() {
          dirClick( path, this );
          $( '#submit' ).removeAttr( 'disabled' );
        });

        $( '.tablePath a' ).click( function() {
          pathClick( path, this );
          $( '#submit' ).removeAttr( 'disabled' );
        });

        $( '#sgdd_root_path' ).val( JSON.stringify( path ) );
      },
      error: function(response) {
        alert("Error");
        console.log(response);
      }
    });
  }

  function pathClick( path, element ) {
    var elementIndex = path.indexOf( $( element ).data( 'id' ) );
    var newPath = path.slice( 0, elementIndex + 1 );

    showDir( newPath );
  }

  function dirClick( path, element ) {
    var newID = $( element ).data( 'id' );

    if ( newID ) {
      path.push( newID );
    } else {
      path.pop();
    }

    showDir( path );
  }

  showDir( sgddRootPathLocalize.path );
});
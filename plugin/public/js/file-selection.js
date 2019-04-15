'use strict';

var el = wp.element.createElement;
var SgddFileSelection = function( props ) {
	this.props = props;
	this.state = { error: undefined, list: undefined };
};

SgddFileSelection.prototype = Object.create( wp.element.Component.prototype );
SgddFileSelection.prototype.componentDidMount = function() {
	this.ajax();
};

SgddFileSelection.prototype.ajax = function() {
	var that = this;
	$.ajax({
		url: sgddBlockJsLocalize.ajaxUrl,
		type: 'GET',
		data: {
			action: 'selectFile',
			path: this.getAttribute( 'path' ),
			_ajax_nonce: sgddBlockJsLocalize.nonce // eslint-disable-line camelcase
		},
		beforeSend: function() {

			//TODO
			/*$( '#loadingCircle' ).fadeIn();
			$( '#submit' ).attr( 'disabled', 'disabled' );*/
		},
		success: function( response ) {
			if ( response.error ) {
				that.setState({error: response.error});
			} else {
				that.setState({list: response});
			}
		},
		error: function( response ) {
			that.setState({error: response.error});
		}
	});
};

SgddFileSelection.prototype.render = function() {
	var that = this;
	var children = [];
	var path = [ el( 'a', {onClick: function( e ) {
		that.pathClick( that, e );
	}}, sgddBlockJsLocalize.root ) ];
	var i, id;

	if ( this.state.error ) {
		return el( 'div', { class: 'notice notice-error' }, el( 'p', {}, this.state.error ) );
	}

	if ( this.state.list ) {
		if ( 0 < this.getAttribute( 'path' ).length ) {
			children.push( el( 'tr', {}, el( 'td', {}, el( 'label', { onClick: function( e ) {
				that.upClick( that );
			}}, '..' ) ) ) );
		}

		for ( i = 0; i < this.state.list.length; i++ ) {
			if ( this.state.list[i].folder ) {
				children.push( el( 'tr', { class: 'folder' }, el( 'td', {}, el( 'label', { onClick: function( e ) {
					that.folderClick( that, e );
				}}, this.state.list[i].fileName ) ) ) );
			} else {
				id = this.state.list[i].fileId;

				children.push( el( 'tr', { class: 'file' }, el( 'td', {}, el( 'label', { onClick: function( e ) {
					that.fileClick( that, id );
				}}, this.state.list[i].fileName ) ) ) );
			}
		}

		for ( i = 0; i < this.getAttribute( 'path' ).length; i++ ) {
			path.push( ' > ' );
			path.push( el( 'a', { 'data-id': this.getAttribute( 'path' )[i], onClick: function( e ) {
				that.path( that, e );
			}}, this.getAttribute( 'path' )[i]) );
		}
	}

	return el( wp.element.Fragment, {}, [
		el( 'table', { class: 'widefat' }, [
			el( 'thead', {},
				el( 'tr', {},
					el( 'th', {}, path )
				)
			),

			el( 'tbody', {}, children ),

			el( 'tfoot', {},
				el( 'tr', {},
					el( 'th', {}, path )
				)
			)
		])
	]);
};

SgddFileSelection.prototype.getAttribute = function( name ) {
	return this.props.attributes[name];
};

SgddFileSelection.prototype.setAttribute = function( name, value ) {
	var attr = {};
	attr[name] = value;
	this.props.setAttributes( attr );
};

SgddFileSelection.prototype.upClick = function( that ) {
	var path;

	path = that.getAttribute( 'path' ).slice( 0, that.getAttribute( 'path' ).length - 1 );
	that.setAttribute( 'path', path );
	that.setState({ error: undefined, list: undefined }, that.ajax );
};

SgddFileSelection.prototype.folderClick = function( that, e ) {
	var newFolder = $( e.currentTarget ).html();
	var path;

	path = that.getAttribute( 'path' ).concat( newFolder );
	that.setAttribute( 'path', path );
	that.setState({ error: undefined, list: undefined }, that.ajax );
};

SgddFileSelection.prototype.fileClick = function( that, fileId ) {
	that.setAttribute( 'fileId', fileId );
	that.setState({ error: undefined, list: undefined }, that.ajax );
};

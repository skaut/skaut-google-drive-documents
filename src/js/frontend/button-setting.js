'use strict';

const SgddButtonSetting = function ( props ) {
	this.props = props;
	this.name = props.name;
};
SgddButtonSetting.prototype = Object.create( wp.element.Component.prototype );
SgddButtonSetting.prototype.render = function () {
	const that = this;
	const el = wp.element.createElement;
	return el( 'div', { className: 'sgdd-block-settings-row' }, [
		el( 'input', {
			className: 'sgdd-block-settings-button button button-primary',
			key: 'permButton',
			type: 'button',
			value: sgddBlockJsLocalize[ this.name ], //eslint-disable-line no-undef
			onClick() {
				that.ajax();
			},
		} ),
	] );
};

SgddButtonSetting.prototype.ajax = function () {
	jQuery.ajax( {
		url: sgddBlockJsLocalize.ajaxUrl, //eslint-disable-line no-undef
		type: 'GET',
		data: {
			action: 'setPermissions',
			fileId: this.getAttribute( 'fileId' ),
			folderId: this.getAttribute( 'folderId' ),
			folderType: this.getAttribute( 'folderType' ),
			_ajax_nonce: sgddBlockJsLocalize.noncePerm, // eslint-disable-line camelcase, no-undef
		},
		success() {
			//handle success
			const el = jQuery( '.sgdd-block-settings-button' );
			const originalColor = el.css( 'background' );

			el.prop( 'value', 'Success' ).css( 'background-color', '#5cb85c' );
			setTimeout( function () {
				el.prop( 'value', 'Set permissions' ).css(
					'background',
					originalColor
				);
			}, 3000 );
		},
		error() {
			//handle errors
			const el = jQuery( '.sgdd-block-settings-button' );
			const originalColor = el.css( 'background' );

			el.prop( 'value', 'Error' ).css( 'background-color', '#d9534f' );
			setTimeout( function () {
				el.prop( 'value', 'Set permissions' ).css(
					'background',
					originalColor
				);
			}, 3000 );
		},
	} );
};

SgddButtonSetting.prototype.getAttribute = function ( name ) {
	return this.props.block.props.attributes[ name ];
};

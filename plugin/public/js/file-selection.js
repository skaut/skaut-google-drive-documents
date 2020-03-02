'use strict';

var el = wp.element.createElement;
var SgddFileSelection = function (props) {
	this.props = props;
	this.state = { error: undefined, list: undefined };
};

SgddFileSelection.prototype = Object.create(wp.element.Component.prototype);
SgddFileSelection.prototype.componentDidMount = function () {
	this.ajax();
};

SgddFileSelection.prototype.ajax = function () {
	var that = this;
	jQuery.ajax({
		url: sgddBlockJsLocalize.ajaxUrl,
		type: 'GET',
		data: {
			action: 'selectFile',
			namesPath: this.getAttribute('namesPath'),
			idsPath: this.getAttribute('idsPath'),
			_ajax_nonce: sgddBlockJsLocalize.nonce // eslint-disable-line camelcase
		},
		beforeSend: function () { },
		success: function (response) {
			if (response.error) {
				that.setState({ error: response.error });
			} else {
				that.setState({ list: response });
			}
		},
		error: function (response) {
			that.setState({ error: response.error });
		}
	});
};

SgddFileSelection.prototype.render = function () {
	var that = this;
	var children = [];
	var namesPath = [el('a', {
		key: 'test', onClick: function (e) {
			that.pathClick(that, e);
		}
	}, sgddBlockJsLocalize.root)];
	var i;

	if (this.state.error) {
		return el('div', { className: 'notice notice-error' }, el('p', {}, this.state.error));
	}

	if (this.state.list) {
		for (i = 0; i < this.state.list.length; i++) {
			const id = this.state.list[i].fileId;

			if (this.state.list[i].folder) {
				children.unshift(el('tr', { className: 'folder', key: id }, el('td', {}, el('label', {
					onClick: function (e) {
						that.folderClick(that, e, id);
					}
				}, this.state.list[i].fileName))));
			} else {
				if (id === this.getAttribute('fileId')) {
					children.push(el('tr', { className: 'selected', key: id }, el('td', {}, el('label', {
						onClick: function () {
							that.fileClick(that, id);
						}
					}, this.state.list[i].fileName))));
				} else {
					children.push(el('tr', { className: 'file', key: id }, el('td', {}, el('label', {
						onClick: function () {
							that.fileClick(that, id);
						}
					}, this.state.list[i].fileName))));
				}
			}
		}

		for (i = 0; i < this.getAttribute('namesPath').length; i++) {
			namesPath.push(' > ');
			namesPath.push(el('a', {
				'data-id': this.getAttribute('namesPath')[i], key: i, onClick: function (e) {
					that.pathClick(that, e);
				}
			}, this.getAttribute('namesPath')[i]));
		}

		if (0 < this.getAttribute('namesPath').length) {
			children.unshift(el('tr', { key: -1 }, el('td', {}, el('label', {
				onClick: function (e) {
					that.upClick(that);
				}
			}, '..'))));
		}
	}

	return el(wp.element.Fragment, {}, [
		el(wp.editor.InspectorControls, {},
			el(SgddInspector, { block: this })
		),
		el('table', { className: 'widefat fixed' }, [
			el('thead', { key: 'thead' },
				el('tr', {},
					el('th', {}, namesPath)
				)
			),

			el('tbody', { key: 'tbody' }, children),

			el('tfoot', { key: 'tfoot' },
				el('tr', {},
					el('th', {}, namesPath)
				)
			)
		])
	]);
};

SgddFileSelection.prototype.getAttribute = function (name) {
	return this.props.attributes[name];
};

SgddFileSelection.prototype.setAttribute = function (name, value) {
	var attr = {};
	attr[name] = value;
	this.props.setAttributes(attr);
};

SgddFileSelection.prototype.upClick = function (that) {
	var namesPath;
	var idsPath;
	var folderId;

	if (that.getAttribute('namesPath').length == 1) {
		namesPath = [];
	} else {
		namesPath = that.getAttribute('namesPath').slice(0, that.getAttribute('namesPath').length - 1);
	}

	if (that.getAttribute('idsPath').length == 1) {
		idsPath = [];
	} else {
		idsPath = that.getAttribute('idsPath').slice(0, that.getAttribute('idsPath').length - 1);
	}

	if (idsPath.length < 1) {
		folderId = '';
	} else {
		folderId = idsPath[idsPath.length - 1];
	}

	that.setAttribute('namesPath', namesPath);
	that.setAttribute('idsPath', idsPath);
	that.setAttribute('folder', 'true');
	that.setAttribute('folderId', folderId);
	that.setAttribute('fileId', '');
	that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.pathClick = function (that, e) {
	var namesPath = that.getAttribute('namesPath');
	var idsPath = that.getAttribute('idsPath');

	namesPath = namesPath.slice(0, namesPath.indexOf($(e.currentTarget).data('id')) + 1);
	idsPath = idsPath.slice(0, namesPath.indexOf($(e.currentTarget).data('id')) + 1);

	that.setAttribute('namesPath', namesPath);
	that.setAttribute('idsPath', idsPath);
	that.setAttribute('folder', 'true');
	that.setAttribute('folderId', idsPath[idsPath.length - 1]);
	that.setAttribute('fileId', '');
	that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.folderClick = function (that, e, id) {
	let newFolder = $(e.currentTarget).html();
	var namesPath;
	var idsPath;

	namesPath = that.getAttribute('namesPath').concat(newFolder);
	idsPath = that.getAttribute('idsPath').concat(id);

	that.setAttribute('namesPath', namesPath);
	that.setAttribute('idsPath', idsPath);
	that.setAttribute('folderId', id);
	that.setAttribute('folder', 'true');
	that.setAttribute('fileId', '');
	that.setState({ error: undefined, list: undefined }, that.ajax);
};

SgddFileSelection.prototype.fileClick = function (that, fileId) {
	that.setAttribute('fileId', fileId);
	that.setAttribute('folder', 'false');
	that.setState({ error: undefined, list: undefined }, that.ajax);
};

'use strict';

const registerBlockType = wp.blocks.registerBlockType;

registerBlockType( 'skaut-google-drive-documents/block', {
	title: sgddBlockJsLocalize.blockName,
	description: sgddBlockJsLocalize.blockDescription,
	icon: 'welcome-add-page',
	category: 'common',
	keywords: [ 'docs', 'documents', 'drive' ],

	attributes: {
		namesPath: {
			type: 'array',
			default: [],
		},
		idsPath: {
			type: 'array',
			default: [],
		},
		fileId: {
			type: 'string',
			default: '',
		},
		folderId: {
			type: 'string',
			default: '',
		},
		embedWidth: {
			type: 'string',
			default: undefined,
		},
		embedHeight: {
			type: 'string',
			default: undefined,
		},
		listWidth: {
			type: 'string',
			default: undefined,
		},
		gridCols: {
			type: 'int',
			default: undefined,
		},
		folderType: {
			type: 'string',
			default: undefined,
		},
		orderBy: {
			type: 'string',
			default: undefined,
		},
	},

	edit: SgddFileSelection,
	save: renderFrontend,
} );

function renderFrontend() {
	return null;
}

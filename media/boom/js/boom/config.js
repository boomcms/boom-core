/**
@fileOverview CMS config, including default config for all widgets.
*/
/**
global boom config
@namespace
@name $.boom.config
*/

window.boomConfig =
	/** @lends $.boom.config */
	{

	/**
	@static
	@class
	*/
	logs : {
		/**
		@type boolean
		@default true
		*/
		show: true,
		/**
		@type boolean
		@default true
		*/
		showTimes: true,
		/**
		@type array
		@default []
		*/
		times: [],
		/**
		@type number
		@default 0
		*/
		totalTime: 0
	},

	/**
	@static
	@class
	*/
	editor : {
		/**
		@type string
		@default '/boom/css/boom.page.edit.css'
		*/
		stylesheetURL: '/media/boom/js/xing/editor.css',

		/**
		Wysiwyg editor. One of wysihtml5|tinymce
		@type string
		@default 'wysihtml5'
		*/
		name : 'wysihtml5'
	},

	/**
	@static
	@class
	*/
	datepicker : {
		/**
		@type boolean
		@default true
		*/
		mandatory: true,
		/**
		@type boolean
		@default true
		*/
		hideIfNoPrevNext: true,
		/**
		@type string
		@default 'fast'
		*/
		duration: 'fast',
		/**
		@type string
		@default 'fadeIn'
		*/
		showAnim: 'fadeIn',
		/**
		@type string
		@default 'dd MM yy'
		*/
		dateFormat: 'dd MM yy',
		/**
		@type boolean
		@default true
		*/
		showTime: true,
		/**
		@type string
		@default 'bottom'
		*/
		timePos: 'bottom'
	},

	/**
	@static
	@class
	*/
	tabs : {
		/**
		@type boolean
		@default false
		*/
		cache: false,
		/** @function */
		active: function(event, ui) {
			var url = $.data(ui.tab, 'load.tabs');
			if (url) {
				location.href = url;
				return false;
			}
			return true;
		},
		/** @function */
		activate: function(event, ui){
			//console.log( ui );
			//$(ui.tab.href.replace(/^.*?#/, '#')).show();
		}
	},

	/**
	@static
	@class
	*/
	tree : {
		/**
		@type boolean
		@default false
		*/
		parentAsFolder: false,
		/**
		@type boolean
		@default false
		*/
		icons: false,
		/**
		@type boolean
		@default false
		*/
		showEdit: false,
		/**
		@type boolean
		@default false
		*/
		showRemove: false,
		/**
		@type number
		@default -1
		*/
		maxSelected: -1,
		/**
		@type boolean
		@default true
		*/
		toggleSelected: true,
		/**
		@type string
		@default 'auto'
		*/
		width: 'auto',
		/**
		@type string
		@default 'auto'
		*/
		height: 'auto',
		/**
		@type string
		@default 'boom-tree-hitarea-hover'
		*/
		iconHitareaHover: 'boom-tree-hitarea-hover',
		/**
		@type boolean
		@default false
		*/
		preventDefault: true
	},

	/**
	@static
	@class
	*/
	sortable : {
		/**
		@type string
		@default 'parent'
		*/
		containment: 'parent',
		/**
		@type number
		@default 0.7
		*/
		opacity: 0.7,
		/**
		@type string
		@default 'y'
		*/
		axis: 'y'
	},

	/**
	@static
	@class
	*/
	browser: {
		/**
		@type string
		@default 'audit_time'
		*/
		sortby: 'audit_time',
		/**
		@type string
		@default 'desc'
		*/
		order: 'desc',
		/**
		@type string
		@default 'tag/0'
		*/
		defaultRoute: 'tag/0',
		/**
		@type Array
		@default []
		*/
		selected: [],
		/**
		@type Array
		@default []
		*/
		types: [],
		/**
		@type number
		@default 1
		*/
		page: 1,
		/**
		@type number
		@default 30
		*/
		perpage: 30,
		/**
		@type number
		@default 0
		*/
		excludeSmartTags: 0,
		/**
		@type string
		@default 'list'
		*/
		template: 'list',
		/**
		@type Object
		*/
		treeConfig: {
			border: true,
			height: 'auto',
			overflow: 'hidden',
			toggleSelected: false,
			width: 278
		}
	},

	/**
	@static
	@class
	@extends $.boom.config.browser
	*/
	browser_asset: {
		/**
		@type string
		@default 'last_modified'
		*/
		sortby: 'last_modified',
		/**
		@type string
		@default 'desc'
		*/
		order: 'desc',
		/**
		@type string
		@default 'assets'
		*/
		type: 'assets',
		/**
		@type Object
		*/
		treeConfig :
		/** @ignore */ {
			showEdit: true,
			showRemove: true,
			onEditClick: function(event){

				$.boom.items.group.edit(event);
			},
			onRemoveClick: function(event){

				$.boom.items.group.remove(event);
			}
		}
	},

	/**
	@static
	@class
	@extends $.boom.config.browser
	*/
	browser_people: {
		/**
		@type string
		@default 'name'
		*/
		sortby: 'name',
		/**
		@type string
		@default 'asc'
		*/
		order: 'asc',
		/**
		@type string
		@default 'group/0'
		*/
		defaultRoute: 'group/0',
		/**
		@type string
		@default 'people'
		*/
		type: 'people',
		/**
		@type Object
		*/
		treeConfig : {
			showEdit: true,
			showRemove: true
		}
	},

	/**
	Default options for file uploads
	https://github.com/blueimp/jQuery-File-Upload/wiki/Options
	@static
	@class
	*/
	upload: {
		/**
		@type string
		@default '/cms/assets/upload'
		*/
		url: '/cms/assets/upload',
		/**
		@type string
		@default 'json'
		*/
		dataType: 'json',
		/**
		@type boolean
		@default false
		*/
		singleFileUploads: false,
		/**
		@type Array
		@default []
		*/
		formData: [],

		limitMultiFileUploads: 5,
	}
};
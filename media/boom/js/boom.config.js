/** 
global boom config 
@namespace
@name $.boom.config
*/

window.boomConfig = {
	/** @lends $.boom.config */	

	/** @property */
	cachePageImages : [
		'/media/boom/img/ajax_load.gif',
		'/media/boom/img/cms/chunk_edit_icon.png',
		'/media/boom/js/tiny_mce/themes/advanced/skins/o2k7/img/button_bg_silver.png',
		'/media/boom/js/tiny_mce/themes/advanced/skins/o2k7/img/button_bg.png',
		'/media/boom/js/tiny_mce/themes/advanced/img/icons.gif'
	],

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
	errors : {
		/** 
		@type boolean
		@default true
		*/
		report: true
	},

	/** 
	@static
	@class 
	*/
	history : {
		/** 
		@type number
		@default 100
		*/
		checkInterval: 100
	},

	/** 
	@static
	@class 
	*/
	captions : {
		/** 
		@type string
		@default '.caption'
		*/
		captionTitleSelector: '.caption',
		/** 
		@type string
		@default '.caption-overlay'
		*/
	captionOverlaySelector: '.caption-overlay',
		/** 
		@type number
		@default 0
		*/
		showSpeed: 0,
		/** 
		@type number
		@default 0
		*/
		hideSpeed: 0,
		/** 
		@type number
		@default 0.8
		*/
		opacityOverlay: 0.8
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
		@type string
		@default '/media/boom/js/boom.tinymce.js'
		*/
		path : '/media/boom/js/boom.wysihtml5.js'
	},

	/** 
	@static
	@class 
	*/
	dialog : {
		/** 
		@type string
		@default 'auto'
		*/
		width: 'auto',
		/** 
		@type number
		@default 100
		*/
		maxWidth: 100,
		/** 
		@type
		@default null
		*/
		show: null,
		/** 
		@type
		@default null
		*/
		hide: null,
		/** 
		@type boolean
		@default true
		*/
		autoOpen: true,
		/** 
		@type boolean
		@default true
		*/
		modal: true,
		/** 
		@type boolean
		@default false
		*/
		resizable: false,
		/** 
		@type array
		@default ['center', 'center']
		*/
		position: ['center', 'center'],
		/** 
		@type boolean
		@default true
		*/
		draggable: true,
		/** 
		@type boolean
		@default true
		*/
		closeOnEscape: true
	},

	/** 
	@static
	@class 
	*/
	growl : {
		/**
		@type string
		@default 'default'
		*/
		theme: 'default',
		/**
		@type number
		@default 240
		*/
		speed: 240,
		/**
		@type boolean
		@default false
		*/
		closer: false
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
		select: function(event, ui) {
			var url = $.data(ui.tab, 'load.tabs');
			if (url) {
				location.href = url;
				return false;
			}
			return true;
		},
		/** @function */
		show: function(event, ui){
			$(ui.tab.href.replace(/^.*?#/, '#')).show();
		}
	},

	/** 
	@static
	@class 
	*/
	tooltip : {
		/**
		@type object
		@param {string} my 'right left'
		@param {string} at 'left left'
		@param {string} offset '-5 0'
		*/
		position: {
                        my: 'right left',
			at: 'left left',
			offset: '-5 0'
		}
	},

	/** 
	@static
	@class 
	*/
	selectmenu : {
		/**
		@type number
		@default 200
		*/
		maxHeight: 200,
		/**
		@type string
		@default 'dropdown'
		*/
		style: 'dropdown',
		/**
		@type boolean
		@default true
		*/
		transferClasses: true,
		/**
		@type array
		@default [
			{ find: '.boom-selectmenu-page', icon: 'ui-icon-document' }
		]
		*/
		icons: [
			{ find: '.boom-selectmenu-page', icon: 'ui-icon-document' }
		]
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
		@default false
		*/
		useCookie: false,
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
		iconHitareaHover: 'boom-tree-hitarea-hover'
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
	uploadify : {
		/**
		@type boolean
		@default true
		*/
		debug: true,
		/**
		@type string
		@default '/boom/flash/uploadify.swf'
		*/
		swf: '/media/boom/flash/uploadify.swf',
		/**
		@type string
		@default '/cms/uploadify/asset'
		*/
		uploader: '/cms/uploadify/asset',
		/**
		@type boolean
		@default false
		*/
		auto: true,
		/**
		@type boolean
		@default true
		*/
		multi: true,
		/**
		@type string
		@default 'Select files'
		*/
		buttonText: 'Select files',
		/**
		@type number
		@default 5
		*/
		queueSizeLimit: 5,
		/**
		@type number
		@default 2
		*/
		simUploadLimit: 2,
		/**
		@type string
		@default 'Allowed types: jpg, png, gif'
		*/
		fileTypeDesc: 'Allowed types: jpg, png, gif',
		/**
		@type string
		@default '*.jpg;*.png;*.gif'
		*/
		fileTypeExts: '*.jpeg;*.jpg;*.png;*.gif',
		/**
		@type string
		@default '/boom/img/cms/cross.png'
		*/
		cancelImg: '/boom/img/cms/cross.png'
	},

	/** 
	@static
	@class 
	*/
	cookie : {
		/**
		@type string
		@default 'boomcookie'
		*/
		name: 'boomcookie',
		/**
		@type string
		@default ','
		*/
		delimiter: ',',
		/**
		@type number
		@default 1
		*/
		expiredays: 1,
		/**
		@type string
		@default '/'
		*/
		path: '/'
	},

	/** 
	@static
	@class 
	*/
	orm : {
		/**
		@type string
		@default '/json_orm/get'
		*/
		getobjUrl: '/json_orm/get'
	},

	/** 
	@static
	@class 
	*/
	page : {
		/** 
		@type number
		@default 1
		*/
		homepageRid: 1,
		/**
		@type number
		@default 0
		*/
		ajaxed: 0
	},

	/** 
	@static
	@class 
	*/
	ajax : {
		/**
		@type string
		@default '/_ajax/show_view/boom/editor/'
		*/
		viewBasePath: '/_ajax/show_view/boom/editor/'
	},

	/** 
	@static
	@class 
	*/
	person: {
		/**
		@type number
		@default 0
		*/
		rid: 0,
		/**
		@type string
		@default 'Default'
		*/
		firstname: 'Default',
		/**
		@type string
		@default ''
		*/
		lastname: ''
	}

};

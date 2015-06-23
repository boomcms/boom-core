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
		*/
		format: 'd F Y H:i',
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
	}
};
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
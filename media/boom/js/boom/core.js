/**
@fileOverview Core CMS functionality.
*/
/**
@namespace
@name $
*/
$.extend({
	/**
	Namespace for core boom classes and functions.
	@static
	@class
	@name $.boom
	*/
	boom :
		/** @lends $.boom */
		{

		options: {},

		/**
		Boom bootstrap/setup. Initialises boom.config and does some browser detection.
		*/
		setup: function(){

			$.extend(this, { config: window.boomConfig });

			// reference boom from the site window too.
			top.$.boom = $.boom;
		},

		/**
		Initialise boom classes. Create top bar and UI.
		@param {Object} options Boom options. Extends and overrides defaults in boom.config.
		*/
		init: function(options) {
			( options ) && $.extend( this.config, options );
			this.options = options;

			$('#b-topbar').ui();

			this._init_widgets();
		},

		_init_widgets : function() {
			$('#b-menu').boomMenu({});
			this.loader = $('body').boomLoader({}).data('boomBoomLoader');
			this.growl = $('body').boomGrowl({}).data('boomBoomGrowl');
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	@class
	*/
	editor: {
		state : function(state) {

			$.post('/cms/editor/state', {state: state}, function() {
				top.location.reload();
			});
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom utils.
	@class
	@static
	*/
	util :
		/** @lends $.boom.util */
		{
		/**
		@class
		@static
		@name $.boom.util.dom
		*/
		dom :
			/** @lends $.boom.util.dom */
			{
			/**
			Generate a unique ID for a DOM element
			@param {String} prefix Optional prefix. Defaults to 'boom-'
			*/
			uniqueId : function(prefix){

				prefix = prefix || 'b-';
				var id;

				do {
					id = Math.floor(Math.random()*1000);
				} while($('#' + prefix + id).length);

				return (prefix + id);
			},
		}
	}
});

$.boom.setup();
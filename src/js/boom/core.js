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

		notifications: [],

		setup: function(){

			$.extend(this, { config: window.boomConfig });

			if (typeof(top.$) === 'undefined') {
				top.$ = $;
			}

			// reference boom from the site window too.
			top.$.boom = $.boom;

			$.ajaxSetup({
				cache: false, // Fix for IE9 - prevent caching of all AJAX requests.
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
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
			$('body').ui();
		},

		_init_widgets : function() {
			this.loader = $('body').boomLoader({}).data('boomBoomLoader');
		},

		/**
		 * Close all notifications before reload the page.
		 *
		 * Because Chrome doesn't do it for us :(
		 *
		 * @returns {undefined}
		 */
		reload: function() {
			var notifications = $.boom.notifications, i;

			for (i = 0; i < notifications.length; i++) {
				notifications[i].close();
			}

			top.location.reload();
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
			}
		}
	}
});

$.boom.setup();
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

			// Fix for IE9 - prevent caching of all AJAX requests.
			$.ajaxSetup({cache: false});
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
			this.loader = $('body').boomLoader({}).data('boomBoomLoader');
		},

		/**
		 * Makes a POST AJAX call after adding the CSRF token to the POST data.
		 */
		post : function() {
			var csrf = this.options.csrf,
				arguments = Array.prototype.slice.apply(arguments);

			if (typeof arguments[1] == 'undefined') {
				arguments.push({
					csrf : csrf
				});
			}
			else if (typeof arguments[1] !== 'function') {
				if (typeof arguments[1] === 'string') {
					arguments[1] += '&csrf=' + encodeURIComponent(csrf);
				} else {
					arguments[1]['csrf'] = csrf;
				}
			}

			return $.post.apply($, arguments);
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
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
		state : function(state, url) {

			$.post('/cms/editor/state', {state: state}, function() {
				if (url) {
					top.location = url;
				} else {
					top.location.reload();
				}
			});
		}
	}
});

$.boom.setup();
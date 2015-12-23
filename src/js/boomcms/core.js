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
		*/
		init: function() {
			$('#b-topbar, body').ui();

			this._init_widgets();
		},

		_init_widgets : function() {
			this.loader = $('body').boomLoader({}).data('boomBoomLoader');
		},

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
	}
});

$.boom.setup();
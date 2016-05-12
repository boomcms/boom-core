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
		/**
		Initialise boom classes. Create top bar and UI.
		*/
		init: function() {
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

			// Used in the login / password reset forms. To be extended to other forms.
			$('.input input')
				.on('input paste change keyup', function() {
					var $this = $(this),
						className = 'has-content';

					$this.val() ? $this.addClass(className) : $this.removeClass(className);
				}).trigger('input');

			$('#b-topbar, body').ui();
		},

		editor: {
			state: function(state, url) {

				$.post('/boomcms/editor/state', {state: state}, function() {
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
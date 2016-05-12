(function($) {
	'use strict';

	function BoomCMS() {
		this.urlRoot = '/boomcms/';
		this.Collections = {};

		BoomCMS.prototype.init = function() {
			if (typeof(top.$) === 'undefined') {
				top.$ = $;
			}

			$.ajaxSetup({
				cache: false, // Fix for IE9 - prevent caching of all AJAX requests.
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			// Used in the login / password reset forms. To be extended to other forms.
			$('.input input')
				.on('keypress', function() {
					var $this = $(this),
						className = 'has-content';

					$this.val() ? $this.addClass(className) : $this.removeClass(className);
				}).keypress();

			$('#b-topbar, body').ui();
		};

		BoomCMS.prototype.confirm = function(title, message) {
			return new boomConfirmation(title, message);
		};

		BoomCMS.prototype.editor = {
			state: function(state, url) {

				$.post('/boomcms/editor/state', {state: state}, function() {
					if (url) {
						top.location = url;
					} else {
						top.location.reload();
					}
				});
			}
		};
	}

	window.BoomCMS = top.BoomCMS = new BoomCMS();
}(jQuery));

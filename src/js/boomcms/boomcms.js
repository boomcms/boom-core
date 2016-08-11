(function($, Backbone) {
	'use strict';

	function BoomCMS() {
		this.editableClass = 'b-editable';
		this.urlRoot = '/boomcms/';
		this.Collections = {};

		this.Model = Backbone.Model.extend({
			addRelationship: function(type, id) {
				return $.ajax({
					url: this.urlRoot + '/' + this.id + '/' + type + '/' + id,
					type: 'put'
				});
			},

			getId: function() {
				return this.id;
			},

			removeRelationship: function(type, id) {
				return $.ajax({
					type: 'delete',
					url: this.urlRoot + '/' + this.id + '/' + type + '/' + id
				});
			}
		}),

		BoomCMS.prototype.init = function(options) {
			if (typeof(top.$) === 'undefined') {
				top.$ = $;
			}

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			this.user = options.user;

			// Used in the login / password reset forms. To be extended to other forms.
			$('.input input')
				.on('input paste change keyup', function() {
					var $this = $(this),
						className = 'has-content';

					$this.val() ? $this.addClass(className) : $this.removeClass(className);
				}).trigger('input');

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

		BoomCMS.prototype.getTimezone = function() {
			var key = 'boomcms.timezone';

			if (!sessionStorage.getItem(key)) {
			  var tz = jstz.determine() || 'UTC';

			  sessionStorage.setItem(key, tz.name());
			}

			return sessionStorage.getItem(key);
		};

		BoomCMS.prototype.notify = function(message) {
			new boomNotification(message).show();
		};
	}

	window.BoomCMS = top.BoomCMS = new BoomCMS();
}(jQuery, Backbone));

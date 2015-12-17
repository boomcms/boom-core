function boomNotification(message) {
	boomNotification.prototype.$document = $(top.document);

	boomNotification.prototype.options = {
		theme : 'default',
		speed : 240,
		closer : false,
		sticky : false,
		open : function(elem, message){
			$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
		}
	};

	boomNotification.prototype.open = function(message) {
		var notified = false,
			waitingApproval = false,
			timer,
			notification = this;

		if ("Notification" in window && Notification.permission !== 'denied') {
			waitingApproval = true;

			Notification.requestPermission(function (permission) {
				waitingApproval = false;

				if (permission === "granted") {
					var n = new Notification('BoomCMS', {
						body: message,
						icon: '/vendor/boomcms/boom-core/img/logo-sq.png',
						requireInteraction: false
					});

					notified = true;
	
					setTimeout(function() {
						n.close();
					}, 3000);
				}
			});
		}

		var timer = setInterval(function() {
			if ( ! waitingApproval && ! notified) {
				notification.showFallback(message);
				clearInterval(timer);
			}
		}, 100);
	};

	boomNotification.prototype.showFallback = function(message) {
		if ( ! this.$document.find('#b-notification').length) {
			$('<div id="b-notification"></div>')
					.appendTo(this.$document.find('body'));
		}

		$.jGrowl(message, this.options);

		this.$document.find('#b-notification').append($('#jGrowl'));
	};

	this.open(message);
};
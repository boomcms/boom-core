function boomNotification(message) {
	this.message = message;

	boomNotification.prototype.$document = $(top.document);

	boomNotification.prototype.show = function() {
		var notified = false,
			waitingApproval = false,
			timer,
			notification = this,
			message = this.message;

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
		$.jGrowl(message);
	};
};
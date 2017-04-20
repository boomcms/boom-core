(function($, BoomCMS) {
    'use strict';

    BoomCMS.Notification = function(message) {
        this.message = message;

        this.$document = $(top.document);

        this.show = function() {
            var notified = false,
                waitingApproval = false,
                timer,
                notification = this,
                message = this.message;

            if ('Notification' in window && Notification.permission !== 'denied') {
                waitingApproval = true;

                Notification.requestPermission(function (permission) {
                    waitingApproval = false;

                    if (permission === 'granted') {
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

            timer = setInterval(function() {
                if ( ! waitingApproval && ! notified) {
                    notification.showFallback(message);
                    clearInterval(timer);
                }
            }, 100);
        };

        this.showFallback = function(message) {
            $.jGrowl(message);
        };

        return this.show();
    };
}($, BoomCMS));

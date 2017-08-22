(function(BoomCMS, Notification) {
    'use strict';

    BoomCMS.Notification = function(message) {
        this.message = message;

        this.show = function() {
            var message = this.message;

            if ('Notification' in window && Notification.permission !== 'denied') {
                Notification.requestPermission(function (permission) {
                    if (permission === 'granted') {
                        var n = new Notification('BoomCMS', {
                            body: message,
                            icon: '/vendor/boomcms/boom-core/img/logo-sq.png',
                            requireInteraction: false
                        });

                        setTimeout(function() {
                            n.close();
                        }, 3000);
                    }
                });
            }
        };

        return this.show();
    };
}(BoomCMS, Notification));

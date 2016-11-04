(function(BoomCMS) {
    'use strict';

    BoomCMS.Confirmation = function(title, message) {
        this.title = title;
        this.message = message;

        this.open = function() {
            var confirmation = this;

            return new BoomCMS.Dialog({
                title : confirmation.title,
                msg : '<p>' + confirmation.message + '</p>',
                width : 300
            });
        };

        return this.open();
    };
}(BoomCMS));

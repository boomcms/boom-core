(function(BoomCMS) {
    'use strict';

    BoomCMS.Editor = {
        url: BoomCMS.urlRoot + 'editor/',

        setTime: function(time) {
            return $.post(this.url + 'time', {time: time});
        },

        state: function(state, url) {
            $.post(this.url + 'state', {state: state}, function() {
                if (url) {
                    top.location = url;
                } else {
                    top.location.reload();
                }
            });
        }
    };
}(BoomCMS));

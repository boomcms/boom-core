(function(BoomCMS, storage) {
    'use strict';

    BoomCMS.Storage = {
        get: function(key) {
            var data = storage.getItem(key);

            try {
               return JSON.parse(data);
            } catch(e) {
               return data;
            }
        },

        save: function(key, data) {
            storage.setItem(key, JSON.stringify(data));
        }
    };
}(BoomCMS, window.sessionStorage));

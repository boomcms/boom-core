(function(BoomCMS) {
    'use strict';
  
    BoomCMS.alert = function(message) {
        return new BoomCMS.Dialog({
            msg : message,
            cancelButton : false
        });
    };
}(BoomCMS));

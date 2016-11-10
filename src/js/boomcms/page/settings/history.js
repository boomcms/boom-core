(function(BoomCMS) {
    'use strict';

    $.widget('boom.pageSettingsHistory', $.boom.pageSettingsDefault, {
        bind: function() {
            var page = this.options.page;

            this.element
                .on('click', 'a[data-timestamp]', function() {
                    BoomCMS.Editor.setTime($(this).attr('data-timestamp')).done(function() {
                        top.location.reload();
                    });
                })
                .on('click', 'a[data-restore]', function(e) {
                    e.preventDefault();

                    page.restoreTo($(this).attr('data-restore'));
                    top.location.reload();
                });
        }
    });
}(BoomCMS));
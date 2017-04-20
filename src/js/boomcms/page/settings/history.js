(function(BoomCMS) {
    'use strict';

    $.widget('boom.pageSettingsHistory', $.boom.pageSettingsDefault, {
        bind: function() {
            var page = this.options.page;

            this.element
                .on('click', 'a[data-restore]', function(e) {
                    e.preventDefault();

                    page.restoreTo($(this).attr('data-restore'))
                        .done(function() {
                            top.location.reload();
                        });
                });
        }
    });
}(BoomCMS));
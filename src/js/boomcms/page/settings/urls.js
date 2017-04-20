$.widget('boom.pageSettingsUrls', {
    baseUrl: '/boomcms/page/{page}/urls',

    add: function() {
        var url = new BoomCMS.PageUrl(null, this.page.id),
            urlEditor = this;

        url.add(this.element.find('form input[type=text]').val())
            .done(function() {
                BoomCMS.Notification('Url added');

                urlEditor.element.load(urlEditor.list_url);
            });
    },

    bind: function() {
        var urlEditor = this;

        this.element
            .on('change', '.b-urls-primary', function() {
                var $url = $(this).closest('li'),
                    is_primary = $url.find('.b-urls-primary').is(':checked')? 1 : 0;

                if (is_primary) {
                    urlEditor.makePrimary($url);
                    urlEditor._trigger('done', null, $url.find('label').text());
                }
            })
            .on('click', '.b-urls-remove', function(e) {
                e.preventDefault();

                urlEditor.delete($(e.target).closest('li'));
            })
            .on('submit', 'form', function(e) {
                e.preventDefault();

                urlEditor.add();
            });
    },

    _create: function() {
        this.page = this.options.page;
        this.list_url = this.baseUrl.replace('{page}', this.page.id);

        this.bind();
    },

    delete: function($li) {
        var id = $li.data('id'),
            url = new BoomCMS.PageUrl(id, this.page.id);

        url.delete()
            .done(function() {
                $li.remove();

                BoomCMS.Notification('The specified URL has been deleted');
            });
    },

    makePrimary: function($url) {
        var url = new BoomCMS.PageUrl($url.data('id'), this.page.id);

        url.makePrimary()
            .done(function() {
                $url
                    .parent()
                    .find('.b-page-urls-primary')
                    .removeClass('b-page-urls-primary')
                    .end()
                    .find('.b-urls-primary:checked')
                    .parent()
                    .addClass('b-page-urls-primary');

                BoomCMS.Notification('The primary URL of the page has been updated');
            });
    }
});
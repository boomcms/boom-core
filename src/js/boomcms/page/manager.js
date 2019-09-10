$.widget( 'boom.pageManager', {
    addActionButtons: function($li) {
        $li.append('<div><a href=\'#\' class=\'fa fa-plus b-pages-add\'><span>Add page</span></a><a href=\'#\' class=\'fa fa-trash-o b-pages-delete\'><span>Delete page</span></a><a href=\'#\' class=\'fa fa-cog b-pages-settings\'><span>Settings</span></a><a href=\'#\' class=\'fa fa-eye b-pages-visibility\'><span>Visible</span></a><a href=\'#\' class=\'fa fa-thumbs-o-up b-pages-version-status\'><span>Published</span></a></div>');
    },

    addPage: function($el) {
        var page = $el.data('page'),
            el = this.element;

        page.add()
            .done(function(data) {
                var newPage = new BoomCMS.Page(data);

                el.pageTree('addPageToList', newPage);
                el.pageTree('makeExpandable', $el);

                setTimeout(function() {
                    el.pageTree('showChildren', $el);
                }, 0);
            });
    },

    _create: function() {
        var pageManager = this;

        this.element
            .pageTree({
                add: function(e, $li) {
                    pageManager.addActionButtons($li);
                    pageManager.showVisibilityIcons($li);
                    pageManager.showStatusIcons($li);
                },
                onPageSelect: function(link) {
                    window.open(link.getUrl());
                }
            });

        this.addActionButtons(this.element.find('li'), {});
    },

    deletePage: function($el) {
        this.showPageSettings($el, 'delete');
    },

    editSettings: function($el) {
        this.showPageSettings($el);
    },

    _init: function() {
        var pageManager = this;

        this.element
            .on('click', '.b-pages-delete', function(e) {
                e.preventDefault();

                pageManager.deletePage($(this).closest('li'));
            })
            .on('click', '.b-pages-add', function(e) {
                e.preventDefault();

                pageManager.addPage($(this).closest('li'));
            })
            .on('click', '.b-pages-settings', function(e) {
                e.preventDefault();

                pageManager.editSettings($(this).closest('li'));
            })
            .on('click', '.b-pages-visibility', function(e) {
                e.preventDefault();

                pageManager.showVisibility($(this).closest('li'));
            })
            .on('click', '.b-pages-version-status', function(e) {
                e.preventDefault();

                pageManager.showPageStatus($(this).closest('li'));
            });
    },

    showPageSettings: function($el, section) {
        var page = $el.data('page'),
            $settings = $('<div></div>');

        $settings
            .addClass('b-settings-container')
            .appendTo($('#b-pages'))
            .load(page.baseUrl + 'settings/index', function() {
                $settings
                    .addClass('open')
                    .pageSettings({
                        page: page,
                        deleteSave: function() {
                            $el.remove();
                            $settings.remove();
                        }
                    });

                if (section) {
                    $settings.pageSettings('show', section);
                }
            });
    },

    showVisibilityIcons: function($el) {
        var page = $el.data('page');

        if (typeof page !== 'undefined' && page.attributes.visible === 0) {
            $el.find('.b-pages-visibility').removeClass('fa-eye').addClass('fa-eye-slash text-pink');
        }
    },

    showStatusIcons: function($el) {
        var page = $el.data('page');

        if(typeof page !== 'undefined') {
            $.get('/boomcms/editor/toolbar?page_id='+page.attributes.id).done(function(response) {
                var status = $(response).find('#b-page-version-status').data('status'),
                    icon = $el.find('.b-pages-version-status');
    
                switch (status) {
                    case 'draft':
                        icon.removeClass('fa-thumbs-o-up');
                        icon.addClass('fa-thumbs-o-down');
                        icon.addClass('text-pink');
                        break;
                    case 'embargoed':
                        icon.removeClass('fa-thumbs-o-up');
                        icon.addClass('fa-clock-o');
                        icon.addClass('text-pink');
                        break;
                    case 'pending approval':
                        icon.removeClass('fa-thumbs-o-up');
                        icon.addClass('fa-product-hunt');
                        icon.addClass('text-blue');
                        break;
                    default:
                        icon.addClass('fa-thumbs-o-up');
                }
            });
        }
    },

    showVisibility: function($el) {
        this.showPageSettings($el, 'visibility');
    },

    showPageStatus: function($el) {
        this.showPageSettings($el, 'drafts');
    }
});
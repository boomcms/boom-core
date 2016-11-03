(function($, BoomCMS) {
    'use strict';

    $.widget('boom.pageSettingsRelations', {
        addRelatedPage: function() {
            var page = this.page,
                pages = this.pages;

            new BoomCMS.LinkPicker(new BoomCMS.Link(), {
                external: false,
                asset: false
            })
                .done(function(link) {
                    page.addRelatedPage(link.getPageId())
                        .done(function() {
                            pages.add(new BoomCMS.Page({
                                id: link.getPageId(),
                                title: link.getTitle(),
                                url: link.getUrl()
                            }));
                        });
                });
        },

        addToList: function(page) {
            var $li = $('<li>');

            $('<span>').addClass('title').text(page.getTitle()).appendTo($li),
            $('<span>').addClass('uri').text(page.getUrl()).appendTo($li),
            $('<a>')
                .attr('href', '#')
                .addClass('fa fa-trash-o')
                .data('page', page)
                .html('<span>Remove</span>')
                .appendTo($li);

            this.element.find('ul').append($li);
            this.element.find('.current').show();
        },

        bind: function() {
            var editor = this;

            this.element
                .on('click', '#b-tags-addpage', function() {
                    editor.addRelatedPage();
                })
                .on('click', 'li a', function() {
                    editor.removeRelatedPage($(this));
                });
        },

        _create: function() {
            var relations = this,
                page = this.options.page;

            this.page = page;
            this.pages = new BoomCMS.Collections.Pages();

            this.pages
                .on('add', function(page) {
                    relations.addToList(page);
                })
                .on('remove', function() {
                    page.removeRelatedPage(page);    
                });

            this.getRelatedPages();
            this.bind();
        },

        getRelatedPages: function() {
            this.pages.findByRelatedTo(this.page);
        },

        removeRelatedPage: function($a) {
            var $el = this.element,
                $relatedPages = $el.find('ul'),
                $current = $el.find('.current'),
                page = $a.data('page');

            this.pages.remove(page);
            
            $a.parent().remove();
            $relatedPages.find('li').length ? $current.show() : $current.hide();
        }
    });
}(jQuery, BoomCMS));
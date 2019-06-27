(function($, BoomCMS) {
    'use strict';

    $.widget('boom.pageSettingsRelatedlangpages', {

        addRelatedLangPage: function(language) {
            var control = this,
                page = this.page,
                lang = language;

            new BoomCMS.LinkPicker(new BoomCMS.Link(), {
                external: false,
                asset: false
            })
            .done(function(link) {

                    var pageId = $('#en-page').data('default-page-id');
                    if(lang == 'en') {
                        pageId = page.getId();
                    }

                    page.addRelatedLangPage(pageId, lang, link.getPageId())
                        .done(function(resp) {

                            var response = JSON.parse(resp);

                            if(response.status) {
                                control.addToList({
                                    lang: lang,
                                    page_id: link.pageId,
                                    title: link.title,
                                    url: link.url
                                });
                            } else {
                                if(response.msg != '') {
                                    $("#error-message").show();
                                    $("#error-message").text(response.msg);
                                }
                            }
                });
            });
        },

        addToList: function(page) {

            var $lang = $('#'+page.lang+'-page');

            if(page.lang == 'en') {
                $('#en-page').data('default-page-id', page.page_id);
                $('.b-lang-addpage').show();
            }

            $('#'+page.lang+'-lang-button').hide();

                $('<a id="b-lang-remove">')
                .attr('href', '#')
                .addClass('fa fa-trash-o')
                .data('page', page)
                .html('<span>Remove</span>')
                .appendTo($lang);

            $('<span>').addClass('title').text(page.title).appendTo($lang);
            $('<span>').addClass('uri').text(page.url).appendTo($lang);
        },

        bind: function() {
            var editor = this;

            this.element
                .on('click', '.b-lang-addpage', function() {
                  var lang = $(this).data('lang');
                    editor.addRelatedLangPage(lang);

                })
                .on('click', '#b-lang-remove', function() {
                    editor.removeLangPage($(this).data('page'));
                });
        },

        _create: function() {
            var relations = this,
                page = this.options.page;
                this.page = page;

                $('.b-lang-addpage').hide();
                $('#en-lang-button').show();

                $("#current-page-title").text(this.page.getTitle());

            // ajax call 
            var pages = this.page.getRelatedLangPages();
            pages.done(function(data) {
                // parse data 
                var links = JSON.parse(data).map(function(item){
                    return item;
                });

                // add to list
                links.map(function(link){
                    relations.addToList(link);
                });
            });

            $("#error-message").on('click', function(){
                $(this).hide();
            });

            this.bind();
        },

        removeLangPage: function(link) {
            var page = this.page;

            page.removeRelatedLangPage(link.lang, link.page_id)
                    .done(function(resp) {

                        if(link.lang == 'en') {
                            $('.b-lang-addpage').hide();
                            $('#en-page').data('default-page-id', '');
                        }

                        var response = JSON.parse(resp);
                        if(response.status) {
                            $('#'+link.lang+'-page').html('');

                            var hasEnglishPage = $('#en-page').data('default-page-id');
                            if(hasEnglishPage != '' || link.lang == 'en') {
                                $('#'+link.lang+'-lang-button').show();
                            }
                            
                        } else {
                            if(response.msg != '') {
                                $("#error-message").show();
                                $("#error-message").text(response.msg);
                            }
                        }
            });
        }
    });
}(jQuery, BoomCMS));
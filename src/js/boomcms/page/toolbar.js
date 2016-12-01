/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
    buttons : {},

    _bindButtonEvents: function() {
        var self = this,
            page = this.options.page;

        this.element.contents()
            .on('click', 'button[data-editor-time]', function() {
                BoomCMS.Editor
                    .setTime($(this).attr('data-editor-time'))
                    .done(function() {
                        top.location.reload();
                    });
            })
            .on('click', 'button.b-version-info', function() {
                var html = self.element.contents().find('#b-history-template').html();

                new BoomCMS.Dialog({
                    msg: html,
                    width: '600px',
                    cancelButton: false,
                    title: 'Version information'
                });
            })
            .on('click', '#b-page-delete', function() {
                self.$settings.pageSettings('show', 'delete');
                self.openPageSettings();
            })
            .on('click', '#b-page-addpage', function() {
                page.add()
                    .done(function(response) {
                        top.location = response.url;
                    })
                    .fail(function(response) {
                        BoomCMS.alert(response);
                    });
            })
            .find('.b-page-visibility')
            .dblclick()
            .end()
            .on('sclick', '.b-page-visibility', function() {
                self.showSettings('visibility');
            })
            .on('dclick', '.b-page-visibility', function() {
                page.toggleVisibility();

                self.toggleVisibility(page.isVisible());
            })
            .on('click', '.b-button-preview', function() {
                window.BoomCMS.Editor.state($(this).attr('data-preview'));
            })
            .on('click', '#b-page-template', function() {
                self.showSettings('template');
            })
            .on('click', '#b-menu-button', function() {
                var $body = $('body');

                if ($body.hasClass('pushy-active')) {
                    $(top.window).trigger('boom:dialog:open');
                } else {
                    $(top.window).trigger('boom:dialog:close');
                }
            })
            .on('click', '#b-page-settings', function() {
                self.settingsAreOpen() ? self.closePageSettings() : self.showSettings();
            })
            .find('#b-page-version-status')
            .dblclick()
            .end()
            .on('sclick', '#b-page-version-status', function() {
                self.showSettings('drafts');    
            })
            .on('dclick', '#b-page-version-status', function() {
                page.publish().done(function(data) {
                    self.status.set(data);
                    $(this).blur();
                });        
            })
            .on('mouseup', '#b-menu a', function() {
                // Clicking a link in the menu but opening in a new tab causes the menu to close.
                // But without this the iframe doesn't minimise and the window can't scroll.

                $(top.window).trigger('boom:dialog:close');
            })
            .on('click', '#b-gethelp', function() {
                self.supportRequest();
            });

        this.buttonBar = this.element.contents().find('#b-topbar');
    },

    closePageSettings: function() {
        var toolbar = this;

        this.$settingsContainer.removeClass('open');

        setTimeout(function() {
            toolbar.minimise();
            $(top.window).trigger('boom:dialog:close');
        }, 1000);
    },

    _create: function() {
        var toolbar = this;

        this.toolbarWidth = this.element.width();

        this.findButtons();
        this._toggle_view_live_button();

        this.status = $('#b-page-version-status')
            .pageStatus({
                page : this.options.page,
                publishable : this.options.publishable
            })
            .data('boom-pageStatus');

        this.$settingsContainer = this.element.contents().find('#b-page-settings-toolbar');

        this.$settings = this.$settingsContainer
            .find('.b-settings')
            .pageSettings({
                page: toolbar.options.page,
                close: function() {
                    toolbar.closePageSettings();
                },
                draftsSave: function(event, data) {
                    toolbar.draftsSaved(event, data);
                },
                featureSave: function(event, asset) {
                    top.$('.b-page-featureimage').each(function() {
                        var $el = $(this);

                        if (asset.getId() > 0) {
                            if ($el.is('img')) {
                                var src = $el
                                    .attr('src')
                                    .replace(/\/asset\/view\/\d+/, asset.getUrl());

                                $el.attr('src', src);
                            } else {
                                var attrs = { };

                                $.each($el[0].attributes, function(i, attr) {
                                    attrs[attr.nodeName] = attr.nodeValue;
                                });

                                $el.replaceWith(function () {
                                    return $('<img />', attrs).attr('src', asset.getUrl());
                                });
                            }
                        } else {
                            $el.remove();
                        }
                    });
                },
                templateSave: function() {
                    toolbar.status.set('draft');

                    new BoomCMS.Confirmation('Reload page?', 'Do you want to reload the page to view the new template?')
                        .done(function() {
                            top.location.reload();
                        });
                },
                visibilitySave: function(event, response) {
                    toolbar.toggleVisibility(response === '1');
                },
                urlsSave: function(event, primaryUrl) {
                    top.history.replaceState({},
                        top.document.title,
                        '/' + ((primaryUrl === '/') ? '' : primaryUrl)
                    );
                },
                deleteSave: function(event, response) {
                    BoomCMS.Notification('Page deleted, redirecting to parent');

                    setTimeout(function() {
                        top.location = response;
                    }, 500);
                }
            });

        this._bindButtonEvents();
    },

    draftsSaved: function(event, data) {
        this.status.set(data.status);
    },

    findButtons: function() {
        this.buttons = {
            visible : this.element.contents().find('#b-page-visible'),
            invisible : this.element.contents().find('#b-page-invisible'),
            viewLive : this.element.contents().find('#b-page-viewlive')
        };
    },

    /**
    * extend the toolbar to cover the entire window
    * @function
    */
    maximise: function() {
        this.element.css({
            width : '100%',
            'z-index' : 100002
        });
    },

    /**
    * minimise the toolbar to allow clicking on the underlying page
    * @function
    */
    minimise: function() {
        this.element.css({
            width : this.toolbarWidth,
            'z-index' : 10000
        });
    },

    openPageSettings: function() {
        this.closeSettingsOnPublish = false;
        this.maximise();

        this.$settingsContainer.addClass('open');

        $(top.window).trigger('boom:dialog:open');
    },

    /**
    @function
    */
    hide: function() {
        this.buttonBar.css('z-index', 1);
    },

    settingsAreOpen: function() {
        return this.$settingsContainer.hasClass('open');
    },

    /**
    @function
    */
    show: function() {
        this.buttonBar.css('z-index', 10000);
    },

    showSettings: function(section) {
        if (!this.settingsAreOpen()) {
            this.openPageSettings();
        }

        if (section) {
            this.$settings.pageSettings('show', section);
        }
    },

    supportRequest: function() {
        var url = '/boomcms/support', dialog;

        dialog = new BoomCMS.Dialog({
            url: url,
            title: 'Submit support request',
            width: 600,
            onLoad: function() {
                dialog.contents
                    .find('input[name=browser]')
                    .val(navigator.platform + ' - ' + navigator.userAgent)
                    .end()
                    .find('input[name=viewport-width]')
                    .val(document.documentElement.clientWidth)
                    .end()
                    .find('input[name=viewport-height]')
                    .val(document.documentElement.clientHeight)
                    .end()
                    .find('input[name=location]')
                    .val(top.location.href);
            }
        })
        .done(function() {
            $.post(url, dialog.contents.find('form').serialize());

            BoomCMS.Notification('Your message has been sent');
        });
    },

    toggleVisibility: function(visible) {
        if (visible === 1 || visible === true) {
            this.buttons.visible.show();
            this.buttons.invisible.hide();
        } else {
            this.buttons.visible.hide();
            this.buttons.invisible.show();
        }

        this._toggle_view_live_button();  
    },

    _toggle_view_live_button: function() {
        if (this.buttons.visible.css('display') === 'none') {
            this.buttons.viewLive
                .attr('title', 'You cannot view a live version of this page as it is currently hidden from the live site')
                .prop('disabled', true);
        } else {
            this.buttons.viewLive
                .attr('title', 'View the page as it appears on the live site')
                .prop('disabled', false);
        }
    }
});

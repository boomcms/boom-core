(function(BoomCMS) {
    'use strict';

    BoomCMS.ChunkLinksetEditor = function(pageId, slotname, options) {
        this.pageId = pageId;
        this.slotname = slotname;
        this.options = options;
        this.deferred = new $.Deferred();

        this.toggle = {
            title: '#b-linkset-title',
            linkAssets: '.b-linkset-asset',
            linkTitle: '.b-linkset-title',
            linkText: '.b-linkset-text'
        };

        BoomCMS.ChunkLinksetEditor.prototype.addDeleteButtons = function() {
            this.$links.find('li').each(function() {
                var $this = $(this);

                if (!$this.find('.delete').length) {
                    $('<a class="delete fa fa-trash-o" href="#"></a>').appendTo($this);
                }
            });
        };

        BoomCMS.ChunkLinksetEditor.prototype.addLink = function() {
            var linksetEditor = this;

            new BoomCMS.LinkPicker()
                .done(function(link) {
                    var $a = $('<a href="#"></a>')
                        .attr('data-page-id', link.getPageId())
                        .attr('data-title', '')
                        .attr('data-url', link.getUrl())
                        .attr('data-asset', '')
                        .text(link.getTitle());

                    linksetEditor.$links.append($('<li></li>').append($a));
                    linksetEditor.dialog.contents.find('#b-linkset-links .none').hide();
                    linksetEditor.addDeleteButtons();
                    linksetEditor.editLink($a);

                    setTimeout(function() {
                        linksetEditor.resize();
                    }, 0);
                });
        };

        BoomCMS.ChunkLinksetEditor.prototype.bind = function() {
            var linksetEditor = this;

            for (var property in this.toggle) {
                if (!this.options[property]) {
                    this.dialog.contents.find(this.toggle[property]).hide();
                }
            }

            this.$links = this.dialog.contents.find('#b-linkset-links ul');

            this.addDeleteButtons();

            this.$links
                .on('click', 'a[data-url]', function() {
                    linksetEditor.editLink($(this));
                });

            this.dialog.contents
                .on('click', '.back', function() {
                    linksetEditor.dialog.contents
                        .find('#b-linkset-current')
                        .removeClass('visible');
                })
                .on('click', '#b-linkset-add', function() {
                    linksetEditor.addLink();
                })
                .on('click', '.delete', function() {
                    linksetEditor.deleteLink($(this).parent());
                })
                .on('keyup change', '#b-linkset-current form input[type=text]', function() {
                    var $this = $(this),
                        name = $this.attr('name'),
                        val = $this.val();

                    linksetEditor.currentLink.attr('data-' + name, val);

                    if (name === 'title') {
                        linksetEditor.currentLink.text(val);
                    }
                })
                .on('click', '.b-linkset-target button', function(e) {
                    e.preventDefault();

                    linksetEditor.editLinkTarget();
                })
                .on('focusin', '.b-linkset-target input', function() {
                    linksetEditor.editLinkTarget();
                })
                .on('click', '#b-linkset-delete', function() {
                    linksetEditor.deferred.resolveWith({});
                    linksetEditor.dialog.cancel();
                })
                .on('click', '.b-linkset-asset button, .b-linkset-asset img, .b-linkset-asset p', function(e) {
                    e.preventDefault();

                    linksetEditor.editAsset(new BoomCMS.Asset({id: linksetEditor.currentLink.attr('data-asset')}));
                })
                .find('img').on('load', function() {
                    linksetEditor.resize();
                })
                .end()
                .find('ul')
                .sortable();

            if (this.options.limit === 1) {
                this.bindSingle();
            }
        };

        BoomCMS.ChunkLinksetEditor.prototype.bindSingle = function() {
            var $link = this.$links.find('[data-url]');

            if ($link.length === 0) {
                $link = $('<a>');
            }

            this.editLink($link);
            this.dialog.contents.addClass('b-linkset-single');
            this.dialog.contents.find('#b-linkset-add').hide();
        };

        BoomCMS.ChunkLinksetEditor.prototype.editAsset = function(currentAsset) {
            var linksetEditor = this;

            BoomCMS.AssetPicker(currentAsset)
                .done(function(asset) {
                    linksetEditor.currentLink.attr('data-asset', asset.getId());
                    linksetEditor.toggleLinkAsset(asset);
                });
        };

        BoomCMS.ChunkLinksetEditor.prototype.editLink = function($a) {
            this.currentLink = $a;

            this.dialog.contents
                .find('#b-linkset-current')
                .addClass('visible')
                .find('.back')
                .focus()
                .end()
                .find('form div')
                .removeClass()
                .addClass($a.attr('data-page-id') ? 'optional' : '')
                .end()
                .find('.b-linkset-target input[type=text]')
                .val($a.attr('data-url'))
                .end()
                .find('.b-linkset-title input[type=text]')
                .val($a.attr('data-title'))
                .end()
                .find('.b-linkset-text input[type=text]')
                .val($a.attr('data-text'))
                .end();

            this.toggleLinkAsset(new BoomCMS.Asset({id: $a.attr('data-asset')}));

            this.resize();
        };

        BoomCMS.ChunkLinksetEditor.prototype.editLinkTarget = function() {
            var linksetEditor = this,
                link = new BoomCMS.Link(this.currentLink.attr('data-url'), this.currentLink.attr('data-page-id'));

            new BoomCMS.LinkPicker(link)
                .done(function(link) {
                    var $div = linksetEditor.dialog.contents.find('#b-linkset-current form > div'),
                        className = 'optional';

                    linksetEditor.currentLink
                        .attr('data-page-id', link.getPageId())
                        .attr('data-url', link.getUrl());

                    link.getPageId() ? $div.addClass(className) : $div.removeClass(className);

                    linksetEditor.dialog.contents.find('.b-linkset-target input').val(link.getUrl());
                });
        };

        BoomCMS.ChunkLinksetEditor.prototype.deleteLink = function($li) {
            var linksetEditor = this;

            $li.fadeOut(200, function() {
                $li.remove();
                linksetEditor.resize();
            });
        };

        BoomCMS.ChunkLinksetEditor.prototype.getData = function() {
            return {
                links: this.getLinks(),
                title: this.dialog.contents.find('#b-linkset-title input').val()
            };
        };

        BoomCMS.ChunkLinksetEditor.prototype.getDialogTitle = function() {
            return this.options.limit === 1 ? 'Edit Link': 'Edit Linkset';
        };

        BoomCMS.ChunkLinksetEditor.prototype.getLinks = function() {
            var linksetEditor = this, links = [];

            if (this.options.limit === 1) {
                return [this.getLinkData(this.currentLink)];
            };

            this.$links.find('a:not(.delete)').each(function() {
                links.push(linksetEditor.getLinkData($(this)));
            });

            return links;
        };

        BoomCMS.ChunkLinksetEditor.prototype.getLinkData = function($el) {
            return {
                target_page_id: $el.attr('data-page-id'),
                url: $el.attr('data-url'),
                title: $el.attr('data-title'),
                asset_id: $el.attr('data-asset'),
                text: $el.attr('data-text')
            };
        };

        BoomCMS.ChunkLinksetEditor.prototype.open = function() {
            var linksetEditor = this;

            this.dialog = new BoomCMS.Dialog({
                url: '/boomcms/page/' + this.pageId + '/chunk/edit?slotname=' + this.slotname + '&type=linkset',
                title: this.getDialogTitle(),
                id: 'b-linkset-editor',
                width: 600,
                onLoad: function() {
                    linksetEditor.bind();
                    linksetEditor.resize();
                }
            })
            .done(function() {
                linksetEditor.deferred.resolve(linksetEditor.getData());
            })
            .fail(function() {
                linksetEditor.deferred.reject();
            });

            return this.deferred;
        };

        BoomCMS.ChunkLinksetEditor.prototype.resize = function() {
            this.dialog.contents
                .find('section')
                .css('height', '')
                .equalHeights();
        };

        BoomCMS.ChunkLinksetEditor.prototype.toggleLinkAsset = function(asset) {
            var $linksetAsset = this.dialog.contents.find('.b-linkset-asset');

            if (asset && asset.getId() > 0) {
                $linksetAsset
                    .find('.none')
                    .hide()
                    .end()
                    .find('img')
                    .attr('src', asset.getUrl('view', 500))
                    .show();
            } else {
                $linksetAsset
                    .find('.none')
                    .show()
                    .end()
                    .find('img')
                    .hide();
            }
        };

        return this.open();
    };
}(BoomCMS));

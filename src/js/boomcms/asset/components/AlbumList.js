(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.AlbumList = Backbone.View.extend({
        tagName: 'div',
        renderTimeout: null,
        selectedClass: 'selected',

        getAlbumElement: function(album) {
            return this.$('li[data-album=' + album.getId() + ']');
        },

        initialize: function(options) {
            var view = this,
                scrollTimeout = null;

            this.albums = options.albums;
            this.selected = options.selected;
            this.$container = options.$container;

            this.albumTemplate = _.template($('#b-assets-album-thumbnail-template').html());
            this.template = _.template($('#b-assets-album-list-template').html());

            this.listenTo(this.albums, 'add sync', this.render);
            this.listenTo(this.albums, 'remove', this.removeAlbum);
            this.listenTo(this.selected, 'add', this.selectAlbum);
            this.listenTo(this.selected, 'remove', this.unselectAlbum);

            this.listenTo(this.albums, 'change:asset_count change:description change:slug change:name', function(album) {
                view.updateAlbum(album);
            });

            this.listenTo(this.albums, 'change:thumbnail change:feature_image_id', function(album) {
                view.refreshThumbnail(album);
            });

            this.$container.on('scroll', function() {
                if (scrollTimeout !== null) {
                    clearTimeout(scrollTimeout);
                }

                scrollTimeout = setTimeout(function() {
                    view.lazyLoadThumbnails();
                }, 300);
            });
        },

        lazyLoadThumbnails: function() {
            var $window = $(this.$el[0].ownerDocument),
                windowTop = $window.scrollTop(),
                windowBottom = windowTop + document.documentElement.clientHeight,
                $thumbnails = this.$('[data-asset]');

            $thumbnails.each(function(i, el) {
                var $el = $(el),
                    top = $el.offset().top;

                if (top >= windowTop && top <= windowBottom) {
                    var asset = new BoomCMS.Asset({id: parseInt($el.attr('data-asset'))});

                    $el
                        .css('background-image', 'url(' + asset.getUrl('thumb', 500, 500) + ')')
                        .removeAttr('data-asset');
                }
            });
        },

        refreshThumbnail: function(album) {
            var $li = this.getAlbumElement(album),
                $a = $li.find('a'),
                assetId = album.get('feature_image_id'),
                asset = new BoomCMS.Asset({id: parseInt(assetId)});

            if (assetId) {
                $a.css('background-image', 'url(' + asset.getUrl('thumb', 500, 500) + '?' + (new Date()).valueOf() + ')');
            }
        },

        removeAlbum: function(album) {
            this.$el
                .find('li[data-album=' + album.getId() + ']')
                .fadeOut(600, function() {
                    $(this).remove();
                });
        },

        render: function() {
            var view = this,
                $el = this.$el,
                albums = this.albums;

            if (this.renderTimeout !== null) {
                clearTimeout(this.renderTimeout);
            }

            this.renderTimeout = setTimeout(function() {
                var $ul, $li, i;

                $el.html($(view.template()));
                $ul = $el.find('ul');

                for (i = 0; i < albums.models.length; i++) {
                    $li = view.renderAlbum(albums.models[i]);
                    $ul.append($li);
                }

                view.$('li').removeClass('selected');

                if (view.selected !== undefined) {
                    view.selected.each(function(album) {
                        view.getAlbumElement(album).addClass(view.selectedClass);
                    });
                }

                setTimeout(function() {
                    view.lazyLoadThumbnails();
                }, 0);
            }, 500);

            return this;
        },

        renderAlbum: function(album) {
            return this.albumTemplate({
                album: album
            });
        },

        selectAlbum: function(album) {
            this.getAlbumElement(album).addClass(this.selectedClass);
        },

        unselectAlbum: function(album) {
            this.getAlbumElement(album).removeClass(this.selectedClass);
        },

        updateAlbum: function(album) {
            var $li = this.getAlbumElement(album);

            $li.replaceWith(this.renderAlbum(album));
        }
    });
}(jQuery, Backbone, BoomCMS));

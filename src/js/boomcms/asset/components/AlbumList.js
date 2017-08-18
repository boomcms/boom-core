(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.AlbumList = Backbone.View.extend({
        el: $('<ul class="b-assets-album-list"></ul>'),
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

            this.template = _.template($('#b-assets-album-list-template').html());

            this.listenTo(this.albums, 'change add sync', this.render);
            this.listenTo(this.albums, 'remove', this.removeAlbum);
            this.listenTo(this.selected, 'add', this.selectAlbum);
            this.listenTo(this.selected, 'remove', this.unselectAlbum);

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

        removeAlbum: function(album) {
            this.$el
                .find('li[data-album=' + album.getId() + ']')
                .fadeOut(600, function() {
                    $(this).remove();
                });
        },

        render: function() {
            var view = this;

            if (this.renderTimeout !== null) {
                clearTimeout(this.renderTimeout);
            }

            this.renderTimeout = setTimeout(function() {
                view.$el.html($(view.template({
                    albums: view.albums
                })));

                view.$('li').removeClass('selected');

                if (view.selected !== undefined) {
                    view.selected.each(function(album) {
                        view.$('li[data-album=' + album.getId() + ']').addClass(view.selectedClass);
                    });
                }

                setTimeout(function() {
                    view.lazyLoadThumbnails();
                }, 0);
            }, 500);

            return this;
        },

        selectAlbum: function(album) {
            this.getAlbumElement(album).addClass(this.selectedClass);
        },

        unselectAlbum: function(album) {
            this.getAlbumElement(album).removeClass(this.selectedClass);
        }
    });
}(jQuery, Backbone, BoomCMS));

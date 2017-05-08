(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ThumbnailGrid = Backbone.View.extend({
        loading: 'loading',
        none: 'none',
        thumbnailsSelector: '.thumbnails > div',
        thumbnails: [],

        initialize: function(options) {
            var view = this,
                scrollTimeout = null;

            this.assets = options.assets;
            this.selection = options.selection;

            this.listenTo(this.assets, 'add remove sync', this.render);
            this.listenTo(this.assets, 'change change:image', this.justify);

            this.listenTo(this.assets, 'reset', function() {
                view.$el.removeClass(view.none).addClass(view.loading);
            });

            $(view.$el[0].ownerDocument).on('scroll', function() {
                if (scrollTimeout !== null) {
                    clearTimeout(scrollTimeout);
                }

                scrollTimeout = setTimeout(function() {
                    view.lazyLoadThumbnails();
                }, 300);
            });
        },

        justify: function() {
            this.$thumbnails.justifyAssets();

            return this;
        },

        lazyLoadThumbnails: function() {
            var $window = $(this.$el[0].ownerDocument),
                windowTop = $window.scrollTop(),
                windowBottom = windowTop + document.documentElement.clientHeight;

            for (var i = 0; i < this.thumbnails.length; i++) {
                var top = this.thumbnails[i].$el.offset().top;

                if (top >= windowTop && top <= windowBottom) {
                    this.thumbnails[i].loadImageOnce();
                }
            }
        },

        render: function() {
            var view = this,
                selection = this.selection,
                assetCount = this.assets.models.length;

            this.$thumbnails = this.$(this.thumbnailsSelector).html('');

            if (assetCount === 0 && this.assets.fetched === true) {
                this.$el.removeClass(this.loading).addClass(this.none);

                return this;
            }

            this.assets.each(function(asset, i) {
                view.$el.removeClass(view.none);

                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                view.thumbnails.push(thumbnail);
                view.$thumbnails.append(thumbnail.render().el);

                if (selection.get(asset.getId())) {
                    thumbnail.select();
                }

                if (i === (assetCount - 1)) {
                    setTimeout(function() {
                        view.$el.removeClass(view.loading);
                        view.justify();
                        view.lazyLoadThumbnails();
                    }, 0);
                }
            });

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

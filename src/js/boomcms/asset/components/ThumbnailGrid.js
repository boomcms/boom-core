(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ThumbnailGrid = Backbone.View.extend({
        loading: 'loading',
        none: 'none',
        thumbnails: '.thumbnails > div',

        initialize: function(options) {
            var view = this;

            this.assets = options.assets;
            this.selection = options.selection;

            this.listenTo(this.assets, 'add remove sync', this.render);
            this.listenTo(this.assets, 'change change:image', this.justify);

            this.listenTo(this.assets, 'reset', function() {
                view.$el.removeClass(view.none).addClass(view.loading);
            });
        },

        justify: function() {
            this.$thumbnails.justifyAssets();

            return this;
        },

        render: function() {
            var view = this,
                selection = this.selection,
                assetCount = this.assets.models.length;

            this.$thumbnails = this.$(this.thumbnails).html('');

            if (assetCount === 0 && this.assets.fetched === true) {
                this.$el.removeClass(this.loading).addClass(this.none);

                return this;
            }

            this.assets.each(function(asset, i) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                view.$thumbnails.append(thumbnail.render().el);

                if (selection.get(asset.getId())) {
                    thumbnail.select();
                }

                if (i === (assetCount - 1)) {
                    setTimeout(function() {
                        view.$el.removeClass(view.loading);
                        view.justify();
                    }, 0);
                }
            });

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

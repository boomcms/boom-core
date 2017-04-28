(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ThumbnailGrid = Backbone.View.extend({
        loading: 'loading',
        thumbnails: '.thumbnails > div',

        initialize: function(options) {
            var view = this;

            this.assets = options.assets;
            this.selection = options.selection;

            this.listenTo(this.assets, 'add remove reset sync', this.render);
            this.listenTo(this.assets, 'change change:image', this.justify);
        },

        justify: function() {
            this.$thumbnails.justifyAssets();

            return this;
        },

        render: function() {
            var view = this,
                assetCount = this.assets.models.length;

            this.$thumbnails = this.$(this.thumbnails).html('');

            this.assets.each(function(asset, i) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                view.$thumbnails.append(thumbnail.render().el);

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

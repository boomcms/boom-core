(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ThumbnailGrid = Backbone.View.extend({
        initialize: function(options) {
            this.assets = options.assets;
        },

        justify: function() {
            this.element.find('.b-assets-view-thumbs > div:nth-of-type(2)').justifyAssets();
        },

        render: function() {
            if (!this.assets.length) {
                return this.$el.html(this.$('#b-assets-none-template').html());
            }

            this.assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                this.$el.append(thumbnail.render().el);
            });

            this.element.find('#b-assets-view-thumbs').removeClass('loading');
            this.justify();
        }
    });
}(jQuery, Backbone, BoomCMS));

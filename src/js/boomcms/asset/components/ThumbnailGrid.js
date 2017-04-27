(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ThumbnailGrid = Backbone.View.extend({
        initialize: function(options) {
            var view = this;

            this.assets = options.assets;
            this.selection = options.selection;

            this.listenTo(this.assets, 'add remove reset sync', this.render);

            this.listenTo(this.assets, 'select', function(asset) {
                view.selection.toggle(asset);
            });
        },

        justify: function() {
            this.$el.justifyAssets();

            return this;
        },

        render: function() {
            var view = this;

            if (!this.assets.length) {
                this.$el.addClass('none');

                return this;
            }

            this.assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                });

                view.$el.append(thumbnail.render().el);
            });

            return this.justify();
        }
    });
}(jQuery, Backbone, BoomCMS));

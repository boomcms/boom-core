(function($, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Navigation = Backbone.View.extend({
        el: '#b-assets-navigation',

        initialize: function(options) {
            this.selection = options.selection;
        },

        render: function(assets) {
            var $container = $container = $('<div></div>');

            this.$el.html('');

            assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                        model: asset
                    }).render(),
                    width = Math.floor(150 * asset.getAspectRatio());

                thumbnail.$el.css('width', width);

                $container.append($('<div></div>').append(thumbnail.$el));

//                if (selection.get(asset.getId())) {
//                    thumbnail.select();
//                }
            });

            this.$el.html($container);
        }
    });
}(jQuery, BoomCMS));

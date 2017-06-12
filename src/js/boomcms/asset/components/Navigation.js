(function($, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Navigation = Backbone.View.extend({
        el: '#b-assets-navigation',

        initialize: function(options) {
            this.selection = options.selection;
        },

        render: function(assets, active) {
            var $container = $container = $('<div></div>'),
                thumbnails = [];

            this.$el.html('');

            assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                        model: asset
                    }).render(),
                    width = Math.floor(150 * asset.getAspectRatio());

                thumbnail.$el.css('width', width);

                $container.append($('<div></div>').append(thumbnail.$el));

                if (asset.getId() === active.getId()) {
                    thumbnail.$el.parent().addClass('active');
                }

                thumbnails.push(thumbnail);
            });

            for (var i = 0; i < thumbnails.length; i++) {
                thumbnails[i].loadImageOnce();
            }

            this.$el.html($container);
        }
    });
}(jQuery, BoomCMS));

(function($, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Filmroll = Backbone.View.extend({
        visibleClass: 'visible',

        hide: function() {
            this.$el.removeClass(this.visibleClass);
        },

        initialize: function(options) {
            this.$el = $('<div id="b-assets-filmroll"></div>');
            this.assets = options.assets;
        },

        render: function() {
            var filmroll = this,
                thumbnails = [];
     
            this.$el.html('');

            this.assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                            model: asset
                        }).render(),
                    width = Math.floor(150 * asset.getAspectRatio());

                thumbnail.$el.css('width', width);

                filmroll.$el.append($('<div></div>').append(thumbnail.$el));

                thumbnails.push(thumbnail);
            });

            setTimeout(function() {
                filmroll.filmroll = new FilmRoll({
                    container: filmroll.$el,
                    scroll: false,
                    configure_load: true
                });

                for (var i = 0; i < thumbnails.length; i++) {
                    thumbnails[i].$el.css('width', '100%');
                    thumbnails[i].loadImage();
                }
            }, 0);

            return this;
        },

        select: function(asset) {
            var filmroll = this;

            setTimeout(function() {
                var $el = filmroll.$el.find('[data-asset="' + asset.getId() + '"]').parents('.film_roll_child');

                filmroll.$el.find('.film_roll_child').removeClass('selected');

                if ($el.length) {
                    $el.addClass('selected');
                    filmroll.filmroll.moveToChild($el[0]);
                } 
            }, 0);

            return this;
        },

        show: function() {
            this.$el.addClass(this.visibleClass);

            return this;
        }
    });
}(jQuery, BoomCMS));

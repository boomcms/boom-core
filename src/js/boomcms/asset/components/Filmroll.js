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
                    configure_load: true,
                    resize: false
                });

                for (var i = 0; i < thumbnails.length; i++) {
                    thumbnails[i].$el.css('width', '100%');
                    thumbnails[i].loadImage();
                }
            }, 0);

            return this;
        },

        select: function(asset) {
            var $el = this.$el.find('[data-asset="' + asset.getId() + '"]').parents('.film_roll_child');

            this.$el.find('.selected').removeClass('selected');

            if ($el.length) {
                $el.addClass('selected');
                this.filmroll.moveToChild($el[0]);
                this.$el.find('.film_roll_pager .active').addClass('selected');
            } 

            return this;
        },

        show: function() {
            this.$el.addClass(this.visibleClass);

            return this;
        }
    });
}(jQuery, BoomCMS));

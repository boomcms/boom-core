(function($, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Filmroll = Backbone.View.extend({
        el: '#b-assets-filmroll',

        initialize: function(options) {
            this.assets = options.assets;

            this.listenTo(this.assets, 'reset', this.render);
        },

        /**
         * Init the filmroll plugin
         *
         * These needs to be called after render() and after the element has been inserted into the DOM
         *
         * Otherwise Filmroll calculates its width as 0
         *
         * @returns {undefined}
         */
        initFilmroll: function() {
            this.filmroll = new FilmRoll({
                container: this.$el.find('> div'),
                scroll: false,
                configure_load: true,
                resize: false
            });

            for (var i = 0; i < this.thumbnails.length; i++) {
                this.thumbnails[i].$el.css('width', '100%');
                this.thumbnails[i].loadImage();
            }
        },

        render: function() {
            var filmroll = this,
                $container = $('<div></div>');

            this.thumbnails = [];

            this.$el.html($container);

            this.assets.each(function(asset) {
                var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                        model: asset
                    }).render(),
                    width = Math.floor(150 * asset.getAspectRatio());

                thumbnail.$el.css('width', width);

                $container.append($('<div></div>').append(thumbnail.$el));

                filmroll.thumbnails.push(thumbnail);
            });

            this.initFilmroll();

            return this;
        },

        select: function(asset) {
            var $el = this.$el.find('[data-asset="' + asset.getId() + '"]').parent().parent();

            this.$el.find('.selected').removeClass('selected');

            if ($el.length) {
                $el.addClass('selected');
                this.filmroll.moveToChild($el[0]);
                this.$el.find('.film_roll_pager .active').addClass('selected');
            } 

            return this;
        }
    });
}(jQuery, BoomCMS));

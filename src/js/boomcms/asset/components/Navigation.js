(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Navigation = Backbone.View.extend({
        el: '#b-assets-navigation',
        centerOffset: 0,
        thumbnails: [],

        calculateInserts: function(assets) {
            var inserts = [];

            for (var i = 0; i < assets.length; i++) {
                if (!this.hasThumbnail(assets.models[i])) {
                    var thumbnail = this.createThumbnail(assets.models[i]),
                        after = i === 0 ? null : assets.models[i - 1];

                    inserts.push({
                        after: after,
                        thumbnail: thumbnail
                    });
                }
            }

            return inserts;
        },

        calculateRemovals: function(assets) {
            var thumbnails = this.thumbnails,
                removals = [];

            for (var i = 0; i < thumbnails.length; i++) {
                if (!assets.findById(thumbnails[i].model.getId())) {
                    removals.push(thumbnails[i]);
                }
            }

            return removals;
        },

        centerActiveAsset: function() {
            var middle = this.$el.width() / 2,
                $active = this.$el.find('.active'),
                activeMiddle, moveBy;

            if ($active.length === 0) {
                return;
            }

            activeMiddle = ($active.offset().left + ($active.outerWidth(true) / 2)) - this.centerOffset;
            moveBy =  middle - activeMiddle;

            this.$el
                .find('> div')
                .animate({
                    left: moveBy + 'px'
                }, 300);

            this.centerOffset = moveBy;
        },

        createThumbnail: function(asset) {
            var thumbnail = new BoomCMS.AssetManager.Thumbnail({
                    model: asset
                }).render(),
                width = Math.floor(98 * asset.getAspectRatio());

            thumbnail.$el
                .attr('data-width', width)
                .css('width', width);

            this.thumbnails.push(thumbnail);

            return thumbnail;
        },

        hasThumbnail: function(asset) {
            for (var i = 0; i < this.thumbnails.length; i++) {
                if (this.thumbnails[i].model.getId() === asset.getId()) {
                    return true;
                }
            }

            return false;
        },

        initialize: function(options) {
            this.selection = options.selection;
            this.$container = $('<div></div>');

            this.$el.html(this.$container);
            this.recenterActiveAssetOnWindowResize();
        },

        insertThumbnail: function(thumbnail, after, timeout) {
            // Wrap the thumbnail element in a div which has the padding and border to indicate the active asset
            var $el = $('<div></div>').html(thumbnail.$el).css('width', 0);

            $el.animate({
                width: parseFloat($el.find('> div').attr('data-width')) + 20 + 'px'
            }, timeout);

            if (after === null) {
                return this.$container.prepend($el);
            }

            this.$container
                .find('[data-asset="' + after.getId() + '"]')
                .parent()
                .parent()
                .after($el);
        },

        loadImages: function(inserts) {
            setTimeout(function() {
                for (var i = 0; i < inserts.length; i++) {
                    inserts[i].thumbnail.loadImageOnce();
                }
            }, 0);
        },

        recenterActiveAssetOnWindowResize: function() {
            var navigation = this,
                resizeTimeout = null;

            $(window)
                .on('resize', function() {
                    if (resizeTimeout !== null) {
                        clearTimeout(resizeTimeout);
                    }

                    resizeTimeout = setTimeout(function() {
                        navigation.centerActiveAsset();
                    }, 100);
                });
        },

        removeThumbnail: function(thumbnail, timeout) {
            var thumbnails = this.thumbnails,
                $el = thumbnail.$el.parent();

            for (var i = 0; i < thumbnails.length; i++) {
                if (thumbnails[i] === thumbnail) {
                    thumbnails.splice(i, 1);
                }
            }

            if (timeout === 0) {
                return $el.remove();
            }

            $el
                .animate({
                    width: 0
                }, timeout, function() {
                    $el.remove();
                });
        },

        /**
         * Show the navigation bar with the given assets
         *
         * The assets are compared whats currently displayed and inserts and removals are calculated
         * This is better than starting again each time as it allows the active asset to be scrolled nicely into the center of the viewport
         * 
         * This is done by animating removed assets to a width of 0 and animating inserted assets to their full width
         *
         * @param {type} assets
         * @param {type} active
         */
        render: function(assets, active) {
            var navigation = this,
                freshStart, inserts, removals, i, timeout;

            removals = this.calculateRemovals(assets);
            inserts = this.calculateInserts(assets);

            freshStart = (inserts.length + removals.length === this.thumbnails.length);

            for (i = 0; i < removals.length; i++) {
                timeout = freshStart ? 0 : i * 100;

                this.removeThumbnail(removals[i], timeout);
            }

            for (i = 0; i < inserts.length; i++) {
                timeout = freshStart ? 0 : i * 100;

                this.insertThumbnail(inserts[i].thumbnail, inserts[i].after, timeout);
            }

            this.setActive(active);
            this.loadImages(inserts);

            timeout = freshStart ? 0 : (Math.abs(inserts.length - removals.length) * 100) + 100;

            setTimeout(function() {
                navigation.centerActiveAsset();
            }, timeout);
        },

        setActive: function(asset) {
            var active = 'active';

            this.$container
                .find('.' + active)
                .removeClass(active)
                .end()
                .find('[data-asset="' + asset.getId() + '"]')
                .parent()
                .parent()
                .addClass(active);
        }
    });
}(jQuery, Backbone, BoomCMS));

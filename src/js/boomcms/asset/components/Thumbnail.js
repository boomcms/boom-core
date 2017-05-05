(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
        selected: 'selected',
        tagName: 'div',

        initialize: function() {
            var view = this,
                model = this.model,
                $el = this.$el;

            this.template = _.template($('#b-asset-thumb').html());

            this.listenTo(model, 'change change:image', function() {
                view.render();
                view.loadImage();
            });

            this.listenTo(model, 'select', this.select);
            this.listenTo(model, 'unselect', this.unselect);

            $el
                .on('click', function(e) {
                    e.preventDefault();
                })
                .data('model', model)
                .dblclick()
                .on('sclick', function() {
                    view.toggleSelected();

                    $el.blur();
                })
                .on('dclick', function() {
                    model.trigger('view', model);
                })
                .on('click', '.edit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    model.trigger('view', model);
                })
                .on('justified', function() {
                    view.loadImage();
                })
                .on('keydown', '.thumb', function(e) {
                    if (e.which === $.ui.keyCode.DELETE || e.which === $.ui.keyCode.BACKSPACE) {
                        e.preventDefault();
                        e.stopPropagation();

                        model.destroy();
                    }
                })
                .on('keypress', '.thumb', function(e) {
                    if (e.which === $.ui.keyCode.ENTER) {
                        model.trigger('select', model);

                        $el.blur();
                    }
                });
        },

        /**
         * Load the image after if has been justified
         * 
         * Ensures that an image can be loaded to the correct dimensions of the thumbnail.
         *
         * Rounds dimensions to the nearest 500px to avoid excessive generation of thumbnail sizes.
         *
         * @returns {undefined}
         */
        loadImage: function() {
            var asset = this.model;

            this.$el
                .find('[data-asset]')
                .each(function() {
                    var $this = $(this),
                        width = Math.round(($this.width() + 1) / 500) * 500,
                        height = Math.round(($this.height() + 1) / 500) * 500,
                        url = asset.getUrl('thumb', width, height) + '?' + asset.getEditedAt(),
                        loadingClass = 'loading';

                    $this.find('img')
                        .attr('src', url)
                        .on('load', function() {
                            $(this).parent().removeClass(loadingClass);
                        })
                        .on('error', function() {
                            $(this).parent().removeClass(loadingClass).addClass('failed');
                        });
                });
        },

        render: function() {
            var aspectRatio = this.model.getAspectRatio();

            this.$el
                .html(this.template({
                    asset: this.model
                }))
                .attr('data-aspect-ratio', aspectRatio > 0 ? aspectRatio : 1);

            this.$thumbnail = this.$('.b-assets-thumbnail');

            return this;
        },

        select: function() {
            this.$thumbnail.addClass(this.selected);
        },

        toggleSelected: function() {
            var event = this.$thumbnail.hasClass(this.selected) ? 'unselect' : 'select';

            this.model.trigger(event, this.model);
        },

        unselect: function() {
            this.$thumbnail.removeClass(this.selected);
        }
    });
}(jQuery, Backbone, BoomCMS));

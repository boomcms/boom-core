(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
		tagName: 'div',

		initialize: function() {
			var view = this,
				model = this.model,
				$el = this.$el;

			this.template = _.template($('#b-asset-thumb').html());

			this.listenTo(model, 'change', this.render);

			$el
				.dblclick()
				.on('sclick', function() {
					model.trigger('select', {
						asset: model,
						$el: $el
					});
				})
				.on('dclick', function() {
					model.trigger('view', model);
				})
				.on('click', '.edit', function(e) {
					e.stopPropagation();

					model.trigger('view', model);
				})
				.on('justified', function() {
					view.loadImage();
				});
		},

		/**
		 * Load the image after if has been justified
		 * 
		 * Ensures that an image can be loaded to the correct dimensions of the thumbnail.
		 *
		 * @returns {undefined}
		 */
		loadImage: function() {
			var asset = this.model;

			this.$el
				.find('[data-asset]')
				.each(function() {
					var $this = $(this),
						width = Math.round(($this.width() + 1) / 10) * 10,
						height = Math.round(($this.height() + 1) / 10) * 10,
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
			this.$el
				.html(this.template({
					asset: this.model
				}))
				.attr('data-aspect-ratio', this.model.getAspectRatio());

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));
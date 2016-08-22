(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
		tagName: 'div',

		initialize: function() {
			var model = this.model,
				$el = this.$el;

			this.template = _.template($('#b-asset-thumb').html());

			this.listenTo(model, 'change replace revert', this.render);

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
				});
		},

		render: function() {
			var asset = this.model;

			this.$el
				.html(this.template({
					asset: asset
				}))
				.find('[data-asset]')
				.each(function() {
					var $this = $(this),
						url  = asset.getUrl('thumb', $this.width(), $this.height()) + '?' + Math.floor(Date.now() / 1000),
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

			if (!this.$el.attr('data-aspect-ratio')) {
				this.$el
					.css({
						height: '160px',
						width: Math.floor(160 * asset.getAspectRatio()) + 'px'
					})
					.attr('data-aspect-ratio', asset.getAspectRatio());
			}

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));
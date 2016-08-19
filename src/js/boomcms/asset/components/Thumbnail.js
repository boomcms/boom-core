(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Thumbnail = Backbone.View.extend({
		tagName: 'div',

		initialize: function() {
			var model = this.model,
				$el = this.$el;

			this.template = _.template($('#b-asset-thumb').html());

			this.listenTo(model, 'change', this.render);

			$el
				.on('click', function() {
					model.trigger('select', {
						asset: model,
						$el: $el
					});
				})
				.on('click', '.edit', function(e) {
					e.stopPropagation();
				});
		},

		render: function() {
			this.$el
				.html(this.template({
					asset: this.model
				}));

			if (!this.$el.attr('data-aspect-ratio')) {
				this.$el
					.css({
						height: '160px',
						width: Math.floor(160 * this.model.getAspectRatio()) + 'px'
					})
					.attr('data-aspect-ratio', this.model.getAspectRatio());
			}

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));
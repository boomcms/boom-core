(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.ViewSelection = Backbone.View.extend({
		routePrefix: 'selection',
		templateSelector: '#b-assets-selection-template',

		bind: function() {
			var view = this,
				selection = this.selection;

			this.$el
				.on('click', '.b-settings-close', function(e) {
					view.close(e);
				})
				.on('click', '.b-assets-delete', function() {
					selection.destroy();
				})
				.on('click', 'a[data-section]', function() {
					var section = $(this).attr('data-section');

					view.router.navigate(view.routePrefix + '/' + selection.getIdString() + '/' + section);

					if (section === 'tags') {
						view.showTags();
					}
				})
				.on('submit', '#b-assets-download-filename', function(e) {
					e.preventDefault();

					var filename = $(this).find('input[name=filename]').val();

					selection.download(filename);
				});

			this.$('.b-settings-menu a[href^=#]').boomTabs();
			this.$('time').localTime();
		},

		close: function() {
			this.$el.remove();
			this.router.navigate('', {trigger: true});
		},

		getSection: function() {
			return this.$('a[data-section].selected').attr('data-section');
		},

		init: function(options) {
			var view = this;

			this.assets = options.assets;
			this.router = options.router;

			this.template = _.template($(this.templateSelector).html());

			this.listenTo(this.selection, 'sync', function() {
				this.render(view.getSection());
			});

			this.listenTo(this.selection, 'destroy', function() {
				view.close();
			});
		},

		initialize: function(options) {
			this.selection = options.selection;

			this.init(options);
		},

		render: function(section) {
			this.$el.html(this.template({
				selection: this.selection,
				section: section
			}));

			this.bind();

			return this;
		}
	});
}(jQuery, Backbone, BoomCMS));

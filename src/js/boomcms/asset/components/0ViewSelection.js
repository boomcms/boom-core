(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.ViewSelection = Backbone.View.extend({
		tagName: 'div',
		tagsDisplayed: false,
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

					view.router.navigate('selection/' + selection.getIdString() + '/' + section);

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

		displayTags: function(tags) {
			var view = this,
				$tagList = this.$('.b-tags').eq(0),
				$tagTemplate = this.$('#b-tag-template').html(),
				$el;

			for (var i = 0; i < tags.length; i++) {
				$el = $($tagTemplate);

				$el
					.find('a')
					.attr('data-tag', tags[i])
					.find('span:first-of-type')
					.text(tags[i]);

				$tagList.append($el);
			}

			this.$el
				.on('click', '.b-tags a', function(e) {
					e.preventDefault();

					view.toggleTag($(this));
				});
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

			if (section === 'tags') {
				this.showTags();
			}

			this.bind();

			return this;
		},

		showTags: function() {
			if (this.tagsDisplayed === false) {
				var view = this,
					allTags = new BoomCMS.Collections.Assets([]).getTags();

				allTags.done(function(tags) {
					view.displayTags(tags);
				});

				$.when(allTags, this.selection.getTags()).done(function(response1, response2) {
					if (typeof response2[0] !== 'undefined') {
						var tags = response2[0];

						for (var i = 0; i < tags.length; i++) {
							view.$('.b-tags').find('a[data-tag="' + tags[i] + '"]').addClass('active');
						}
					}
				});

				this.tagsDisplayed = true;
			}
		},

		toggleTag: function($a) {
			var activeClass = 'active',
				funcName = $a.hasClass(activeClass) ? 'removeTag' : 'addTag';

			this.selection[funcName]($a.attr('data-tag')).done(function() {
				$a.toggleClass(activeClass).blur();
			});
		}
	});
}(jQuery, Backbone, BoomCMS));

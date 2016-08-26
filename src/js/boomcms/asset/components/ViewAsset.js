(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.ViewAsset = Backbone.View.extend({
		tagName: 'div',

		bind: function() {
			var view = this,
				asset = this.model;

			this.$el
				.find('#b-tags')
				.assetTagSearch({
					addTag: function(e, tag) {
						asset.addTag(tag);
					},
					removeTag: function(e, tag) {
						asset.removeTag(tag);
					}
				})
				.end()
				.on('click', '.b-settings-close', function(e) {
					view.close(e);
				})
				.on('click', '.b-assets-delete', function() {
					asset.destroy();
				})
				.on('click', '.b-assets-revert', function(e) {
					e.preventDefault();

					asset.revertToVersion($(this).parents('li').attr('data-version-id'));
				})
				.on('click', '.b-assets-save', function() {
					asset
						.set(view.$('form').serializeJSON())
						.save();

					BoomCMS.notify("Asset details saved");
				})
				.on('focus', '#thumbnail', function() {
					var $this = $(this);

					new boomAssetPicker(asset)
						.done(function(asset) {
							$this.val(asset.getId());
						});
				})
				.on('click', 'a[data-section]', function() {
					var section = $(this).attr('data-section');

					view.router.navigate('asset/' + view.model.getId() + '/' + section);

					if (section === 'tags') {
						view.showTags();
					}
				});

			this.$('.b-assets-upload').assetUploader({
				asset: asset,
				uploadFinished: function(e, data) {
					asset.set(data.result);

					view.render('info');
				}
			});

			this.$('.b-settings-menu a[href^=#]').boomTabs();
			this.$('time').localTime();
		},

		bindRouter: function() {
			var view = this;

			this.router.on('viewAsset', function(asset, section) {

			});
		},

		close: function() {
			this.$el.remove();
			this.router.navigate('', {trigger: true});
		},

		displayTags: function(tags) {
			var $tagList = this.$('.b-tags').eq(0),
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
		},

		getSection: function() {
			return this.$('a[data-section].selected').attr('data-section');
		},

		initialize: function(options) {
			var view = this;

			this.assets = options.assets;
			this.router = options.router;

			this.template = _.template($('#b-assets-view-template').html());

			this.listenTo(this.model, 'destroy', function() {
				this.close();
			});

			this.listenTo(this.model, 'revert', function() {
				BoomCMS.notify('This asset has been reverted to the previous version');
			});

			this.listenTo(this.model, 'change:image revert', function(e) {
				this.render('info');
			});

			this.listenTo(this.model, 'sync', function() {
				this.render(view.getSection());
			});

			this.bindRouter();
		},

		initImageEditor: function() {
			var asset = this.model;

			this.$('.b-asset-imageeditor').imageEditor({
				save: function(e, blob) {
					asset.replaceWith(blob);
				}
			});
		},

		render: function(section) {
			this.$el.html(this.template({
				asset: this.model,
				section: section
			}));

			if (section === 'tags') {
				this.showTags();
			}

			this.bind();
			this.initImageEditor();

			return this;
		},

		showTags: function() {
			var view = this,
				allTags = this.assets.getAllTags();

			allTags.done(function(tags) {
				view.displayTags(tags);
			});

			$.when(allTags, this.model.getTags()).done(function(response1, response2) {
				if (typeof response2[0] !== 'undefined') {
					var tags = response2[0];

					for (var i = 0; i < tags.length; i++) {
						view.$('.b-tags').find('a[data-tag="' + tags[i] + '"]').addClass('active');
					};
				}
			});
		}
	});
}(jQuery, Backbone, BoomCMS));

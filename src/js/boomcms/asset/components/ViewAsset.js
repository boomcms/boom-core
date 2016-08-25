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
				.on('click', '.b-assets-download', function(e) {
					e.preventDefault();
					asset.download();
				})
				.on('click', '.b-assets-replace', function(e) {
					var uploadFinished = view.uploader.assetUploader('option', 'uploadFinished');

					e.preventDefault();

					view.uploader.assetUploader('replacesAsset', asset);
					view.uploader.assetUploader('option', 'uploadFinished', function(e, data) {
						view.reloadPreviewImage();
						uploadFinished(e, data);

						// Restore the previous event handler.
						view.uploader.assetUploader('option', 'uploadFinished', uploadFinished);
					});

					view.uploader.show();
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

					new boomAssetPicker(new BoomCMS.Asset({id: $this.val()}))
						.done(function(asset) {
							$this.val(asset.getId());
						});
				})
				.on('click', 'a[data-section]', function() {
					var section = $(this).attr('data-section');

					view.router.navigate('asset/' + view.model.getId() + '/' + section);
				});

			this.$('.b-assets-upload').assetUploader({
				asset: this.model,
				uploadFinished: function() {
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
				$el.find('span:first-of-type').text(tags[i]);

				$tagList.append($el);
			}
		},

		initialize: function(options) {
			this.assets = options.assets;
			this.router = options.router;

			this.template = _.template($('#b-assets-view-template').html());

			this.listenTo(this.model, 'destroy', function() {
				this.close();
			});

			this.listenTo(this.model, 'revert', function() {
				BoomCMS.notify("This asset has been reverted to the previous version");
			});

			this.listenTo(this.model, 'change sync revert', function() {
				this.render();
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
			var view = this;

			this.$el.html(this.template({
				asset: this.model
			}));

			this.assets.getAllTags().done(function(tags) {
				view.displayTags(tags);
			});

			this.bind();
			this.initImageEditor();

			if (section) {
				this.showSection(section);
			}

			return this;
		},

		showSection: function(section) {
			this.$('a[data-section=' + section + ']').click();
		}
	});
}(jQuery, Backbone, BoomCMS));

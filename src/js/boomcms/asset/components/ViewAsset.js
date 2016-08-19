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
				.on('click', '.b-settings-menu a', function(e) {
					var $this = $(this),
						href = $this.attr('href'),
						$target;

					if (href === '#') {
						return;
					}

					$target = view.$(href);

					if ($target.length) {
						e.preventDefault();

						view.$('.b-settings-menu li').removeClass('selected');
						$this.parent().addClass('selected');

						view.$('.b-settings-content > *').hide();
						$target.show();
					}
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

					view.revertTo($(this).attr('data-version-id'));
				})
				.on('click', '.b-assets-openeditor', function(e) {
					e.preventDefault();

					new boomImageEditor(asset.getUrl() + '?' + new Date().getTime())
						.done(function(blob) {
							view.replaceWithBlob(blob);
						});
				})
				.on('click', '.b-assets-save', function() {
					view.selection
						.save(view.dialog.contents.find('form').serialize())
						.done(function() {
							new boomNotification("Asset details saved").show();
						});
				})
				.on('focus', '#thumbnail', function() {
					var $this = $(this);

					new boomAssetPicker(new BoomCMS.Asset({id: $this.val()}))
						.done(function(asset) {
							$this.val(asset.getId());
						});
				});

			this.$('#b-assets-view-edit').imageEditor({
				imageUrl: asset.getUrl()
			});
		},

		close: function(e) {
			e.preventDefault();

			this.$el.remove();
		},

		initialize: function() {
			this.listenTo(this.model, 'destroy', function(e) {
				this.close(e);
			});
		},

		reloadPreviewImage: function() {
			var $img = this.dialog.contents.find('.b-assets-preview img');

			$img.attr("src", $img.attr('src') + '?' + new Date().getTime());
		},

		render: function() {
			var view = this;

			this.$el
				.load(this.model.url(), function() {
					view.bind();
				});

			return this;
		},
	
		replaceWithBlob: function(blob) {
			this.model.replaceWith(blob);
		},

		revertTo: function(versionId) {
			var assetEditor = this;

			this.selection.revertToVersion(versionId)
				.done(function() {
					new boomNotification("This asset has been reverted to the previous version").show();
					assetEditor.reloadPreviewImage();
				});
		}
	});
}(jQuery, Backbone, BoomCMS));

(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager = Backbone.View.extend({
		el: 'body',

		assets: new BoomCMS.Collections.Assets(),
		selection: new BoomCMS.Collections.Assets(),

		selectedClass: 'selected',

		assetsUploaded: function() {
			var assetManager = this;

			assetManager.getAssets();
			assetManager.uploader.assetUploader('reset');
			assetManager.uploader.assetUploader('close');
		},

		bind: function() {
			var assetManager = this;

			this.$el
				.on('click', '#b-button-multiaction-delete', function() {
					assetManager.selection.delete()
						.done(function() {
							assetManager.getAssets();
							assetManager.clearSelection();
					});
				})
				.on('click', '#b-button-multiaction-download', function() {
					assetManager.selection.download();
				})
				.on('click', '#b-assets-select-all', function() {
					assetManager.selectAll();

					$(this).blur();
				})
				.on('click', '#b-assets-select-none', function() {
					assetManager.clearSelection();

					$(this).blur();
				})
				.on('click', '#b-button-multiaction-tag', function() {
					assetManager.selection.tag();
				})
				.on('click', '#b-assets-upload', function() {
					assetManager.uploader.show();
				})
				.on('click', '#b-assets-search', function() {
					$('#b-assets-filters').toggleClass('visible');
					$(this).toggleClass('open');
				});

			this.uploader
				.assetUploader({
					uploadFinished: function(e, data) {
						assetManager.assetsUploaded(data.result);
					},
					uploadFailed: function() {
						// Update asset list even though an error occurred
						// For situations where multiple files were uploaded but one caused an error.
						assetManager.getAssets();
					}
				})
				.on('click', '#b-assets-upload-close', function(e) {
					e.preventDefault();

					assetManager.uploader.assetUploader('close');
				});
		},

		clearSelection: function() {
			this.selection.reset();

			this.$('#b-assets-view-thumbs .selected').removeClass(this.selectedClass);
		},

		getAssets: function() {
			this.element.assetSearch('getAssets');
		},

		initialize: function() {
			this.router = new BoomCMS.AssetManager.Router({assets: this.assets});
			this.uploader = this.$('#b-assets-upload-form');

			this.listenTo(this.assets, 'select', this.select);
			this.listenTo(this.assets, 'view', this.viewAsset);
			this.listenTo(this.selection, 'reset update', this.toggleButtons);

			this.assets.once('sync', function() {
				Backbone.history.start();
			});

			this.$el.assetSearch({assets: this.assets});
			this.bind();
		},

		selectAll: function() {
			this.selection = this.assets;
			this.selection.trigger('update');

			this.$('#b-assets-view-thumbs .thumb').addClass(this.selectedClass);
		},

		select: function(data) {
			var $el = data.$el,
				asset = data.asset,
				selection = this.selection,
				method = selection.findWhere({id: asset.getId()}) ? 'remove' : 'add';

			$el.find('.thumb').toggleClass(this.selectedClass).blur();

			selection[method]('asset');
		},

		toggleButtons: function() {
			var buttons = $('[id|=b-button-multiaction]');

			buttons.prop('disabled', this.selection.length ? false : true);
		},

		updateTagFilters: function(tags) {
			var assetManager = this;

			this.addFilter('tag', tags);
			this.getAssets();
		},

		viewAsset: function(asset) {
			var assetManager = this;

			new boomAssetEditor(asset, assetManager.uploader)
				.always(function() {
					assetManager.router.navigate('');
				});
		}
	});
}(Backbone, BoomCMS));

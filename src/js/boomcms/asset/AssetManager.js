(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager = Backbone.View.extend({
		el: 'body',

		assets: new BoomCMS.Collections.Assets(),
		selection: new BoomCMS.Collections.Assets(),

		selectedClass: 'selected',
		hideThumbsClass: 'hide-thumbs',

		assetsUploaded: function() {
			this.router.navigate('home', {trigger: true});
			this.uploader.assetUploader('reset');
			this.uploader.assetUploader('close');
		},

		bind: function() {
			var assetManager = this;

			this.$el
				.on('click', '#b-button-multiaction-delete', function() {
					BoomCMS
						.confirm('Please confirm', 'Are you sure you want to delete the selected assets?')
						.done(function() {
							assetManager.selection.destroy();
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
					assetManager.router.navigate('upload', {trigger: true});
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
					assetManager.showThumbs();
					assetManager.router.navigate('home');
				});
		},

		bindRoutes: function() {
			var assetManager = this;

			this.router
				.on('upload', function() {
					assetManager.uploader.show();
					assetManager.hideThumbs();
				})
				.on('home', function() {
					if (assetManager.assets.length <= 1) {
						assetManager.getAssets();
					}

					assetManager.showThumbs();
				});

			Backbone.history.start();
		},

		clearSelection: function() {
			this.selection.reset();

			this.$('#b-assets-view-thumbs .selected').removeClass(this.selectedClass);
		},

		getAssets: function() {
			this.$el.assetSearch('getAssets');
		},

		hideThumbs: function() {
			this.$content.addClass(this.hideThumbsClass);
		},

		initialize: function() {
			var assetManager = this;

			this.router = new BoomCMS.AssetManager.Router({assets: this.assets});

			this.uploader = this.$('#b-assets-upload-form');
			this.$content = this.$('#b-assets-content');

			this.listenTo(this.assets, 'select', this.select);
			this.listenTo(this.assets, 'view', this.viewAsset);
			this.listenTo(this.assets, 'destroy', function() {
				assetManager.getAssets();
				assetManager.clearSelection();
			});
			this.listenTo(this.selection, 'reset update', this.toggleButtons);

			this.$el.assetSearch({assets: this.assets});
			this.bind();
			this.bindRoutes();
		},

		showThumbs: function() {
			this.$content.removeClass(this.hideThumbsClass);
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

			selection[method](asset);
		},

		toggleButtons: function() {
			var buttons = $('[id|=b-button-multiaction]');

			buttons.prop('disabled', this.selection.length ? false : true);
		},

		updateTagFilters: function(tags) {
			this.addFilter('tag', tags);
			this.getAssets();
		},

		viewAsset: function(asset, section) {
			var assetManager = this,
				view = new BoomCMS.AssetManager.ViewAsset({model: asset}).render();

			this.$content.prepend(view.$el);
			this.hideThumbs();

			assetManager.router.navigate('asset/' + asset.getId());

			if (section) {
				view.on('loaded', function() {
					view.$('a[data-section="' + section + '"]').click();
				});
			}

			view.$el
				.on('remove', function() {
					assetManager.router.navigate('', {trigger: true});
				})
				.on('click', 'a[data-section]', function() {
					var section = $(this).attr('data-section');

					assetManager.router.navigate('asset/' + asset.getId() + '/' + section);
				});
		}
	});
}(Backbone, BoomCMS));

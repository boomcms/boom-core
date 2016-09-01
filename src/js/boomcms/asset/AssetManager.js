(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager = Backbone.View.extend({
		el: 'body',

		assets: new BoomCMS.Collections.Assets(),
		selection: new BoomCMS.Collections.Assets(),

		types: {},

		selectedClass: 'selected',
		hideThumbsClass: 'hide-thumbs',

		assetsUploaded: function() {
			this.router.navigate('', {trigger: true});
			this.uploader.assetUploader('close');
			this.uploader.assetUploader('reset');

			this.getAssets();
		},

		bind: function() {
			var assetManager = this;

			this.$el
				.on('click', '#b-assets-selection-delete', function() {
					assetManager.viewSelection(assetManager.selection, 'delete');
				})
				.on('click', '#b-assets-selection-download', function() {
					assetManager.viewSelection(assetManager.selection, 'download');
				})
				.on('click', '#b-assets-select-all', function() {
					assetManager.selectAll();

					$(this).blur();
				})
				.on('click', '#b-assets-select-none', function() {
					assetManager.clearSelection();

					$(this).blur();
				})
				.on('click', '#b-assets-selection-tag', function() {
					assetManager.viewSelection(assetManager.selection, 'tags');
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
				.on('click', '.b-assets-upload-close', function(e) {
					e.preventDefault();

					assetManager.uploader.assetUploader('close');
					assetManager.router.navigate('', {trigger: true});
				});
		},

		bindRoutes: function() {
			var assetManager = this;

			this.router
				.on('selection', function(assetIds, section) {
					assetManager.selection.reset();

					for (var i = 0; i < assetIds.length; i++) {
						var asset = new BoomCMS.Asset({id: assetIds[i]});
						asset.fetch();

						assetManager.selection.add(asset);
					}

					assetManager.viewSelection(assetManager.selection, section);
				})
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
			this.$content = this.$('#b-assets-content');
			this.uploader = this.$content.find('> .b-assets-upload .b-assets-upload-form').eq(0);

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
			this.uploader.hide();
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
			var buttons = this.$('.b-assets-multi');

			buttons.prop('disabled', this.selection.length ? false : true);
		},

		updateTagFilters: function(tags) {
			this.addFilter('tag', tags);
			this.getAssets();
		},

		viewAsset: function(asset, section) {
			var view = new BoomCMS.AssetManager.ViewAsset({
				model: asset,
				assets: this.assets,
				router: this.router
			});

			this.router
				.navigate('asset/' + asset.getId() + '/' + section)
				.once('home', function() {
					view.close();
				});

			view.render(section);
			this.$content.prepend(view.$el);
			this.hideThumbs();
		},

		viewSelection: function(selection, section) {
			var view = new BoomCMS.AssetManager.ViewSelection({
				selection: selection,
				assets: this.assets,
				router: this.router
			});

			this.router
				.navigate('selection/' + selection.getIdString() + '/' + section)
				.once('home', function() {
					view.close();
				});

			view.render(section);
			this.$content.prepend(view.$el);
			this.hideThumbs();
		}
	});
}(Backbone, BoomCMS));

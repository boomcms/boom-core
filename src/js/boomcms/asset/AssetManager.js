(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager = Backbone.View.extend({
		el: 'body',

		initialize: function() {
			this.router = new BoomCMS.AssetManager.Router();
			this.assets = new BoomCMS.Collections.Assets();

			this.gridView = new BoomCMS.AssetManager.GridView({
				assets: this.assets
			});
		}
	});
}(jQuery, Backbone, BoomCMS));

$.widget('boom.assetManager', {
	baseUrl: '/boomcms/assets/',
	listUrl: '/boomcms/assets/get',

	postData: {
		page: 1,
		limit: 30,
		order: 'last_modified desc'
	},

	selection: new boomAssetSelection(),

	addFilter: function(type, value) {
		this.postData.page = 1;
		this.postData[type] = value;
	},

	assetsUploaded: function() {
		var assetManager = this;

		assetManager.getAssets();
		assetManager.uploader.assetUploader('reset');
		assetManager.uploader.assetUploader('close');
	},

	bind: function() {
		var assetManager = this;

		this.bindContentArea();
		this.bindMenuButtons();

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

	bindContentArea: function() {
		var assetManager = this;

		this.element
			.on('change', '#b-assets-sortby', function(event) {
				assetManager.sortBy(this.value);
			})
			.on('change', '#b-assets-types', function(event) {
				assetManager.addFilter('type', this.selectedIndex? this.options[this.selectedIndex].value : '');
				assetManager.getAssets();
			})
			.on('click', '#b-assets-all', function(event) {
				assetManager.removeFilters();
				assetManager.getAssets();
			})
			.on('click', '.thumb .edit', function(e) {
				e.preventDefault();
				e.stopPropagation();

				assetManager.viewAsset($(this).parent());
			})
			.on('click', '.thumb', function(event) {
				event.preventDefault();

				var $this = $(this);

				assetManager.select($this.attr('data-asset'));

				$this
					.toggleClass('selected')
					.blur();
			});

		this.titleFilter = this.element
			.find('#b-assets-filter-title')
			.assetTitleFilter({
				search: function(event, ui) {
					assetManager.addFilter('title', $(this).val());
					assetManager.getAssets();
				},
				select: function(event, ui) {
					assetManager.addFilter('title', ui.item.value);
					assetManager.getAssets();
				}
			});

		this.element.find('#b-tags-search')
			.assetTagSearch({
				update: function(e, data) {
					assetManager.updateTagFilters(data.tags);
				}
			});
	},

	bindMenuButtons: function() {
		var assetManager = this;

		this.menu
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
	},

	clearSelection: function() {
		this.selection.clear();
		this.toggleButtons();

		this.element.find('#b-assets-view-thumbs .selected').removeClass('selected');
	},

	_create: function() {
		this.menu = this.element.find('#b-topbar');
		this.uploader = this.element.find('#b-assets-upload-form');
		this.bind();

		this.getAssets();
	},

	getAssets: function() {
		var assetManager = this;

		return $.post(this.listUrl, this.postData)
			.done(function(response) {
				assetManager.element
					.find('#b-assets-view-thumbs')
					.replaceWith(response.html);

				assetManager.element
					.find('#b-assets-view-thumbs')
					.justifyAssets()
					.find('[data-asset]')
					.assetManagerImages();

				assetManager.initPagination(response.total);
				assetManager.clearSelection();
			});
	},

	getPage: function(page) {
		if (this.postData.page !== page) {
			this.postData.page = page;
			this.getAssets();
		}
	},

	initPagination: function(total) {
		var assetManager = this,
			$el = assetManager.element.find('.b-pagination');

		// Max page isn't set correctly when re-initialising
		if ($el.data('jqPagination')) {
			$el.jqPagination('destroy');
		}

		$el.jqPagination({
			paged: function(page) {
				assetManager.getPage(page);
			},
			max_page: Math.ceil(total / this.postData.limit),
			current_page: total > 0 ? this.postData.page : 0
		});
	},

	removeFilters: function() {
		this.postData = {
			page: 1,
			limit: 30,
			order: 'last_modified desc'
		};

		this.element.find('#b-assets-types').val(0);

		this.getAssets();
	},

	selectAll: function() {
		var assetManager = this,
			$thumbs = this.element.find('#b-assets-view-thumbs .thumb');

		$thumbs.addClass('selected');

		$thumbs.each(function() {
			assetManager.select($(this).attr('data-asset'));
		});
	},

	select: function(assetId) {
		this.selection.add(assetId);

		this.toggleButtons();
	},

	sortBy: function(sort) {
		this.postData['order'] = sort;
		this.getAssets();
	},

	toggleButtons: function() {
		var buttons = $('[id|=b-button-multiaction]');

		buttons.prop('disabled', this.selection.length() ? false : true);
	},

	updateTagFilters: function(tags) {
		var assetManager = this;

		this.addFilter('tag', tags);
		this.getAssets();
	},

	viewAsset: function($el) {
		var assetManager = this;

		new boomAssetEditor(new BoomCMS.Asset({id: $el.attr('data-asset')}), assetManager.uploader)
			.fail(function() {
				assetManager.getAssets();
			});
	}
});

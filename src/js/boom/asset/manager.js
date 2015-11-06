$.widget('boom.assetManager', {
	baseUrl: '/cms/assets/',
	listUrl: '/cms/assets/get',
	postData: {
		page: 1
	},

	selection: new boomAssetSelection(),

	addFilter : function(type, value) {
		this.postData.page = 1;
		this.postData[type] = value;
	},

	assetsUploaded : function(assetIds) {
		var assetManager = this;

		assetManager.getAssets();
		assetManager.uploader.assetUploader('reset');
		assetManager.uploader.assetUploader('close');
	},

	bind : function() {
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

	bindContentArea : function() {
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
			.on('click', '.thumb a', function(event) {
				event.preventDefault();

				var $this = $(this);

				assetManager.select($this.attr('href').replace('#asset/', ''));
				$this.parent().parent().toggleClass('selected');
			});

		this.titleFilter = this.element
			.find('#b-assets-filter-title')
			.assetTitleFilter({
				search : function(event, ui) {
					assetManager.addFilter('title', $(this).val());
					assetManager.getAssets();
				},
				select : function(event, ui) {
					assetManager.addFilter('title', ui.item.value);
					assetManager.getAssets();
				}
			});

		this.element.find('#b-tags-search')
			.assetTagSearch({
				update : function(e, data) {
					assetManager.updateTagFilters(data.tags);
				}
			});
	},

	bindMenuButtons : function() {
		var assetManager = this;

		this.menu
			.on('click', '#b-button-multiaction-delete', function() {
				assetManager.selection.delete()
					.done(function() {
						assetManager.getAssets();
						assetManager.clearSelection();
				});
			})
			.on('click', '#b-button-multiaction-edit', function() {
				assetManager.viewAsset();
				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-download', function() {
				assetManager.selection.download();
			})
			.on('click', '#b-button-multiaction-clear', function() {
				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-tag', function() {
				assetManager.selection.tag();
			})
			.on('click', '#b-assets-upload', function() {
				assetManager.uploader.show();
			});
	},

	clearSelection : function() {
		this.selection.clear();
		this.toggleButtons();

		this.element.find('#b-assets-view-thumbs div').removeClass('selected');
	},

	_create : function() {
		this.menu = this.element.find('#b-topbar');
		this.uploader = this.element.find('#b-assets-upload-form');
		this.bind();

		this.getAssets();
	},

	getAssets : function() {
		var assetManager = this;

		return $.post(this.listUrl, this.postData)
			.done(function(response) {
				var $response = $(response);

				assetManager.element
					.find('#b-assets-content')
					.html($($response[0]).html());

				assetManager.element
					.find('#b-assets-view-thumbs')
					.justifyAssets()
					.find('[data-asset]')
					.assetManagerImages();

				assetManager.element
					.find('.b-pagination')
					.replaceWith($response[2]);

				assetManager.initPagination();
				assetManager.clearSelection();
				assetManager.updateContentAreaMargin();
			});
	},

	getPage : function(page) {
		if (this.postData.page !== page) {
			this.addFilter('page', page);
			this.getAssets();
		}
	},

	initPagination : function() {
		var assetManager = this;

		assetManager.element.find('.b-pagination')
			.jqPagination({
				paged: function(page) {
					assetManager.getPage(page);
				}
			});
	},

	removeFilters : function() {
		this.postData = {
			page : 1
		};

		this.element.find('#b-assets-types').val(0);

		var $title = this.element.find('#b-assets-filter-title');
		$title.val($title.attr('placeholder'));

		this.getAssets();
	},

	select : function(assetId) {
		this.selection.add(assetId);

		this.toggleButtons();
	},

	sortBy : function(sort) {
		this.postData['sortby'] = sort;
		this.getAssets();
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').prop('disabled', this.selection.length() == 1 ? false : true);
		buttons.prop('disabled', this.selection.length() ? false : true);
	},

	updateContentAreaMargin : function() {
		// The filters bar will now be higher so move the content box down.
		// Filters bar is position: fixed so this won't happen automatically.
		var $filters = this.element.find('#b-assets-filters');
		this.element.find('#b-assets-content').css('padding-top', $filters.outerHeight() + ($filters.offset().top) + 'px');
	},

	updateTagFilters : function(tags) {
		var assetManager = this;

		this.addFilter('tag', tags);
		this.getAssets();
	},

	viewAsset : function() {
		var assetManager = this;

		new boomAssetEditor(new boomAsset(this.selection.index(0)), assetManager.uploader)
			.fail(function() {
				assetManager.getAssets();
			});
	}
});

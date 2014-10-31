$.widget('boom.assetManager', {
	baseUrl : '/cms/assets/',
	listUrl : '/cms/assets/list',
	filters : {
		page : 1
	},

	selected : [],

	tag : 0,
	sortby : '',

	addFilter : function(type, value) {
		this.filters.page = 1;
		this.filters[type] = value;
	},

	assetsUploaded : function(assetIds) {
		var assetManager = this;

		assetManager.getAssets();
		assetManager.uploader.hide();
	},

	bind : function() {
		var assetManager = this;

		this.bindContentArea();
		this.bindMenuButtons();

		this.uploader
			.assetUploader({
				done : function(e, data) {
					assetManager.assetsUploaded(data.result);
				}
			});
	},

	bindContentArea : function() {
		var assetManager = this;

		this.element
			.on('change', '#b-assets-sortby', function(event) {
				assetManager.sortBy(this.value);
			})
			.on('change', '#b-assets-types', function(event) {
				if (this.selectedIndex) {
					assetManager.addFilter('type', this.options[this.selectedIndex].innerHTML);
				} else {
					assetManager.addFilter('type', '');
				}

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
			})
			.on('focus', '#b-assets-filter-title, #b-tags-search input', function() {
				var $this = $(this);

				if ($this.val() == $this.attr('placeholder')) {
					$this.val('');
				}
			})
			.on('blur', '#b-assets-filter-title, #b-tags-search input', function() {
				var $this = $(this);

				if ($this.val() == '') {
					$this.val($this.attr('placeholder'));
				}
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
			.tagger_search({
				update : function(tagIds) {
					assetManager.addFilter('tag', tagIds);
					assetManager.getAssets();
				}
			});
	},

	bindMenuButtons : function() {
		var assetManager = this;

		this.menu
			.on('click', '#b-button-multiaction-delete', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.delete()
					.done(function() {
						assetManager.getAssets();
						assetManager.clearSelection();
				});
			})
			.on('click', '#b-button-multiaction-edit', function() {
				assetManager.viewAsset(assetManager.selected.join('-'));
				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-download', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.download();
			})
			.on('click', '#b-button-multiaction-clear', function() {
				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-tag', function() {
				var asset = new boomAsset(assetManager.selected.join('-')),
					dialog;

				dialog = new boomDialog({
					url: '/cms/tags/asset/list/' + asset.id,
					title: 'Asset tags',
					width: 440,
					cancelButton : false,
					onLoad: function() {
						$('#b-tags').tagger({
							type: 'asset',
							id: asset.id
						});
					}
				});
			})
			.on('click', '#b-assets-upload', function() {
				assetManager.uploader.show();
			});
	},

	clearSelection : function() {
		this.selected = [];
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

		$.post(this.listUrl, this.filters)
			.done(function(response) {
				var $response = $(response);

				assetManager.element
					.find('#b-assets-view-thumbs')
					.replaceWith($response.find('#b-assets-view-thumbs'));

				assetManager.element.find('#b-assets-view-thumbs').justifyAssets();

				assetManager.element.find('.pagination').replaceWith($response[2]);
				assetManager.initPagination();
			});
	},

	getPage : function(page) {
		if (this.filters.page !== page) {
			this.addFilter('page', page);
			this.getAssets();
		}
	},

	initPagination : function() {
		var assetManager = this;

		assetManager.element.find('.pagination')
			.jqPagination({
				paged: function(page) {
					assetManager.getPage(page);
				}
			});
	},

	removeFilters : function() {
		this.filters = {
			page : 1
		};

		this.element.find('#b-assets-types').val(0);

		var $title = this.element.find('#b-assets-filter-title');
		$title.val($title.attr('placeholder'));

		this.getAssets();
	},

	select : function(asset_id) {
		var index = this.selected.indexOf(asset_id);

		if (index == -1) {
			this.selected.push(asset_id);
		} else {
			this.selected.splice(index, 1);
		}

		this.toggleButtons();
	},

	sortBy : function(sort) {
		this.sortby = sort;
		this.getAssets();
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').button(this.selected.length == 1 ? 'enable' : 'disable');
		buttons.button(this.selected.length > 0 ? 'enable' : 'disable');
	},

	viewAsset : function(assetId) {
		var asset = new boomAsset(assetId),
			assetManager = this,
			dialog;

		dialog = new boomDialog({
			title : 'Edit Asset',
			url : this.baseUrl + 'view/' + assetId,
			width: document.documentElement.clientWidth >= 1000? '1000px' : '100%',
		})
		.done(function() {
			asset
				.save(dialog.contents.find('form').serialize())
				.done(function() {
					new boomNotification("Asset details saved");
				});
		});

		dialog.contents
			.on('click', '.b-assets-delete', function() {
				asset
					.delete()
					.done(function() {
						dialog.close();
						assetManager.getAssets();
					});
			})
			.on('click', '.b-assets-download', function(e) {
				e.preventDefault();
				asset.download();
			})
			.on('focus', '#thumbnail', function() {
				var $this = $(this),
					picker;
				picker = new boomAssetPicker($this.val())
					.done(function(assetId) {
						$this.val(assetId);
					});
			})
			.find('#b-tags')
			.tagger({
				type: 'asset',
				id: asset.id
			});
	}
});
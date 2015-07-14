$.widget('boom.assetManager', {
	baseUrl : '/cms/assets/',
	listUrl : '/cms/assets/get',
	postData : {
		page : 1
	},

	selected : [],

	addFilter : function(type, value) {
		this.postData.page = 1;
		this.postData[type] = value;
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
			})
			.on('click', '#b-assets-upload-close', function(e) {
				e.preventDefault();
				assetManager.uploader.hide();
			});
	},

	bindContentArea : function() {
		var assetManager = this;

		this.element
			.on('change', '#b-assets-sortby', function(event) {
				assetManager.sortBy(this.value);
			})
			.on('change', '#b-assets-types', function(event) {
				assetManager.addFilter('type', this.selectedIndex? this.options[this.selectedIndex].innerHTML : '');
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
				assetManager.tagMultiple(assetManager.selected);
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

		return $.post(this.listUrl, this.postData)
			.done(function(response) {
				var $response = $(response);

				assetManager.element
					.find('#b-assets-content')
					.html($($response[0]).html());

				assetManager.element
					.find('#b-assets-view-thumbs')
					.justifyAssets();

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
		this.postData['sortby'] = sort;
		this.getAssets();
	},

	tagMultiple : function(assetIds) {
		var asset = new boomAsset(assetIds.join('-')),
			dialog;

		dialog = new boomDialog({
			url: '/cms/assets/tags/list/' + asset.id,
			title: 'Asset tags',
			width: 440,
			cancelButton : false,
			onLoad: function() {
				dialog.contents.find('#b-tags').assetTagSearch({
					addTag : function(e, tag) {
						asset.addTag(tag);
					},
					removeTag : function(e, tag) {
						asset.removeTag(tag);
					}
				});
			}
		});
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').prop('disabled', this.selected.length == 1 ? false : true);
		buttons.prop('disabled', this.selected.length > 0 ? false : true);
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

	viewAsset : function(assetId) {
		var asset = new boomAsset(assetId),
			assetManager = this,
			dialog;

		dialog = new boomDialog({
			title : 'Edit Asset',
			url : this.baseUrl + 'view/' + assetId,
			width: document.documentElement.clientWidth >= 1000? '1000px' : '100%',
			closeButton: false,
			saveButton: true,
			onLoad : function() {
				dialog.contents
					.find('#b-tags')
					.assetTagSearch({
						addTag : function(e, tag) {
							asset.addTag(tag);
						},
						removeTag : function(e, tag) {
							asset.removeTag(tag);
						}
					});
			}
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
			});
	}
});
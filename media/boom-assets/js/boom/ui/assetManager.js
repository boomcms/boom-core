$.widget('boom.assetManager', {
	selected : [],

	bind : function() {
		this.bindContentArea();
		this.bindMenuButtons();
	},

	bindContentArea : function() {
		var assetManager = this;

		this.element
			.delegate('#b-assets-pagination a', 'click', function(e) {
				e.preventDefault();

				$.get('/cms/assets/list?' + $(this).attr('href').split('?')[ 1 ])
					.done(function(data) {
						assetManager.showContent(data);
					});

				return false;
			})
			.delegate('.b-assets-back', 'click', function(event) {
				event.preventDefault();
				$.boom.history.load('tag/' + $.boom.filter_assets.rid);
			})
			.on('change', '#b-assets-sortby', function(event) {
				self.tag.options.sortby = this.value;
				$.boom.history.refresh();
			})
			.on('change', '#b-assets-types', function(event) {
				if (this.selectedIndex) {
					self.filterByType(this.options[this.selectedIndex].innerHTML);
				} else {
					self.filterByType();
				}

			})
			.on('click', '#b-assets-all', function(event) {
				self.removeFilters();
			})
			.on('click', '.thumb a', function(event) {
				event.preventDefault();

				var $this = $(this);

				assetManager.select($this.attr('href').replace('#asset/', ''));
				$this.parent().parent().toggleClass('selected');
			});
	},

	bindMenuButtons : function() {
		var assetManager = this;

		this.menu
			.on('click', '#b-button-multiaction-delete', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.delete()
					.done(function() {
						assetManager.listAssets();
						assetManager.clearSelection();
				});
			})
			.on('click', '#b-button-multiaction-edit', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.get()
					.done(function(response) {
						assetManager.showContent(response);

						$('#b-assets-content').asset({
							asset_id : asset.id
						});
					});

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
					onLoad: function() {
						$('#b-tags').tagger({
							type: 'asset',
							id: asset.id
						});
					},
					buttons: [boomDialog.closeButton]
				});
			});
	},

	clearSelection : function() {
		this.selected = [];
		this.toggleButtons();

		this.element.find('#b-assets-view-thumbs div').removeClass('selected');
	},

	_create : function() {
		this.menu = this.element.find('#b-topbar');
		this.bind();

		this.listAssets();
	},

	filterByType : function(type) {
		if (type) {
			this.tag.set_filters([{type : 'type', id: type}]);
		} else {
			this.tag.set_filters([]);
		}

		$.boom.history.refresh();
	},

	filterByTitle : function(title) {
		this.url_map.tag.filters['title'] = title;
		$.boom.history.load('tag/0');
	},

	listAssets : function() {
		var assetManager = this;

		$.get('/cms/assets/list')
			.done(function(response) {
				assetManager.showContent(response);
			});
	},

	removeFilters : function() {
		this.element.find('#b-assets-types').val(0);

		var $title = this.element.find('#b-assets-filter-title');
		$title.val($title.attr('placeholder'));

		this.removeTagFilters();

		$.boom.history.load(this.options.defaultRoute);
	},

	removeTagFilters : function() {
		this.tag.filters = {};

		$('#b-tags-search')
			.find('.b-filter-input')
			.each(function() {
				var $this = $(this);
				$this.val($this.attr('placeholder'));
			})
			.end()
			.find('.b-tags-list li')
			.remove();
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

	showContent : function(content) {
		this.selected = [];
		this.toggleButtons();
		var $content = $(content);

		var id = $($content.get(0)).attr('id');
		var pagination = $content.get(2);
		var stats = $content.get(4);

		if (id == 'b-assets-content') {
			$('#b-assets-content')
				.replaceWith($content.get(0))
				.ui();
		} else {
			$('#b-assets-content')
				.html($content.get(0))
				.ui();
		}
		$('#b-assets-view-thumbs').justifyAssets();


		if (pagination) {
			$('#b-assets-pagination').replaceWith(pagination);
			$('#b-assets-filters').show();
			$('#b-assets-buttons').show();
		} else {
			$('#b-assets-pagination').contents().remove();
			$('#b-assets-filters').hide();
			$('#b-assets-buttons').hide();
		}

		if (stats) {
			$('#b-assets-stats').replaceWith(stats);
		} else {
			$('#b-assets-stats').contents().remove();
		}
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').button(this.selected.length == 1 ? 'enable' : 'disable');
		buttons.button(this.selected.length > 0 ? 'enable' : 'disable');
	}
});
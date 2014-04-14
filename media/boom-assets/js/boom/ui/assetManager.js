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

				self.select($this.attr('href').replace('#asset/', ''));
				$this.parent().parent().toggleClass('selected');
			});
	},

	bindMenuButtons : function() {
		var assetManager = this,
			selectedAssets = this.selected,
			asset = new boomAsset(selectedAssets.join('-'));

		this.menu
			.on('click', '#b-button-multiaction-delete', function() {
				asset.delete()
					.done(function() {
						$.boom.history.refresh();
						assetManager.clearSelection();
				});
			})
			.on('click', '#b-button-multiaction-edit', function() {
				$.boom.history.load('asset/' + asset.id)
					.done(function() {
						$('#b-assets-content').asset({
							asset_id : asset.id
						});
					});

				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-download', function() {
				asset.download();
			})
			.on('click', '#b-button-multiaction-clear', function() {
				assetManager.clearSelection();

				$('#b-assets-view-thumbs div').removeClass('selected');
			})
			.on('click', '#b-button-multiaction-tag', function() {
				var dialog = new boomDialog({
					url: '/cms/tags/asset/list/' + asset.id,
					title: 'Asset tags',
					width: 440,
					onLoad: function(){
						$('#b-tags').tagger({
							type: 'asset',
							id: asset.id
						});
					},
					buttons: [
						{
							text: 'Close',
							class : 'b-button',
							icons: {primary : 'b-button-icon b-button-icon-accept'},
							click: function(event) {
								dialog.close();
							}
						}
					]
				});
			});
	},

	clearSelection : function() {
		this.selected = [];
		this.toggleButtons();
	},

	_create : function() {
		this.menu = this.element.find('#b-topbar');
		this.bind();
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
});
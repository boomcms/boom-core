$.widget('boom.assetManager', {
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

	bind : function() {
		this.bindContentArea();
		this.bindMenuButtons();
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

			})
			.on('click', '#b-assets-all', function(event) {
				//assetManager.removeFilters();
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
				select : function(event, ui) {
					assetManager.addFilter('title', ui.item.value);
					$(".ui-menu-item").hide();
				}
			});


		var selected_tag_ids = [];
		this.element.find('#b-tags-search')
			.tagger_search()
			.find('input')
			.tagAutocompleter({
				type : 1,
				complete : function(event, data) {
					selected_tag_ids.push(data.id);
					$(this).tagAutocompleter('setSelectedTags', selected_tag_ids);
					$('#b-tags-search').tagger_search('add', data.name, data.id);
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
				var asset = new boomAsset(assetManager.selected.join('-'));

//				$.boom.history.load('asset/' + assetManager.selected.join('-'))
//					.done(function(response) {
//						assetManager.showContent(response);
//
//						$('#b-assets-content').asset({
//							asset_id : asset.id
//						});
//					});

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
				assetManager.openUploader();
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

		this.getAssets();
	},

	getAssets : function() {
		var assetManager = this;

		$.post(this.listUrl, this.filters)
			.done(function(response) {
				var $response = $(response);

				assetManager.element
					.find('#b-assets-view-thumbs')
					.replaceWith($response.find('#b-assets-view-thumbs'))
					.justifyAssets();

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

	openUploader : function() {
		var assetManager = this,
			uploaded = new $.Deferred();

		$.get(this.baseUrl + 'upload')
			.done(function(response) {
				assetManager.showContent(response);

				var tags = [],
					tagged = new $.Deferred();

				/* bit of a hack to get current tags */
				$( '#b-tags-search .b-tags-list li').each( function(){
					$this = $( this );
					tags.push( {
						label: $this.find( 'span' ).text(),
						value: $this.find( 'a' ).attr( 'data-tag_id')
					} );
				});

				assetManager.element.find('#b-assets-content').assetUploader({
					dropAreaHeight : $(window).height() - $('#b-topbar').height() - 30 + 'px',
					start: function(e) {
						var dialog = new boomDialog({
							url: '/cms/tags/asset/list/0',
							title: 'Asset tags',
							width: 440,
							cancelButton : false,
							onLoad: function(){
								// Make the tag editor work.
								$( '#b-tags' ).tagger_deferred( { tags : tags } );
							}
						})
						.done(function() {
							tagged.resolve(tags);
						});
					},
					done : function(e, data) {
						assetManager.getAssets();
						uploaded.resolve(data);
					}
				});

				$.when(tagged, uploaded).done(function(tags, data) {
					var promises = [],
						i;

					for (i in tags) {
						var request = $.post('/cms/tags/asset/add/' + data.result.join('-'), {
							tag : tags[i]
						});

						promises.push(request);
					}

					$.when(promises)
						.pipe(function() {
							return $('#b-tags-search').tagger_search('do_search');
						})
						.done(function() {
							for (i in data.result){
								$('.thumb[data-asset=' + i + '] a').click();
							}
						});
				});
			});

		return uploaded;
	},

	removeFilters : function() {
		this.element.find('#b-assets-types').val(0);

		var $title = this.element.find('#b-assets-filter-title');
		$title.val($title.attr('placeholder'));

		this.removeTagFilters();
		this.getAssets();
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
		var $content = $(content),
			assetManager = this;
console.log(content);
		$("#b-assets-view-thumbs").replaceWith($content.find('#b-assets-view-thumbs'));
		$('#b-assets-view-thumbs').justifyAssets();
		$('#b-assets-pagination')
			.replaceWith($content[2])
			.jqPagination({
				paged: function(page) {
					$.get(assetManager.baseUrl + 'list?')
						.done(function(data) {
							assetManager.showContent(data);
						});
				}
			});
	},

	sortBy : function(sort) {
		this.sortby = sort;
		this.getAssets();
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').button(this.selected.length == 1 ? 'enable' : 'disable');
		buttons.button(this.selected.length > 0 ? 'enable' : 'disable');
	}
});
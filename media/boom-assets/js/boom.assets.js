/**
@fileOverview Asset manager UI, including asset tags and asset view.
*/
/**
@class
@extends $.boom.item
*/
$.boom.asset = {};

/**
@namespace
*/
$.boom.assets = {};


$.extend($.boom.assets,
	/** @lends $.boom.assets */
	{

	/**
	Open an asset manager in a dialog box.
	@function
	@returns {Promise} promise which resolves with an asset RID.
	@example
	var asset_id = selected_asset.id;
	$.boom.assets
		.picker({
			id : asset_id
		})
		.done( function( asset_id ){
			selected_asset.id = asset_id;
		});
	*/
	picker : function( opts ){
		$.boom.log( 'opening an asset manager' );

		var self = this;
		var complete = new $.Deferred();
		var browser;
		var dialog;

		var cleanup = function(){
			top.location.hash = '';
		};

		var default_options =
			/** @ignore */ {
			url: '/cms/assets/manager',
			iframe: false,
			width: '80%',
			height: '700',
			dialogClass : 'b-assets-dialog',
			title: 'Select an asset',
			cache: false,
			callback: function() {
				var asset_id = browser.browser_asset( 'get_asset' );
				cleanup();
				complete.resolve( asset_id );
				return false;
			},
			open: function(){
				$.boom.log( 'dialog open' );
				var dialog = $(this);

				var remove = $('<button />')
					.addClass('ui-helper-left')
					.text('Remove')
					.button({
						text: false,
						icons: { primary : 'ui-icon-boom-delete' }
					})
					.click(function(){
						complete.reject();
						cleanup();
						dialog.dialog('destroy');
					});
				var upload = $('<button />')
					.addClass('ui-helper-left')
					.text( 'Upload' )
					.button({
						text: false,
						icons: { primary : 'ui-icon-boom-upload' }
					})
					.click( function() {
						browser.browser_asset( 'upload' )
							.done( function(){
								$.boom.history.load( 'tag/0' );
							});
					});

				if ( opts.asset_rid && opts.asset_rid > 0 ) {
					remove.button( 'enable');
				} else {
					remove.button( 'disable' );
				}

				$(this).dialog('widget')
					.find('.ui-dialog-buttonpane')
					.prepend( upload )
					.prepend( remove )
					.append($('<div class="center"><div id="b-assets-pagination"></div><div id="b-assets-stats"></div></div>'));
			},
			onLoad: function(){

				browser = $( '#b-assets-manager' ).browser_asset();

				$.when( browser.browser_asset( 'browse' ) )
				.progress( function( rid ){
					browser.browser_asset( 'edit', rid );
				});

				// browser widget pushes a default URL to the history stack.
				// need to override that if an asset is already selected
				// by setting a fragment identifier on the parent window.
				if ( opts.asset_rid && opts.asset_rid > 0 ) {
					$.boom.log( 'getting asset ' + opts.asset_rid );
					browser.browser_asset( 'edit', opts.asset_rid );
				}
			}
		};

		opts = $.extend( default_options, opts );

		dialog = $.boom.dialog.open( opts );

		return complete;
	}
});

$.extend($.boom.asset, $.boom.item,
	/** @lends $.boom.asset */
	{

	base_url: '/cms/assets/',

	type: 'asset',

	/** @function */
	bind : function( context ){
		var self = this;
		var rids = $.boom.history.getHash().split('/')[1].split('-');

		// Make the tag editor work.
		$('#b-tags', context ).tagger({
			type: 'asset',
			id: this.rid
		});

		$('.boom-tabs', context ).tabs('option', 'active', 1);

		$.boom.dialog.bind({
			image: $('.boom-asset-preview',context)
		});

		$('#boom-button-asset-link-add', context )
			.on( 'click', function(event){
				event.preventDefault();

				var page_rid = $( '#link_rid' ).val();

				$.boom.links
					.picker( {
						page_rid: page_rid
					})
					.done( function( link ){
						$( '#link' ).val( link.url );
						$( '#link_rid' ).val( link.rid );
						$( '#link_url' ).val( link.url );
					});
			});

		$('#boom-button-asset-tags-delete', context ).click(function(){
			var tags = [];

			$( this )
				.parent()
				.find( '.boom-tree .ui-state-active' )
				.each( function(i){

					var tag =
						$( this )
							.attr('href')
							.split( '/' );
					tags.push( tag[1] );
				});

			$.boom.loader.show();

			$.post('/cms/assets/remove_tags/' + $('#asset_id').val(), {tags:  tags})
			.done( function(){

				$.boom.loader.hide();
				$.boom.history.refresh();
			});

			return false;
		});

		$('#b-assets-content')
			.on('click', '.b-assets-save', function(event) {
				var data = $(this).closest( 'form' ).serialize();

				self
					.save(data)
					.done( function(){
						$.boom.growl.show( "Asset saved." );
					});

			})
			.on('click', '.b-assets-download', function(event) {
				var rid = $( this ).attr( 'rel' );
				window.location = '/cms/assets/download?assets=' + rid;
			})
			.on('click', '.b-assets-delete', function(event) {
				self.remove()
					.done( function(){
						$.boom.history.load( 'tag/0' );
					});
			})
			.on('click', '.b-assets-back', function(event) {
				event.preventDefault();
				$.boom.history.load( 'tag/' + $.boom.filter_assets.rid );
			});
	}
});

/**
Filter lists of assets by tag.
@class
@extends $.boom.filter
*/
$.boom.filter_assets = $.extend( {}, $.boom.filter, {

	base_url: '/cms/assets/',

	type : 'tag'
});

/**
* User interface for browsing and managing assets.
* @class
* @name $.boom.browser_asset
* @extends $.boom.browser
*/
$.widget( 'boom.browser_asset', $.boom.browser,
	/** @lends $.boom.browser_asset */
	{

	/**
	default config
	@property options
	@default $.boom.config.browser_asset
	@see $.boom.config.browser_asset
	*/
	options: $.boom.config.browser_asset,

	_create : function(){
		var self = this;

		$.boom.log( 'asset browser init' );

		self.item = $.boom.asset;
		self.tag = $.boom.filter_assets;

		$.boom.browser.prototype._create.call( this );
	},

	_bind: function(){

		$.boom.browser.prototype._bind.call( this );

		var self = this;

		$('#boom-assets-upload-menu')
		.on( 'click', function( event ) {
			var tags = [];
			var tagged = new $.Deferred();

			/* bit of a hack to get current tags */
			$( '#b-tags-search .b-tags-list li').each( function(){
				$this = $( this );
				tags.push( {
					label: $this.find( 'span' ).text(),
					value: $this.find( 'a' ).attr( 'data-tag_id')
				} );
			});

			var uploaded = self
				.upload({
					add: function( e, data ){

						$.boom.dialog.open({
							url: '/cms/tags/asset/list/0',
							// cache: true,
							title: 'Asset tags',
							width: 440,
							callback: function(){
								tagged.resolve( tags );
							},
							onLoad: function(){
								// Make the tag editor work.
								$( '#b-tags' ).tagger_deferred( { tags : tags } );
							}
						});

						data.submit();
					}
				})
				.done( function( data ){
					$.boom.history.load( self.options.defaultRoute )
					.done( function(){
						$.boom.log( 'asset list updated' );
						for ( i in data.result ){
							$( '#asset-list-' + data.result[ i ] ).click();
						}
					});

				});

				$.when( tagged, uploaded ).done( function( tags, data ){

					var promises = [];

					for ( i in tags ) {
						var request = $.post(
							'/cms/tags/asset/add/' + data.result.join( '-' ),
							{
								tag : tags[i].label
							}
						);

						promises.push( request );
					}

					$.when( promises )
						.pipe( function(){
							return $( '#b-tags-search' )
							.tagger_search( 'do_search' );
						})
						.done( function(){
							for ( i in data.result ){
								$( '#asset-list-' + data.result[ i ] ).click();
							}
						});

				});
		});

		var title_filter = $('#b-assets-filter-title')
			.autocomplete({
				delay: 200, // Time to wait after keypress before making the AJAX call.
				minLength : 0,
				source: function(request, response){
					$.ajax({
						url: '/cms/autocomplete/assets',
						dataType: 'json',
						data: {
							text : title_filter.val()
						}
					})
					.done(function(data) {
						response(data);

						self.filterByTitle(title_filter.val());
					});
				},
				select: function(event, ui){
					self.filterByTitle(ui.item.value);
					$(".ui-menu-item").hide();
				}
			});

		$( '#b-tags-search' ).tagger_search();

		$( '#b-topbar' )
			.on( 'click', '#b-button-multiaction-delete', function(){

				$.boom.log( 'asset delete selected' );

				var msg = 'Are you sure you want to delete the selected assets?';

				$.boom.dialog.confirm(
					'Confirm deletion',
					msg
				)
				.done( function(){
					$.boom.loader.show();

					$.post('/cms/assets/delete', {csrf: $.boom.options.csrf, assets:  this.selected}, function(){
						$.boom.loader.hide();

						$.boom.history.refresh();
					});
				});
			})
			.on( 'click', '#b-button-multiaction-edit', function(){
				$.boom.history.load('asset/' + self.selected.join('-'));
			})
			.on( 'click', '#b-button-multiaction-download', function(){
				var url = '/cms/assets/download?assets=' + self.selected.join(',');

				window.location = url;
			})
			.on('click', '#b-button-multiaction-clear', function(){
				self.selected = [];
				self.toggleButtons();
			})
			.on( 'click', '#b-button-multiaction-tag', function(){
				$.boom.dialog.open({
					url: '/cms/tags/asset/list/' + self.selected.join( '-' ),
					// cache: true,
					title: 'Asset tags',
					width: 440,
					onLoad: function(){
						$('#b-tags').tagger({
							type: 'asset',
							id: self.selected.join( '-' )
						});
					}
				});
			});

		$('body').delegate('#b-assets-pagination a', 'click', function( e ){
			e.preventDefault();

			$.get( '/cms/assets/list?' + $( this ).attr( 'href' ).split( '?' )[ 1 ])
				.done( function( data ){
					self.showContent(data);
				});

			return false;
		});

		$('#b-assets-manager')
			.on( 'change', '#b-assets-sortby', function( event ){
				self.tag.options.sortby = this.value;
				$.boom.history.refresh();
			})
			.on('change', '#b-assets-types', function(event) {
				self.filterByType(this.options[this.selectedIndex].innerHTML);
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

		$('#b-assets-content')
			.on( 'click', '.boom-tagmanager-asset-replace ', function( event ){

				var rid = $( this ).attr( 'rel' );

				self.
					upload({
						url: '/cms/assets/upload',
						formData : [ { csrf: $.boom.options.csrf,  name: 'asset_id', value: rid } ]
					})
					.done( function( data ){
						$.boom.history.refresh();
					});
			})
			.on('change', '#b-assets-toggleall', function() {
				var checked = $(this).is(':checked');

				$('.b-items-select-checkbox').change();
			});

		$.when( self.browse() )
			.progress( function( rid ){
				$( '#asset-list-' + rid ).click();
			});
	},

	/**
	Set up an asset browser
	@returns {Object} promise which updates via .notify( rid ) when an asset is selected.
	*/
	browse: function(){
		var self = this;
		var select_asset = new $.Deferred();

		$('#b-assets-manager')
			.on( 'click', ' .thumb a', function(event){
				var data = $(this).attr('href').split('/');
				var rid = parseInt( data[1], 10 );
				select_asset.notify( rid );

				return false;
			});

		return select_asset;

	},

	/**
	Open the asset editing view
	@param {Integer} rid RID of the currently selected asset.
	@returns {Object} promise resolved when the text is set.
	*/
	edit: function( rid ){
		$.boom.history.load('asset/' + rid);
		this.selected_rid = rid;
	},

	/**
	Get the currently selected asset ID
	@returns {Integer} asset ID
	*/
	get_asset : function() {
		return this.selected_rid;
	},

	/**
	Upload a new asset file.
	@function
	*/
	upload: function( opts ){

		var self = this;
		var uploaded = new $.Deferred();
		var file_data = {};

		var default_opts = $.extend( $.boom.config.upload, {
			submit: function( e, data ){
				$( '#b-upload-progress' ).progressbar();

				file_data = data;
			},
			progressall: function( e, data ){
				var percent = parseInt( (data.loaded / data.total * 100), 10);

				$( '#b-upload-progress' ).progressbar( 'value', percent );
			},
			done: function( e, data ){
				$.boom.log( 'file upload complete' );
				$.boom.assets.selected_rid = data.result.join( '-' );

				uploaded.resolve( data );

			},
		   fail: function(e, data) {
				message = "There was an error uploading your file";

			   if (data.jqXHR.responseText) {
				   message = message + ': ' + data.jqXHR.responseText;
			   }

			   message = message + '.';

				$( '#upload-advanced span.message' ).text( message );
			},
			always: function( e, data ){
				$.boom.log( 'file upload finished' );
			}
		});

		opts = $.extend( default_opts, opts );

		$.get('/cms/assets/upload')
			.done(function(response) {
				self.showContent(response);

				opts.formData.push( { name: 'csrf', value: $('input[name=csrf]').val() } );

				$( '#b-assets-upload-form' )
				.fileupload( opts );

				 $( '#b-assets-upload-file' )
					.detach()
					.appendTo( '#b-upload-add' )
					.css({
						transform: 'translate(-300px, 0) scale(4)'
					});

				$( '#b-assets-upload-cancel' )
					.on( 'click', function(){
						file_data.jqXHR && file_data.jqXHR.abort();

						$.boom.history.load( 'tag/' + $.boom.assets.tag.rid );
						self.main_content.trigger('justify');
					});
			});

		return uploaded;
	},

	showContent : function(content) {
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

	removeFilters : function() {
		$('#b-assets-types').val('');
		$('#b-assets-filter-title').val('');

		this.removeTagFilters();

		$.boom.history.load(this.options.defaultRoute );
	},

	removeTagFilters : function() {
		this.tag.filters = {};

		$('#b-tags-search')
			.find('.b-filter-input')
			.val('')
			.end()
			.find('.b-tags-list li')
			.remove();
	},

	filterByType : function(type) {
		this.tag.set_filters([{type : 'type', id: type}]);
		$.boom.history.refresh();
	},

	filterByTitle : function(title) {
		this.url_map.tag.filters[ 'title' ] = title;
		$.boom.history.load( 'tag/0' );
	}
});
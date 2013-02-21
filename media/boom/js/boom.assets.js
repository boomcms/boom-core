/**
* User interface for browsing and managing assets.
* @class
* @name boom.browser
*/
$.widget( 'boom.asset_browser', $.boom.browser, {
	
	options: {
		sortby: 'last_modified',
		order: 'desc',
		basetagRid: 1,
		defaultTagRid: 0,
		edition: 'cms',
		type: 'assets',
		treeConfig : {
			showEdit: true,
			showRemove: true,
			onEditClick: function(event){

				$.boom.items.group.edit(event);
			},
			onRemoveClick: function(event){

				$.boom.items.group.remove(event);
			}
		}
	},
	
	_create : function(){
		
		var self = this;
		
		self.items = {
			asset: $.boom.asset,
			tag: $.boom.assets.tag
		};
		
		$.boom.browser.prototype._create.call( this );

		$('#boom-assets-upload-menu')
		.on( 'click', function( event ) {
			var tags = [];
			var tagged = new $.Deferred();
			
			var uploaded = self
				.upload({
					add: function( e, data ){
					
						$.boom.dialog.open({
							url: '/cms/tags/asset/list/0',
							// cache: true,
							title: 'Asset tags',
							width: 440,
							buttons: {
								Done: function(){
									$.boom.dialog.destroy( this );
									tagged.resolve( tags );
								}
							},
							onLoad: function(){
								// Make the tag editor work.
								$( '#b-tags' ).deferred_tagger( { tags : tags } );
							}
						});
						
						data.submit();
					}
				})
				.done( function( data ){
					self.items.tag.get( 0 )
					.done( function(){
						$.boom.log( 'asset list updated' );
						for ( i in data.result.rids ){
							$( '#asset-list-' + data.result.rids[ i ] ).click();
						}
					});
					
				});
				
				$.when( tagged, uploaded ).done( function( tags, data ){
					
					for ( i in tags ) {
						$.post(
							'/cms/tags/asset/add/' + data.result.rids.join( '-' ),
							{
								tag : tags[i]
							}
						);
					}
				});
		});

		var title_filter = $('#b-assets-filter-title')
		.autocomplete({
			delay: 200, // Time to wait after keypress before making the AJAX call.
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
					var title = title_filter.val();
					if ( title != '' ) self.items.tag.filters[ 'title' ] = title;
					self.items.tag.get( 0 );
				});
			},
			select: function(event, ui){
				self.items.tag.filters[ 'title' ] = ui.item.value;
				self.items.tag.get( 0 );
			}
		});
		
		var selected_tag_ids = [];
		
		$( '#b-tags-search' ).tag_search( { tagmanager : self } );

		$( '#boom-topbar' )
			.on( 'click', '#b-button-multiaction-delete', function(){

				$.boom.log( 'asset delete selected' );

				var msg = 'Are you sure you want to send the selected assets to the rubbish bin?';

				$.boom.dialog.confirm(
					'Confirm deletion',
					msg,
					function(){

						var assets = [];

						$('.b-items-select-checkbox:checked').each(function(i){

							assets.push( $( this ).attr('id').replace(/asset-(thumb|list)-/, '') );
						});

						$.boom.loader.show();

						$.post('/cms/assets/delete', {assets:  assets}, function(){

							$.boom.loader.hide();

							$.boom.history.refresh();
						});
					}
				);
			})
			.on( 'click', '#b-button-multiaction-edit', function(){

				var ids = [];

				$('.b-items-select-checkbox:checked').each(function(){
					var id = this.id.replace(/asset-(thumb|list)-/, '');

					if ( $.inArray(id, ids) === -1 ) {

						ids.push( id );
					}
				});

				$.boom.history.load('asset/' + ids.join('-'));
			})
			.on( 'click', '#b-button-multiaction-download', function(){

				var ids = [];
				$('.b-items-select-checkbox:checked').each(function(){

					ids.push(this.id.replace(/asset-(thumb|list)-/, ''));
				});

				var	url = '/cms/assets/download?assets=' + ids.join(',');

				window.location = url;
			})
			.on('click', '#b-button-multiaction-clear', function(){
				$('.b-items-select-checkbox:checked').each(function(){
					$('.thumb.ui-state-active').removeClass('ui-state-active');
					$(this).removeAttr( 'checked' ).prop( 'checked', false ).change();
				});

			})
			.on( 'click', '#b-button-multiaction-tag', function(){

				var ids = [];
				$('.b-items-select-checkbox:checked').each(function(){

					ids.push(this.id.replace(/asset-(thumb|list)-/, ''));
				});

				$.boom.dialog.open({
					url: '/cms/tags/asset/list/' + ids.join( '-' ),
					// cache: true,
					title: 'Asset tags',
					width: 440,
					buttons: {
						Close: function(){
							$.boom.dialog.destroy( this );
						}
					},
					onLoad: function(){
						$('#b-tags').tagger({
							type: 'asset',
							id: ids.join( '-' )
						});
					}
				});

			});
	
		self.main_panel
			.on( 'change', '.b-items-select-checkbox', function( event ){
				// checkbox IDs are of the form type-view-id.
				var item = this.id.split( '-' );
				var view = item[ 1 ];
				var type = item[ 0 ];
				var item_id = item[ 2 ];
				
				self.items.asset.select( item_id, $( this ).is(':checked') );

				

				var amount = $('.b-items-select-checkbox:checked').length;

				var buttons = $( '[id|=b-button-multiaction]' ).not( '#b-button-multiaction-edit' );

				$( '#b-button-multiaction-edit' ).button( (  amount && amount < 3) ? 'enable' : 'disable' );

				buttons.button( amount > 0 ? 'enable' : 'disable' );
			})
			.on( 'click', '#b-items-view-thumbs a', function(event){
				event.preventDefault();

				var asset_id = $( this ).attr( 'href' ).split( '/' )[ 1 ];
				$( '#asset-list-' + asset_id ).click();

			})
			.on( 'mouseenter focus', '#b-items-view-list tbody tr, #b-items-view-thumbs a', function( event ){
				$( this ).addClass( 'ui-state-hover' );
			})
			.on( 'mouseleave blur', '#b-items-view-list tbody tr, #b-items-view-thumbs a', function( event ){
				$( this ).removeClass( 'ui-state-hover' );
			});
		
	},

	/**
	Set up an asset browser
	@returns {Object} promise which updates via .notify( rid ) when an asset is selected.
	*/
	browse: function(){

		var self = this;
		var select_asset = new $.Deferred();

		$( self.main_panel )
			.on( 'click', '.thumb a', function(event){

				var data = $(this).attr('href').split('/');
				var rid = parseInt( data[1], 10 );
				select_asset.notify( rid );

				return false;
			})
			.on( 'click', '.boom-pagination a', function( e ){
				e.preventDefault();

				//$.boom.history.load( '/cms/assets/list?' + $( this ).attr( 'href' ).split( '?' )[ 1 ] );
				$.get( '/cms/assets/list?' + $( this ).attr( 'href' ).split( '?' )[ 1 ])
				.done( function( data ){
					var $data = $( data );
					var pagination = $data.find( '.boom-pagination' ).html();
					var list = $data.find( '#b-items-view-list' ).html();
					var thumbs = $data.find( '#b-items-view-thumbs' ).html();
					$( self.main_panel )
						.find( '.boom-pagination' )
						.html( pagination )
						.end()
						.find( '#b-items-view-list' )
						.html( list )
						.end()
						.find( '#b-items-view-thumbs' )
						.html( thumbs );
				});

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

		var self = this;

		$.boom.history.load( 'asset/' + rid );
		//top.location.hash = '#asset/' + rid;
		self.selected_rid = rid;

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
	*/
	upload: function( opts ){

		var self = this;
		var tagmanager = $.boom.assets;
		var uploaded = new $.Deferred();
		var file_data = {};
		
		var default_opts = {
			url: '/cms/uploadify/asset',
			dataType: 'json',
			singleFileUploads: false,
			formData: [],
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
				$.boom.dialog.destroy( upload_dialog );
				tagmanager.selected_rid = data.result.rids.join( '-' );
				
				uploaded.resolve( data );
				
			},
			fail: function( e, data ){
				$( '#upload-advanced span.message' ).text( "There was an error uploading your file." );
			},
			always: function( e, data ){
				$.boom.log( 'file upload finished' );
			}
		};
		
		opts = $.extend( default_opts, opts );

		var upload_dialog = $.boom.dialog.open({
			url:  '/cms/uploadify/form',
			width: 400,
			title: 'Upload file/s',
			onLoad: function(){
				
				var upload_token = $( '#upload_token' ).val();
				
				opts.formData.push( { name: 'upload_token', value: upload_token } );
				
				$( '#b-assets-upload-form' )
				.fileupload( opts );
				
				 $( '#b-assets-upload-file' )
					.detach()
					.appendTo( '#b-upload-add' )
					.css({
						position: 'absolute',
						transform: 'translate(-300px, 0) scale(4)'
					});
			},
			buttons: {
				Cancel: function(){

					// TODO: cancel uploadify uploads
					
					file_data.jqXHR && file_data.jqXHR.abort();

					$.boom.dialog.destroy( upload_dialog );
					
					$.boom.history.refresh();
				}
			}
		});
		
		return uploaded;
	}
	
});

/**
@class
@extends CMSTagmanager
*/
$.boom.assets = {};

$.extend($.boom.assets, {
	/** @lends $.boom.assets */

	/** @property */
	asset_browser: {},

	/**
	Open an asset manager in a dialog box.
	@function
	@returns {Promise} promise which resolves with an asset RID.
	*/
	picker : function( opts ){
		$.boom.log( 'opening an asset manager' );

		var self = this;
		var complete = new $.Deferred();
		var browser;

		var cleanup = function(){
			top.location.hash = '';
			$.boom.dialog.destroy( self.asset_browser );
		};

		var default_options = {
			url: '/cms/assets/manager/',
			iframe: false,
			width: 1000,
			height: 500,
			title: 'Select an asset',
			cache: false,
			buttons: {
				Cancel: function() {
					cleanup();
					( opts.deferred ) && opts.deferred.reject();
					return false;
				},
				'Okay': function() {
					var asset_id = browser.asset_browser( 'get_asset' );
					cleanup();
					( opts.deferred ) && opts.deferred.resolve();
					complete.resolve( asset_id );
					return false;
				}
			},
			open: function(){
				$.boom.log( 'dialog open' );
				var button = $('<button />')
				.addClass('ui-helper-left');

				if ( opts.asset_rid && opts.asset_rid > 0 ) {

					button
					.text('Remove')
					.button()
					.click(function(){
						complete.reject();
						cleanup();
					});

				} else {

					button
						.text( 'Upload' )
						.button()
						.click( function() {
							browser.asset_browser( 'uplaod' );
						});
				}
				$(this).dialog('widget')
					.find('.ui-dialog-buttonpane')
					.prepend( button );
			},
			onLoad: function(){
				
				browser = $( '#boom-tagmanager' ).asset_browser();

				$.when( browser.asset_browser( 'browse' ) )
				.progress( function( rid ){
					browser.asset_browser( 'edit', rid );
				});

				// tagmanager.init() pushes a default URL to the history stack.
				// need to override that if an asset is already selected
				// by setting a fragment identifier on the parent window.
				if ( opts.asset_rid && opts.asset_rid > 0 ) {
					$.boom.log( 'getting asset ' + opts.asset_rid );
					browser.asset_browser( 'edit', opts.asset_rid );
				}
				
				$( '#boom-tagmanager' ).asset_browser();
			}
		};

		opts = $.extend( default_options, opts );

		self.asset_browser = $.boom.dialog.open( opts );

		return complete;
	}
});

/**
@class
*/
$.boom.asset = {};

$.extend($.boom.asset, {
	/** @lends $.boom.asset */

	/** @property */
	buttonManager: {
		show: [ '#b-assets-upload, #boom-tagmanager-save-all' ]
	},

	/** @function */
	get : function(rid){
		$.boom.log( 'boom.items.asset.get ' + rid );

		var self = this;
		var options = self.tagmanager.options;

		this.rid = rid;

		$.boom.loader.show();

		$.boom.events.register('asset.clickBefore', 'tagmanager', { rid: rid });

		var segments = [
			options.edition,
			rid,
			'asset'
		].join('/'),
		url = '/cms/assets/view/' + this.rid;

		return self.tagmanager.main_panel
		.find('.b-items-content')
		.sload(url, function(){

			$( this ).scrollTop( 0 );

			$.boom.events.register('asset.clickAfter', 'tagmanager', { rid: rid });

			$.boom.loader.hide();

			self.bind( this );

			// Make the tag editor work.
			$('#b-tags').tagger({
				type: 'asset',
				id: rid
			});

			$(this).find('.boom-tabs').tabs('option', 'active', 1);
		});
	},
	
	/** @function */
	select : function( rid, selected ){

		var thumb = '#asset-thumb-' + rid;
		var list = '#asset-list-' + rid;

		var checkbox = $( thumb );
		checkbox.prop( 'checked', selected );

		if ( selected ) {

			checkbox.attr('checked', 'checked');

			checkbox.parents( 'div.thumb' ).addClass( 'ui-state-active' );
			$( list ).parents( 'tr' ).addClass( 'ui-state-active' );

		} else {

			checkbox.removeAttr('checked');

			checkbox.parents( 'div.thumb' ).removeClass( 'ui-state-active' );
			$( list ).parents( 'tr' ).removeClass( 'ui-state-active' );
		}
		
	},

	/** @function */
	bind : function(elem){
		var self = this;
		var options = this.tagmanager.options;
		var rids = $.boom.history.getHash().split('/')[1].split('-');

		if ( rids.length <= 1 ) {

			$( '#boom-tagmanager-save-all' ).hide();
		} else {

			$( '#boom-tagmanager-save-all' ).unbind( 'click' ).click(function(){

				$( '.boom-tagmanager-asset-save' ).trigger( 'save' );
			});
		}

		var tabsConfig = $.extend({}, $.boom.config.tabs, {

			show: function(event, ui){

				var label = $.trim( $(ui.tab).text() ).toLowerCase();
			}
		});

		$( elem ).ui({
			tabs: tabsConfig
		});

		$.boom.dialog.bind({
			image: $('.boom-asset-preview')
		});

		$('#boom-button-asset-link-add')
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

		$('#boom-button-asset-tags-delete').click(function(){
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

		$('.boom-tagmanager-asset-save')
			.bind('save', function( event ){

				var rid = $( this ).attr( 'rel' );
				var data = $( this ).closest( 'form' ).serialize();

				$.boom.loader.show();

				$.post('/cms/assets/save/' + rid, data)
				.done( function(){

					$.boom.loader.hide();
				});

			})
			.click(function(){

				$( this ).trigger( 'save' );
			});

		$('.boom-tagmanager-asset-download').click(function( event ){

			var rid = $( this ).attr( 'rel' );

			window.location = '/cms/assets/download?assets=' + rid;
		});

		$('.boom-tagmanager-asset-delete').click(function( event ){

			var rid = $( this ).attr( 'rel' );
			var delete_asset = new $.Deferred();

			delete_asset.done( function(){

				$.boom.loader.show();

				var items = $.boom.history.getHash().split('/')[1].split(',');

				$.post('/cms/assets/delete', { assets: rid })
				.done( function(){

					$.boom.loader.hide();

					if ( items.length > 1 ){

						var segments =
							$.boom.history.getHash().split('/')[0] +
							'/' +
							$.grep(items, function(val){

								return val != rid;

							}).join('-');

						$.boom.history.load( segments );

					} else {

						self.tagmanager.defaultRoute();
					}
				});
			});

			$.boom.dialog.open({
				width: 350,
				msg: 'Are you sure you want to delete this asset?',
				title: 'Please confirm',
				deferred: delete_asset
			});
		});

		$('.boom-tagmanager-asset-replace').click(function( event ){

			var rid = $( this ).attr( 'rel' );
			
			$.boom.assets
				._upload( { formData : [ { name: 'asset_id', value: rid } ] } )
				.done( function( data ){
					$.boom.history.refresh();
				});

		});

		$( '.boom-tagmanager-asset-back' ).on( 'click', function( event ){
			event.preventDefault();
			var tag = self.tagmanager.items.tag;
			tag.get( tag.rid );

		});
	}
});

/**
@class
*/
$.boom.assets.tag = {};

$.extend($.boom.assets.tag,  {
	/** @lends $.boom.assets.tag */

	srid: 0,

	filters: {},

	/** @function */
	get : function( rid ){

		$.boom.log( 'get tag ' + rid );

		var self = this;
		var options = this.tagmanager.options;

		this.rid = rid;

		$.boom.loader.show();

		params =
			'tag=' + rid + '&' +
			'perpage=' + options.perpage + '&' +
			'sortby=' + options.sortby;

		for ( filter in self.filters ) {
			params += '&' + filter + '=' + self.filters[ filter ];
		}

		var url =
			'/cms/' + options.type + '/list'
			+ '?' + params;

		options.url = url;

		return self.tagmanager.main_panel
			.find('.b-items-content')
			.sload( url, function(){

				$.boom.loader.hide();

				self.tagmanager.main_panel.ui();
				
				$.boom.events.register('tag.clickAfter', 'tagmanager');
				
				$('.b-items-thumbs .thumb').captions($.boom.config.captions);
				
				$.boom.log('Tag items get');
			});
	}
});

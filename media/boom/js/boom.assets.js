/**
@class
@extends CMSTagmanager
*/
$.boom.assets = new CMSTagmanager('assets');

$.extend($.boom.assets, {
	/** @lends $.boom.assets */

	/** @property */
	selected_rid : 0,

	/** @property */
	asset_browser: {},

	/** @function */
	init : function(config){

		//if ( !config || !config.items ) return;

		this._init( config );

		var self = this;

		var upload_menu = {
			'Upoad image' : function( event ) {
				var tags = [];
				var tagged = new $.Deferred();
				
				var uploaded = self
					._upload({
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
									var tag_list = $( '#b-tags .b-tags-list' );
									
									$.boom.tags.bind_tree( '#b-tags .b-tags-list' )
										.progress( function( $link ){

											var tag_id = $link.attr( 'data-tag_id' );

											$link
												.closest( 'li' )
												.remove();

												selected_tag_ids.splice( selected_tag_ids.indexOf( tag_id ), 1);
												self.items.tag.get( selected_tag_ids.join( '-' ) );

										});
										
									$.boom.tags.picker( $('#b-tags-add-name'), 'asset' )
										.progress( function ( tag ) {
											tags.push( tag.label );
											
											var link = $( '<a>', {
												href : '#',
												"class" : 'b-tags-remove',
												"data-tag_id" : tag.value 
											});
											var label = $( '<span>').text( tag.label );

											$( '<li>' )
												.append( link )
												.append( label )
												.appendTo( tag_list );
										});
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
								$( 'a[href="#asset/' + data.result.rids[ i ] + '"]' ).click();
							}
						});
						
					});
					
					$.when( tagged, uploaded ).done( function( tags, data ){
						for ( i in tags ) {
							$.post(
								$.boom.tags.base_url + 'asset/add/' + data.result.rids.join( '-' ),
								{
									tag : tags[i]
								}
							);
						}
					});
			},

			'Upload video' : function( event ) {
				$.boom.dialog.open({
					url: '/cms/video/upload',
					title: 'Upload video',
					buttons: {
						Cancel: function(){
							$.boom.dialog.destroy(this);
						},
						Upload: function(){
							$('#uploadForm').submit();
						}
					}
				});
			}
		};

		$('#boom-assets-upload-menu')
			.splitbutton({
				items: upload_menu,
				width: 'auto',
				menuPosition: 'right',
				split: false
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
		
		var tag_filter_list =
		$( '#b-assets-filter-tag + .b-tags-list' );
		
		$.boom.tags.bind_tree( '#b-tags-search' )
			.progress( function( $link ){
				
				var tag_id = $link.attr( 'data-tag_id' );

				$link
					.closest( 'li' )
					.remove();

					selected_tag_ids.splice( selected_tag_ids.indexOf( tag_id ), 1);
					self.items.tag.get( selected_tag_ids.join( '-' ) );
					
			});
		
		$.boom.tags.picker( $('#b-assets-filter-tag'), 'asset', selected_tag_ids )
			.progress( function ( tag ) {
				selected_tag_ids.push( tag.value );
				var link = $( '<a>', {
					href : '#',
					"class" : 'b-tags-remove',
					"data-tag_id" : tag.value 
				});
				var label = $( '<span>').text( tag.label );
				
				$( '<li>' )
					.append( link )
					.append( label )
					.appendTo( tag_filter_list );
					
				self.items.tag.get( selected_tag_ids.join( '-' ) );
			});

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
					$('.thumb').removeClass('ui-state-active');
					$(this).removeAttr('checked');
					$(this).change();
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
						// Make the tag editor work.
						$.boom.tags.init({
							type: 'asset',
							id: ids.join( '-' )
						});
					}
				});

			});
	},

	/**
	Open an asset manager in a dialog box.
	@function
	@returns {Promise} promise which resolves with an asset RID.
	*/
	picker : function( opts ){
		$.boom.log( 'opening an asset manager' );

		var self = this;
		var complete = new $.Deferred();

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
					cleanup();
					( opts.deferred ) && opts.deferred.resolve();
					complete.resolve( self.selected_rid );
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
							self._upload();
						});
				}
				$(this).dialog('widget')
					.find('.ui-dialog-buttonpane')
					.prepend( button );
			},
			onLoad: function(){

				$.when( self._browse() )
				.progress( function( rid ){
					self._edit( rid );
				});

				// tagmanager.init() pushes a default URL to the history stack.
				// need to override that if an asset is already selected
				// by setting a fragment identifier on the parent window.
				if ( opts.asset_rid && opts.asset_rid > 0 ) {
					$.boom.log( 'getting asset ' + opts.asset_rid );
					self._edit( opts.asset_rid );
				}

				self.init({
					items: {
						asset: $.boom.items.asset,
						tag: $.boom.items.tag
					},
					options: {
						sortby: 'last_modified',
						order: 'desc',
						edition: 'site',
						type: 'assets',
						allowedUploadTypes:[ 'jpeg', 'gif', 'jpg', 'png' ],
						template: 'thumb',
						perpage: 10
					}
				});
			}
		};

		opts = $.extend( default_options, opts );

		self.asset_browser = $.boom.dialog.open( opts );

		return complete;
	},

	/**
	Set up an asset browser
	@returns {Object} promise which updates via .notify( rid ) when an asset is selected.
	*/
	_browse: function(){

		var self = this;
		var select_asset = new $.Deferred();

		$( self.asset_browser )
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
					$( self.asset_browser )
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
	_edit: function( rid ){

		var self = this;

		$.boom.history.load( 'asset/' + rid );
		//top.location.hash = '#asset/' + rid;
		self.selected_rid = rid;

	},

	/**
	Upload a new asset file.
	*/
	_upload: function( opts ){

		var self = this;
		var tagmanager = $.boom.assets;
		var uploaded = new $.Deferred();
		
		var default_opts = {
			url: '/cms/uploadify/asset',
			dataType: 'json',
			singleFileUploads: false,
			formData: [],
			submit: function( e, data ){
				$( '#b-upload-progress' ).progressbar();
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
		
		console.log( opts );

		var upload_dialog = $.boom.dialog.open({
			url:  '/cms/uploadify/form',
			width: 400,
			title: 'Upload file/s',
			onLoad: function(){
				
				var upload_token = $( '#upload_token' ).val();
				
				opts.formData.push( { name: 'upload_token', value: upload_token } );
				
				$( '#b-assets-upload-form' )
				.fileupload( opts );
			},
			buttons: {
				Cancel: function(){

					// TODO: cancel uploadify uploads

					$.boom.dialog.destroy( upload_dialog );
				}
			}
		});
		
		return uploaded;
	}
});

/**
@class
*/
$.boom.items.asset = $.extend(true, {}, $.boom.tagmanager.base.item);

$.extend($.boom.items.asset, {
	/** @lends $.boom.items.asset */

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

		self.tagmanager.elements.rightpane
		.find('.b-items-content')
		.sload(url, function(){
			$.boom.tagmanager.base.item.prototype.get.apply( self );

			$( this ).scrollTop( 0 );

			$.boom.events.register('asset.clickAfter', 'tagmanager', { rid: rid });

			$.boom.loader.hide();

			self.bind( this );

			// Make the tag editor work.
			$.boom.tags.init({
				type: 'asset',
				id: rid
			});

			$(this).find('.boom-tabs').tabs('option', 'active', 1);
		});
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


		$('#boom-button-asset-tags-add').click( function(){

			$.boom.assets.items.tag
				.picker()
				.done( function( tags ) {

					$.boom.loader.show();

					return $.post(
						'/cms/assets/add_tags/' + $('#asset_id').val(),
						{tags:  tags}
					);
				})
				.then( function( response ){
					$.boom.loader.hide();
					$.boom.history.refresh();
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

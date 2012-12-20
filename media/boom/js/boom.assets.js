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

		$('.b-tags-add').click(function(){
			$.boom.assets.items.tag.add( this );
		});

		$('#b-assets-upload-video').click(function(){
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
		});

		$('#b-assets-filter-title').autocomplete({
			delay: 200, // Time to wait after keypress before making the AJAX call.
			source: function(request, response){
				$.ajax({
					url: '/cms/autocomplete/assets',
					dataType: 'json',
					data: {
						text : $('#b-assets-filter-title').val()
					}
				})
				.done(function(data) {
					response(data)
				});
			},
			select: function(event, ui){
			}
		});

		$('#b-assets-upload').click(function(){

			self._upload();
		});

		$( '#boom-tagmanager' )
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

				$.boom.assets.items.tag
					.picker()
					.done( function( tags ) {

						$.boom.loader.show();

						return $.post(
							'/cms/assets/tag/' + ids.join('-'),
							{tags:  tags}
						);
					})
					.then( function( response ){
						$.boom.loader.hide();
						$.boom.history.load( 'asset/' + ids.join( '-' ) );
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
		}

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
	@function
	*/
	uploader : function( data ) {

		var upload_token = $( '#upload_token' ).val();

		$( '#b-assets-upload-form' ).find( 'input' ).hide();

		var upload = function( data ) {

			var complete = new $.Deferred();

			$.ajax( {
				xhr: function(){
					var xhr = $.ajaxSettings.xhr();
					xhr.upload.addEventListener( 'progress', function( e ){
						complete.notify( e );
					});
					return xhr;
				},
				type : 'POST',
				url : '/cms/uploadify/asset',
				data : data,
				success: function( response ) {
					complete.resolve( response );
				},
				error: function() {
					complete.reject();
				},
				cache: false,
				contentType: false,
				processData: false
			}, 'json' );

			return complete;
		}

		return upload( data )
		.progress( function( e ){
			var percent = parseInt( (e.loaded / e.total * 100), 10) + "%";

			$( '#upload-advanced span.message' ).text( 'Uploaded ' + percent );
		})
		.pipe( function( data ){
			return $.post(
					'/cms/uploadify/get_rids',
					{ upload_token: upload_token },
					function(){ console.log( this ); },
					'json'
				);
		});


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
				var rid = parseInt( data[1] );
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

		//$.boom.history.load( 'asset/' + rid );
		top.location.hash = '#asset/' + rid;
		self.selected_rid = rid;

	},

	/**
	Upload a new asset file.
	*/
	_upload: function(){

		var self = this;
		var tagmanager = $.boom.assets;

		var upload_dialog = $.boom.dialog.open({
			url:  '/cms/uploadify/form',
			width: 400,
			title: 'Upload file/s',
			onLoad: function(){

				if( window.FormData ){
					$( this )
					.find( '#b-assets-upload-form' )
					.on( 'submit', function( event ){

						var formdata = new FormData( this );
						var upload_token = $( '#upload_token' ).val();

						event.preventDefault();

						formdata.append( 'person_id', $.boom.config.person.rid );

						self.uploader( formdata )
							.done( function( data ){

								$.boom.dialog.destroy( upload_dialog );
								top.location.hash = '#asset/' + data.rids.join('-');
								tagmanager.selected_rid = data.rids[0];
							});
					});
				} else {
					self.bindUploadify( this )
						.done( function( data ){

							$.boom.dialog.destroy( upload_dialog );
							top.location.hash = '#asset/' + data.rids.join('-');
							tagmanager.selected_rid = data.rids[0];
						});
				}
			},
			buttons: {
				Cancel: function(){

					// TODO: cancel uploadify uploads

					$.boom.dialog.destroy( upload_dialog );
				}
			}
		});
	},

	/** @function */
	bindUploadify : function(dialog){

		var
			formData = $( '#b-assets-upload-form' ).serializeArray(),
			jsonForm = {},
			totFiles = 0,
			selectedFiles = {},
			i = 1,
			uploading = new $.Deferred();

		for (field in formData) {

			jsonForm[formData[field].name] = formData[field].value
		}

		// see: http://www.uploadify.com/documentation/
		var uploadifyConfig = $.extend({}, $.boom.config.uploadify, {

			formData: jsonForm,

			fileTypeExts: '*.' + this.options.allowedUploadTypes.join(';*.'),

			onSelect: function(file){

				$.boom.dialog.resize(dialog);

				selectedFiles[ file.name ] = file;
			},

			onUploadComplete: function(){

				totFiles ++;
			},

			onSelectOnce: function(event, queueID, fileObj){
				$('#b-assets-upload-fileUploader').attr('height', 0);

				$('#boom-asset-upload-info').remove();

				$.boom.dialog.resize(dialog);
			},

			onQueueComplete: function(queueData) {
				if (queueData.uploadsErrored > 0) {

					console.log(queueData.uploadsErrored);

					$.boom.dialog.alert('Error', 'Sorry, there were errors uploading your files. Please try again.', function(){

						$.boom.dialog.destroy( dialog );
					});

				} else {

					$.boom.loader.show();

					$
						.post('/cms/uploadify/get_rids', { upload_token: jsonForm.upload_token }, function(){}, 'json')
						.success( function(data){
							$.boom.loader.hide();

							if ( data && data.rids && data.rids.length ) {

								uploading.resolve( data );

							} else if ( data && data.errors && data.errors.length ) {

								var errorStr = 'There were errors uploading ' + ( $.size(errors) == $.size(selectedFiles) ? '' : 'some of ' ) + 'your file/s:<br /><br /><ul>';

								$.each( errors, function(i){

									errorStr += '<li><strong>' + selectedFiles[i].name + '</strong>: <ul>';

									$.each(this, function(k){

										errorStr += '<li>' + this + '</li>';
									});

									errorStr += '</ul>'
								});

								errorStr += '</li></ul>';

								$.boom.dialog.alert('Notice', errorStr, function(){

									if (rids.length) {

										$.boom.history.load('asset/' + rids.join(','));
									}

								}, 400);
							}
						});
				}
			},

			onError: function(event, queueID, fileObj, errorObj){
				console.Log( errorObj );
				// TODO
				console.log('event');
				console.log(event);
				console.log('HTTP error');
				console.log(errorObj);
				console.log('File');
				console.log(fileObj)
			},

			onUploadError : function(file, errorCode, errorMsg, errorString) {
				alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
			}
		});

		$('#b-assets-upload-file').hide();

		$.ajax({
			type: "GET",
			url: "/cms/uploadify/asset?get_token=1&status=",
			success: function(token) {
				// jsonForm["auth_token"] = token;
				jsonForm["person_rid"] = $.boom.config.person.rid;

				uploadifyConfig["formData"] = jsonForm;

				setTimeout(function(){
					$('input#b-assets-upload-file').uploadify(uploadifyConfig);
				}, 40);
			}
		});

		return uploading;
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

			$(this).find('.boom-tabs').tabs('select', 1);
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
						'/cms/assets/tag/' + $('#asset_id').val(),
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

			$.post('/cms/assets/untag/' + $('#asset_id').val(), {tags:  tags})
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

			var upload_dialog = $.boom.dialog.open({
				url:  '/cms/uploadify/form',
				width: 400,
				title: 'Upload file/s',
				onLoad: function(){

					$( '#b-assets-upload-form' )
						.append( '<input type="hidden" name="asset_id" value="' + rid + '" />')
						.append( '<input type="hidden" name="asset_ids" value="' + rids + '" />')

					if( window.FormData ){
						$( this )
						.find( '#b-assets-upload-form' )
						.on( 'submit', function( event ){

							var formdata = new FormData( this );
							var upload_token = $( '#upload_token' ).val();

							event.preventDefault();

							formdata.append( 'person_id', $.boom.config.person.rid );

							$.boom.assets.uploader( formdata )
								.done( function( data ){

									$.boom.dialog.destroy( upload_dialog );
									top.location.reload( true );
								});
						});
					} else {
						$.boom.assets.bindUploadify( this )
							.done( function( data ){

								$.boom.dialog.destroy( upload_dialog );
								top.location.reload( true );
							});
					};

				},
				buttons: {
					Cancel: function(){

						// TODO: cancel uploadify uploads

						$.boom.dialog.destroy( this );
					}
				}
			});

		});
	}
});

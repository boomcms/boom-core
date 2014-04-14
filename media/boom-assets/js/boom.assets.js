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
			width: '60%',
			height: '700',
			dialogClass : 'b-dialog b-assets-dialog',
			title: 'Select an asset',
			cache: false,
			open: function(){
				$.boom.log( 'dialog open' );
				var dialog = $(this);

				var remove = $('<button />')
					.addClass('ui-helper-left b-button ui-button')
					.text('Remove')
					.button({
						text: false,
						icons: { primary : 'b-button-icon b-button-icon-delete' }
					})
					.click(function(){
						complete.reject();
						cleanup();
						dialog.dialog('destroy');
					});
				var upload = $('<button />')
					.addClass('ui-helper-left b-button ui-button')
					.text( 'Upload' )
					.button({
						text: false,
						icons: { primary : 'b-button-icon b-button-icon-upload' }
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
				browser = $('#b-assets-manager').browser_asset();

				$.when(browser.browser_asset('browse'))
					.progress(function(asset_id) {
						cleanup();
						complete.resolve(asset_id);
						dialog.close();
						browser.remove();
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

		dialog = new boomDialog(opts)
			.done(function() {
				var asset_id = browser.browser_asset( 'get_asset' );
				cleanup();
				complete.resolve(asset_id);
				return false;
			});

		return complete;
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
		$.boom.log( 'asset browser init' );

		this.item = $.boom.asset;
		this.tag = $.boom.filter_assets;

		$.boom.browser.prototype._create.call( this );
	},

	_bind: function(){

		$.boom.browser.prototype._bind.call( this );

		var self = this;

		$('#b-assets-upload')
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
					start: function(e) {
						var dialog = new boomDialog({
							url: '/cms/tags/asset/list/0',
							title: 'Asset tags',
							width: 440,
							onLoad: function(){
								// Make the tag editor work.
								$( '#b-tags' ).tagger_deferred( { tags : tags } );
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
						})
						.done(function() {
							tagged.resolve( tags );
						});
					}
				})
				.done( function( data ){
					$.boom.history.load( self.options.defaultRoute );
				});

				$.when(tagged, uploaded).done(function(tags, data) {
					var promises = [];

					for (i in tags) {
						var request = $.post(
							'/cms/tags/asset/add/' + data.result.join('-'),
							{
								tag : tags[i]
							}
						);

						promises.push(request);
					}

					$.when( promises )
						.pipe( function(){
							return $( '#b-tags-search' )
							.tagger_search( 'do_search' );
						})
						.done(function() {
							for (i in data.result){
								$('.thumb[data-asset=' + i + '] a').click();
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


		var selected_tag_ids = [];
		$('#b-tags-search')
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
			})
			.on('click', '#b-assets-filter-title, #b-tags-search input', function() {
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

		return select_asset;

	},

	/**
	Open the asset editing view
	@param {Integer} rid RID of the currently selected asset.
	@returns {Object} promise resolved when the text is set.
	*/
	edit: function(rid) {
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
				$('#b-assets-upload-progress').progressbar();
				$('#b-assets-upload-cancel').css('display', 'block');

				file_data = data;
			},
			progressall: function( e, data ){
				var percent = parseInt( (data.loaded / data.total * 100), 10);

				$( '#b-assets-upload-progress' ).progressbar( 'value', percent );
			},
			done: function( e, data ){
				$.boom.log( 'file upload complete' );

				$( '#b-assets-upload-container p.message' ).text("File upload completed");
				$('#b-assets-upload-progress').progressbar('destroy');
				$('#b-assets-upload-cancel').hide();

				uploaded.resolve( data );
			},
			fail: function(e, data) {
				message = 'Errors occurred during file upload:<br />';

				var errors = $.parseJSON(data.jqXHR.responseText);

				for (i in errors) {
					message = message + errors[i] + '<br />';
				}

				 $( '#b-assets-upload-container p.message' ).html(message);
				 $('#b-assets-upload-progress').progressbar('destroy');
				 $( '#b-assets-upload-cancel' ).hide();
			 },
			 always: function( e, data ){
				 $.boom.log( 'file upload finished' );
			 }
		 });

		opts = $.extend( default_opts, opts );

		$.get('/cms/assets/upload')
			.done(function(response) {
				self.showContent(response);

				if ($('.ui-dialog-content').length) {
					$('#b-assets-upload-container').height($('.ui-dialog-content').height() - 30);
				} else {
					var height = $(window).height() - $('#b-topbar').height() - 30;
					$('#b-assets-upload-container').height(height + 'px');
				}

				opts.formData.push( { name: 'csrf', value: $('input[name=csrf]').val() } );

				$( '#b-assets-upload-form' ).fileupload( opts );

				$( '#b-assets-upload-cancel' )
					.on( 'click', function(){
						file_data.jqXHR && file_data.jqXHR.abort();

						$(this).hide();
						$('#b-assets-upload-progress').progressbar('destroy');
						$( '#b-assets-upload-container p.message' ).text('Upload was canceled');
					});
			});

		return uploaded;
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
	}
});
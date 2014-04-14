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
							cancelButton : false,
							onLoad: function(){
								// Make the tag editor work.
								$( '#b-tags' ).tagger_deferred( { tags : tags } );
							}
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

		$.when( self.browse() )
			.progress( function( rid ){
				$( '#asset-list-' + rid ).click();
			});
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
	}
});
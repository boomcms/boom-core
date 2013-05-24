/**
@fileOverview jQuery UI widgets for editable slots.
*/
/**
@namespace
@name $.ui
*/

/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('ui.chunk',

	/**
	@lends $.ui.chunk
	*/
	{

	/**
	default config
	@property options
	*/
	options: {
		/** URL prefix for ajax calls */
		urlPrefix:  '/cms/chunk'
	},

	_create : function(){
		$.boom.log( 'CHUNK CREATE' );
	},

	_init : function(){
		$.boom.log( 'CHUNK INIT' );

		this.edit();
	},

	/**
	update slot HTML and push changes to the stack of page edits.
	@function
	*/
	_apply: function(replacedata){

		this._update_html( replacedata );

		$.boom.log( 'push changes onto the stack' );
		this._save_slot();

	},
	
	/**
	Remove slot from the page
	@function
	*/
	_remove: function( data ){
		var self = this;

		return this._preview( data )
			.done( function( response ){
				self._update_html( response );
				self._save_slot( { "delete" : true } );
			});
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html : function( html ) {
		// TODO: rewrite this mess.

		var $el = top.$( this.element );

		var replacedata =
			top.$( html )
			.insertAfter( $el );

		// this line implicitly calls _destroy(), by removing the element.
		this.element.remove();

		this.element = replacedata;

	},

	/**
	Push the current state of the slot on to the edits stack
	@function
	*/

	_save_slot : function( data ) {
		
		data = ( data ) ? data : this.getData();

		$.boom.page.slot_edits.push( {
			slot: this.options.slot,
			data: data
		} );

		$.boom.page.save_button.button( 'enable' ).attr( 'title', 'Save page' );
		$.boom.page.cancel_button.button( 'enable' ).attr( 'title', 'Cancel' );
	},

	/**
	Get the base ajax URL for previewing the slot HTML
	@function
	@returns {String} URL for this slot's HTML
	*/
	_preview_url : function() {
		return this.options.urlPrefix +
		'/' + this.options.slot.type +
		 '/preview/' + $.boom.page.options.id;
	},
	
	/**
	Get the POST data for a preview request
	@function
	@returns {Object} slot data including slotname and template
	*/
	_slot_data : function( data ) {
		return $.extend( 
				{ data : data },
				{ 
					slotname: this.options.slot.name,
					template: this.options.slot.template
				 });
	},
	
	_preview : function( data ) {
		
		return $.post( this._preview_url(), this._slot_data( data ) );
	},

	/**
	Bring the slot UI forward, above all other page elements.
	@function
	*/
	_bring_forward : function() {

		this.element.css( {
			'z-index' : 1000,
			'position' : 'relative'
		});
		top.$( 'body' ).prepend( '<div class="overlay"></div>' );

	},
	/**
	Drop the slot UI back into its natural place in the page z-index stack.
	@function
	*/
	_send_back : function() {

		this.element.removeAttr( 'style' );
		top.$( 'div.overlay' ).remove();

	},

	/**
	Remove widget from the DOM and clean up.
	@function
	*/
	_destroy : function(){
		$.boom.log( 'rebinding page chunks' );

		$.boom.page.editor.bind();
	}
});
/**
Editable text slots
@class
@name chunkText
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkText', $.ui.chunk,

	/**
	@lends $.ui.chunkText
	*/
	{

	title : '',

	content : '',

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Text chunk slot edit');

		if ( !$( 'body' ).editor( 'ready' ) ) {

			$.boom.dialog.alert('Error', 'The page editor has not been fully downloaded yet, please wait a few seconds before trying again.');

			return;
		}

		var $content = this.element.find( '.slot-content');

		var edit_content = function( $element ) {

			$element[0].id = $element[0].id || $.boom.util.dom.uniqueId('boom-dom-wysiwyg-');

			var old_html = self.element.html();
			
			if ( $element.text() == 'Default text.' ) {
				$element.html( '' );
			}
			self._bring_forward();

			$( 'body' ).editor( 'edit', $element )
				.fail( function(){
					self.element.html( old_html ).show();
					self.destroy();
				})
				.done( function( html ){
					self._apply( html );
					
					if ( $element.text() == '' ) {
						self._remove();
					}
				})
				.always( function(){
					if ( $element.text() == '' ) {
						$element.html( old_html );
					}
					self._send_back();
				});

		};

		if ( $content.length ) {

			edit_content( $content );
			this.element
				.find( '.slot-title' )
				.attr( 'contenteditable', 'true' );

		} else {

			edit_content( this.element );
		}

	},

	/**
	Get the chunk HTML, escaped and cleaned.
	*/
	getData : function(){
		var $content = this.element.find( '.slot-content');

		if ( $content.length ) {
			this.content = $content.html();
			this.title = this.element.find( '.slot-title').text();
		} else {
			this.title = null;
			this.content = this.element.html();
		}

		return { title : this.title, text : this.content.cleanup() };
	},

	/**
	Update the page with edited HTML from the editor, then remove TinyMCE.
	@param {String} replacedata HTML to insert into the page.
	*/
	_apply: function(replacedata){

		//replacedata = $( replacedata );

		if( replacedata ) {
			this.element
				.html( replacedata )
				.show();
		}

		this.element
			.find( '[contenteditable]' )
			.removeAttr( 'contenteditable' )
			.off( 'click' );

		this._save_slot();

		this.destroy();
	},
	
	/**
	Remove the slot from the page.
	*/
	_remove: function(){

		if( this.element.is( 'div' ) ) {
			this.element
				.html( '<p>Default text.</p>' )
				.show();
		} else {
			this.element
				.text( 'Default text.' )
				.show();
		}

		this.element
			.find( '[contenteditable]' )
			.removeAttr( 'contenteditable' )
			.off( 'click' );

		this._save_slot( { "delete" : true } );

		this.destroy();
	}

});
/**
@class
@name chunkLinkset
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkLinkset', $.ui.chunk,
	/**
	@lends $.ui.chunkLinkset
	*/
	{

	elements : {},

	/**
	Open a dialog to edit the slected linkset.
	*/
	edit : function(){

		$.boom.log('Linkset chunk slot edit');

		var self = this;

		this.options.treeConfig = $.extend({}, $.boom.config.tree, {
			height: 'auto',
			overflow: 'hidden',
			click: false,
			toggleSelected: false,
			showEdit: false,
			showRemove: true,
			onRemoveClick: function(event){
				var item = $( event.target ).closest( 'li' );
				item.remove();
			},
			iconRemove: 'ui-icon-close'
		});

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/linkset/edit/' + $.boom.page.options.id,
			title: 'Edit linkset',
			id: self.element[0].id + '-boom-dialog',
			width: 400,
			destroy: function(){
				self.destroy();
			},
			treeConfig: this.options.treeConfig,
			open: function(){

				self.elements.currentLinks = $( this ).find('.boom-chunk-linkset-links-set');

				self.elements.internalLinks = $( this ).find('.boom-chunk-linkset-internal-links');

				self._buildList();

			},
			onLoad: function(){
				self._bindEvents();
			},
			callback: function(){
				self
				._insert()
				.done( function(){
					self.destroy();
				});
			}
		});

	},

	/**
	Clone the linkset links for editing.
	*/
	_buildList : function(){

		var self = this, clones = this.element.find('li').not('.boom-chunk-linkset-addlink').clone();

		this.elements.currentLinks.append( clones );

		if ( !this._refresh() ) {

			setTimeout(function(){

				$.boom.log('Check linkset links');

				self.dialog.find('.boom-tabs:first').tabs('options', 'active', 1);
			});
		}
	},

	/**
	Bind button and tree control events.
	*/
	_bindEvents : function(){

		var self = this;
		
		$.boom.util.page_tree( this.elements.internalLinks )
			.progress( function( page ) {
				var anchor = 
					$( '<a>')
						.attr( 'rel', page.page_id )
						.attr( 'href', page.url )
						.text( page.title );
				self._add( anchor );
			});

		$('#boom-chunk-linkset-addlink-external-button').click(function(){

			var
				url = $('#boom-chunk-linkset-addlink-external-url').val().trim(),
				copy = $('#boom-chunk-linkset-addlink-external-copy').val().trim(),
				form = $('#boom-chunk-linkset-addlink-external-form'),
				exp = /^https?:\/\//, m, protocol;

			while( m = exp.exec(url) ) {
				url = url.replace(exp, '');
				protocol = m[0];
			}
			if (!protocol) protocol = 'http://';

			if ( url && copy ) {

				url = protocol + url;

				var anchor = $('<a />', { href: url }).text( copy );

				self._add(anchor);

				form.get(0).reset();
			}

			return false;
		});
	},

	/**
	FIXME: Not sure what this does.
	*/
	_refresh: function(){

		if (!this.elements.currentLinks.children().length) {

			$('#boom-chunk-linkset-urls-valid').hide();
			$('#boom-chunk-linkset-urls-invalid').show();

			return false;
		} else {

			$('#boom-chunk-linkset-urls-valid').show();
			$('#boom-chunk-linkset-urls-invalid').hide();

			return true;
		}
	},

	/**
	Add a new link to the list in the linkset dialog.
	@param {Object} anchor <a> element for the new link.
	*/
	_add: function(anchor) {

		var link = $('<li />').hide().append(anchor);

		this.elements.currentLinks.append(link).tree(this.options.treeConfig);

		this._refresh();

		this.dialog.find('.boom-tabs:first').tabs('option', 'active', 0);

		link.fadeIn(function(){
			$(this).removeAttr('style');
		});
	},

	/**
	Add a new link to the list in the linkset dialog.
	*/
	_insert: function(){

		var
			self = this;

		var data = this._getData( this.elements.currentLinks );
		if ( data.links.length == 0 ){
			return this._remove( data );
		} else {
			return self._preview( data )
				.done( function(data) {

					$.boom.loader.hide('dialog');

					self._apply(data);

					//self.destroy();
				});
		}
	},

	/**
	Get the linkset data as an object
	@returns {Object} Simple object containing an array of link objects {links: [ { name: name, uri: uri, target_page_rid: page RID, sequence: sequence }]}
	*/
	getData : function() {

		return this._getData( this.element );
	},

	/**
	Get the linkset data as an object
	@param {Object} element Container element for this linkset.
	@returns {Object} Simple object containing an array of link objects {links: [ { name: name, uri: uri, target_page_rid: page RID, sequence: sequence }]}
	*/
	_getData : function( element ) {

		var links = [];

		element.find('a').each(function(sequence){

			// ensure internal links have no domain attached to them
			var url =
				!this.rel ?
				this.href.safeEscape() :
				this.href.replace(/[a-zA-Z]{4,5}\W{3}[^\/]+/, '').safeEscape();


			var link = {
				title: $(this).text(),
				url: url,
				target_page_id: $(this).attr('rel'),
				sequence: sequence
			};

			links.push( link );
		});

		return { links: links };
	}

});
/**
@class
@name chunkFeature
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkFeature', $.ui.chunk,
	/**
	@lends $.ui.chunkFeature
	*/
	{

	/**
	Open a dialog with a tree control to pick a page for the current feature
	and a button to remove any existing page without replacing it.
	*/
	edit : function(){

		$.boom.log('Feature chunk slot edit');

		var self = this;

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/feature/edit/' + $.boom.page.options.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			// cache: true,
			title: 'Page feature',
			onLoad : function() {
				
				$.boom.util.page_tree( self.dialog.find( '.boom-tree' ), self.options.rid )
					.progress( function( page ){
						self.insert( page.page_id );

						$.boom.dialog.destroy(self.dialog);
					});
			
			},
			destroy: function(){
				self.destroy();
			},
			open: function(){

				if ( self.options.slot.rid > 0 ) {
					
					$( 'input[name=parent_id]' ).val( self.options.slot.rid );

					var button = $('<button />')
					.addClass('ui-helper-left')
					.text('Remove')
					.button({
						text: false,
						icons: { primary : 'ui-icon-boom-delete' }
					})
					.click(function(){

						$.boom.dialog.destroy(self.dialog);

						self._remove( { target_page_id : 0 } );
					});

					$(this).dialog('widget')
						.find('.ui-dialog-buttonpane')
						.prepend( button );
				}
			}
		});
	},

	/**
	Insert the selected page into the DOM as a feature box.
	@param {Int} rid Page RID
	*/
	insert : function(rid){

		var self = this;
		this.options.slot.rid = rid;

		$.boom.loader.show();

		return this._preview( this.getData() ).done( function( data ){

			$.boom.loader.hide();

			self._apply( data );

			//self.destroy();
		});
	},

	/**
	Get the RID for this feature box.
	@returns {Int} Page RID
	*/
	getData: function(){

		return { target_page_id : this.options.slot.rid };
	},

	/**
	Remove the current feature from the page.
	*/
	remove : function(){
		var self = this;

		this.insert( 0 )
			.done( function( response ){
				self._remove();
			});
	}
});

/**
@class
@name chunkAsset
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkAsset', $.ui.chunk,
	/**
	@lends $.ui.chunkAsset
	*/
	{

	/**
	Initialise the caption and asset editor
	*/
	_init : function() {
		var self = this;
		
		self.asset = this._get_asset_details();
		
		switch ( self.element[0].nodeName ){
			case 'IMG':
				this.originals = self.asset.element.clone( true );
			break;
			
			default:
			
				this.originals = 
					this.element
					.children()
					.clone( true );
				
			break;
		}

		this._build_ui()
			.done( function(){
				$.ui.chunk.prototype._init.call( self );
			});


	},
	
	/**
	@function
	*/
	_build_ui : function() {

		this._bring_forward();

		return $.get( '/media/boom/toolbars/asset.php' )
			.done( function( toolbar ){
				$.boom.page.toolbar.hide();
				top.$( 'body' )
					.prepend( toolbar );
			});

	},
	
	/**
	@function
	*/
	_remove_ui : function() {

		this._send_back();

		$.boom.page.toolbar.show();

		this.element
			.find( '.asset-caption' )
			.removeAttr( 'contentEditable style' )
			.end()
			.off( 'focus mouseover' );
		top.$( 'body' )
			.find( 'div.toolbar' )
			.remove();
	},
	
	/**
	Edit the asset
	@param {Object} $caption Caption node
	@returns {Deferred}
	*/
	_edit_asset : function() {
		
		var self = this;
		var asset_selected = new $.Deferred();
		
		// cleanup code when the dialog closes.
		asset_selected
		.fail( function() {
			$.boom.log( 'asset chunk cancelled' );
		});

		return $.boom.assets.picker( {
			asset_rid : self.asset.asset_id,
			deferred: asset_selected
		} )
		.pipe( function( rid ){

			self.asset.asset_id = rid;
			return $.boom.links.picker( {
				page_rid: $.boom.page.options.id,
				title: 'Add a link',
				link: {
					url: self.asset.url,
					rid: -1,
					title: ''
				}
			});
		})
		.done( function( link ){
			self.insert( self.asset.asset_id, link );
		})
		.fail( function() {
			var data = { asset_id : 0, link : self.asset.url, caption: self.asset.description } ;
			self.asset.asset_id = 0;
			self._remove( data );
		})
		.always( function(){
			$.boom.history.load( '' );
		});
	},
	
	/**
	Edit a caption
	@param {Object} $caption Caption node
	@returns {Deferred}
	*/
	_edit_caption : function( $caption ) {

		var edited = new $.Deferred();

		$caption
			.attr( 'contentEditable', 'true' )
			.on( 'focus mouseover', function(){
				$( this )
					.css( 'border', '1px solid black' );
			})
			.on( 'blur mouseout', function(){
				$( this )
					.removeAttr( 'style' );
			})
			.on( 'blur', function(){
				edited.resolve();
			})
			.on( 'keyup click', function( e ){
				e.stopPropagation();
				e.preventDefault();	
			} );

			if ( $.trim( $caption.text() ) == '' ) {
				$caption.text( 'Default text' );
			}

		return edited;
	},
	
	/**
	Remove editor UI and exit
	*/
	_destroy : function() {
		$.boom.log( 'exiting asset editor' );
		var self = this;

		this._remove_ui();

		$.ui.chunk.prototype._destroy.call( this );

	},
	
	/**
	@function
	*/
	_get_asset_details: function(){
		
		var asset_id = 0;
		var url = this.element.find( '.asset-link' ).attr( 'href' );
		var caption = this.element.find( '.asset-caption' );
		var element = this.element.find( 'img' );
		var img_src = element.attr( 'src' );
		
		switch( this.element[0].nodeName ){
			
			case 'A':
				url = this.element.attr( 'href' );
				if( img_src ) asset_id = img_src.match( /asset\/(thumb|view)\/([0-9]+)/ );
				if ( asset_id != null && asset_id.length ) asset_id = asset_id[ 2 ];
			break;
			
			case 'IMG': 
				element = this.element;
				asset_id = element[0].src.match( /asset\/(thumb|view)\/([0-9]+)/ );
				if ( asset_id != null && asset_id.length ) asset_id = asset_id[ 2 ];
				url = '';
			break;
			
			default:
				if( img_src ) asset_id = img_src.match( /asset\/(thumb|view)\/([0-9]+)/ );
				if ( asset_id != null && asset_id.length ) asset_id = asset_id[ 2 ];
			break;
		}
		
		var asset = {
			asset_id : asset_id,
			title : null,
			description : caption.text(),
			url : url,
			element: element
		};
		
		return asset;
	},
	
	/**
	Cancel changes and exit
	@function
	*/
	_cancel : function() {

		var self = this;

		if (self.edited ) {

			$.boom.dialog.confirm(
				'Cancel changes',
				'Cancel changes to this asset?'
			)
			.done( function(){
				
				switch ( self.element[0].nodeName ){
					case 'IMG':
						self.element.replaceWith( self.originals );
					break;

					default:
						self.element
							.children()
							.remove()
							.end()
							.append( self.originals );
						self.destroy();
					break;
				}
			});

		} else {
			self.destroy();
		}
	},
		
	/**
	Asset editor.
	*/
	edit: function(){

		var self = this;
		var caption = this.element.find( '.asset-caption' );

		$.boom.log('Asset chunk slot edit ' + self.asset.asset_id);
		
		if ( caption.length > 0 ) {
			
			this.element
				.on( 'click', function( event ) {
					event.stopPropagation();
					event.preventDefault();
				})
				.on( 'click', 'a', function( event ) {
					event.preventDefault();
					return false;
				})
				.on( 'click', 'img', function( event ) {

					self._edit_asset( self.asset.element );

				})
				.find( '.asset-caption' )
				.each( function(){
					self
						._edit_caption( $( this ) )
						.done( function(){
							self.asset.description = self.element.find( '.asset-caption' ).text();
							self.edited = true;
						});
				});
		} else {
			
			this.element.on( 'click', function(){
				self._edit_asset( self.asset.element );
			});
			
		}
		
		top.$( 'div.toolbar' )
			.on( 'click', 'button.cancel', function(){
				self._cancel();
			})
			.on( 'click', 'button.save', function(){
				self._save_slot();
				self.destroy();
			});
	},

	/**
	Insert selected asset into the page
	@param {Int} rid Asset RID
	*/
	insert: function(rid, link) {
		$.boom.log( 'inserting asset' + rid );

		var self = this;
		var caption = this.element.find( '.asset-caption' );
		link = ( link ) ? link : { url : null };
		self.asset = $.extend( self.asset, {
			asset_id : rid,
			title : null,
			description : caption.text(),
			url : link.url
		});

		$.boom.loader.show();

		var data = { asset_id : rid, link : link.url, caption: caption.text() } ;

		self._preview( data )
		.done( function( data ){

			$.boom.loader.hide();
			
			var new_asset = $('<div>').append( data ).find( 'img' );
			self.edited = true;

			self.asset.element
				.attr( 'src', new_asset.attr( 'src' ) );
		})
		.fail( function( data ) {
			$.boom.log( 'asset chunk error ' );
			console.log( data );
		});
	},

	/**
	Get the RID for this asset.
	@returns {Int} Asset RID
	*/
	getData: function() {

			var rid = this.asset.asset_id;

			rid = (rid == 0) ? null : rid;

			return {
				asset_id : rid,
				title : null,
				caption : this.asset.description,
				link : this.asset.url
			};
	},

	/**
	Remove the current asset from the page.
	*/
	remove : function(){

		self.rid = 0;

		this.insert( self.rid );
	}


});
/**
@class
@name chunkSlideshow
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkSlideshow', $.ui.chunk,
	/**
	@lends $.ui.chunkSlideshow
	*/
	{

	elements : {},

	/**
	@function
	*/
	_create : function() {
		this.edited = false;

		this.originals = this.element
			.find( 'ul.slides li' )
			.clone( true );

		$.ui.chunk.prototype._create.call( this );
	},

	/**
	Pause the slideshow on startup
	*/
	_init : function() {
		var self = this;
		//FIXME: slideshows are tied to flexslider
		var slider = top.$( this.element ).find( '.flexslider' ).data( 'flexslider' );
		this.options.slider = slider;

		this._build_ui()
			.done( function(){
				$.ui.chunk.prototype._init.call( self );
			});


	},

	/**
	@function
	*/
	_build_ui : function() {

		this._bring_forward();

		this.options.slider.pause();

		return $.get( '/media/boom/toolbars/slideshow.php' )
			.done( function( toolbar ){
				$.boom.page.toolbar.hide();
				top.$( 'body' )
					.prepend( toolbar );
			});

	},

	/**
	@function
	*/
	_remove_ui : function() {

		this._send_back();

		$.boom.page.toolbar.show();

		this.options.slider.play();

		this.element
			.find( '.slide-caption' )
			.each( function(){
				$( this ).removeAttr( 'contentEditable style' );
			})
			.off( 'focus mouseover' )
			.end();
		top.$( 'body' )
			.find( 'div.toolbar' )
			.remove();
	},

	/**
	Open a slideshow dialog
	*/
	edit: function(){

		$.boom.log('Slideshow chunk slot edit');

		var self = this;
		//$.boom.page.slot_edits.push( 'this is a hack to enable debugging' );

		this.element
			.on( 'click', function( event ) {
				event.stopPropagation();
				event.preventDefault();
			})
			.on( 'click', 'a', function( event ) {
				event.preventDefault();
				return false;
			})
			.on( 'click', '.slide-asset', function( event ) {
				self
					._edit_slide( $( this ) )
					.always( function(){
						self.edited = true;
					});
			})
			.find( '.slide-caption' )
			.each( function(){
				self
					._edit_caption( $( this ) )
					.done( function(){
						self.edited = true;
					});
			});
		top.$( 'div.toolbar' )
			.on( 'click', 'button.insert', function( event ) {
				var slideshow = self.options.slider;
				var slide = slideshow.slides[ slideshow.currentSlide ];

				self
					._insert_slide( $( slide ) )
					.done( function(){
						self.edited = true;
					});
			})
			.on( 'click', 'button.delete', function( event ) {
				var slideshow = self.options.slider;
				var slide = slideshow.slides[ slideshow.currentSlide ];

				$.boom.dialog.confirm(
					'Delete slide',
					'Delete this slide?'
				)
				.done( function(){
					self._remove_slide( $( slide ) );
				});
			})
			.on( 'click', 'button.cancel', function(){
				self._cancel();
			})
			.on( 'click', 'button.save', function(){
				self._insert()
					.done( function(){
						$.ui.chunk.prototype._destroy.call( this );
					});
			})
			.on( 'click', 'button.sort', function(){
				self._sort()
					.done( function(){
						self.edited = true;
						$.boom.log( 'sort finished' );
					});
			});

	},

	/**
	Edit a slide image and link
	@function
	@param {Object} $slide Slide to edit
	@returns {Deferred}
	*/
	_edit_slide : function( $slide ){
		var self = this;

		$.boom.log( 'edit slide' );
		var url_segments = $slide.attr( 'src' ).split( '/' );
		var rid = url_segments[3];
		var asset_selected = new $.Deferred();

		// cleanup code when the dialog closes.
		asset_selected
		.fail( function() {
			$.boom.log( 'slide asset cancelled' );
		})
		.always( function() {
			$.boom.log( 'slide asset cleanup' );
		})
		;

		return $.boom.assets.picker( {
			asset_rid : rid,
			deferred: asset_selected
		} )
		.pipe( function( rid ){
			
			var link = $slide.closest( 'a.slide-link' );

			url_segments[3] = rid;
			$slide.attr( 'src', url_segments.join( '/' ) );
			return $.boom.links.picker( {
				title: 'Add a link',
				link : {
					url : link.attr( 'href' ),
					rid : link.attr( 'rel' ),
					title : $slide.attr( 'alt' )
				}
			});
		})
		.done( function( link ){
			$slide
				.closest( 'a.slide-link' )
				.attr( 'href', link.url );
			$slide.attr( 'alt', link.title );
		})
		.fail( function() {

			self._remove_slide( $slide );

		})
		.always( function(){
			$.boom.history.load( '' );
		});
	},

	/**
	Insert a slide after an existing slide
	@function
	@param {Object} $li Insert the new slide after this item
	*/
	_insert_slide : function( $li ) {
		var self = this;
		var asset_selected = new $.Deferred();
		var $new_slide = $li
			.clone()
			.insertAfter( $li );

		return self._edit_slide( $new_slide.find( '.slide-asset' ) )
			.done( function() {

				var slides = $.makeArray( self.element.find( 'ul.slides li').not( '.clone' ) );
				var target = $.inArray( $new_slide[0], slides );

				// self._refresh()
				// 					.done( function(){
				// 						$.boom.loader.hide();
				// 						console.log( 'inserted new slide ' + target );
				// 						self.options.slider.flexAnimate( target );
				// 					});

				self.options.slider.count++;
				self.options.slider.flexslider( 'next' );
			});
	},

	/**
	@function
	@param {Object} $slide Slide to remove
	*/
	_remove_slide : function( $slide ) {

		$slide
			.closest( 'li' )
			.remove();
		this.options.slider.flexslider( 'next' );

	//	this._refresh();
		this.options.slider.count--;
		this.edited = true;
	},

	/**
	Edit a caption
	@param {Object} $caption Caption node
	@returns {Deferred}
	*/
	_edit_caption : function( $caption ) {

		var edited = new $.Deferred();

		$caption
			.attr( 'contentEditable', 'true' )
			.on( 'focus mouseover', function(){
				$( this )
					.css( 'border', '1px solid black' );
			})
			.on( 'blur mouseout', function(){
				$( this )
					.removeAttr( 'style' );
			})
			.on( 'blur', function(){
				edited.resolve();
			})
			.on( 'keyup', function( e ){
				e.stopPropagation();
			} );

			if ( $.trim( $caption.text() ) == '' ) {
				$caption.text( 'Default text' );
			}

		return edited;
	},

	/**
	Open a drag-and-drop interface for sorting the slides
	@function
	@returns {Deferred}
	*/
	_sort : function() {

		var self = this;
		var slides = this.getData().slides;
		var edited = new $.Deferred();

		$.boom.log( 'sorting slides' );

		var dialog = $.boom.dialog.open({
			title : 'Sort slides',
			msg: 'hello',
			width: 1000,
			height: 400,
			deferred : edited,
			open : function() {
				var $list = $( '<ul class="sort-list"></ul>');
				var new_slides = [];

				for ( i in slides ) {
					var slide = slides[ i ];
					var $li = $( '<li><img src="/asset/thumb/' + slide.asset_id +'/150/150/85/1" /></li>' )
						.data( 'slide', slide );
					$list
						.append( $li );
				}

				$( this )
				.html(
					$list
						.sortable( {
							helper : 'clone'
						})
				);

				edited.done( function(){
					$list
						.find( 'li' )
						.each( function(){
							var slide = $( this ).data( 'slide' );
							new_slides.push( slide );
						});

					self._refresh( new_slides );
				});
			}
		});
		return edited;
	},

	/**
	Apply new slide data to the slot template
	@function
	@param {Array} slides New slides
	@returns {String} updated HTML
	*/
	_get_preview : function( slides ) {

		var self = this;
		$.boom.loader.show();

		var url =
			this._preview_url() +
			'&editable=1&remove=0';

		var slides = this.getData();
		var request = self._preview( slides )
		.done( function( data ){

			$.boom.loader.hide();

		})
		.fail( function( data ) {
			$.boom.loader.hide();
			$.boom.log( 'slideshow error ' + data );
		});

		return request;
	},

	/**
	Apply changes to the HTML
	*/
	_insert : function(){

		var self = this;
		var data = this.getData();
		
		console.log( data.slides );
		
		if ( data.slides.length == 0 ){
			return self._remove( data );
		} else {
			return this._get_preview( this.getData() )
			.done( function( data ){
				self._apply( data );
			});
		}
	},

	/**
	refresh slides after a sort
	@params {Array} slides New slide set (same length as old set)
	@function
	*/
	_refresh : function( slides ) {

		var self = this;

		self.element
			.find( 'ul.slides li')
			.not( '.clone' )
			.each( function( i, item ){
				var slide = slides[ i ];
				var $item = $( item );
				var src = $item.find( '.slide-asset' ).attr( 'src' ).split( '/' );

				src[3] = slide.asset_id;

				$item.find( '.slide-asset' ).attr( 'src', src.join( '/' ) );
				$item.find( '.slide-link' ).attr( 'href', slide.url );
				$item.find( '.slide-caption' ).text( slide.caption );
			});

		$.boom.log( 'slideshow refresh' );
	},

	/**
	Cancel changes and exit
	@function
	*/
	_cancel : function() {

		var self = this;

		if (self.edited ) {

			$.boom.dialog.confirm(
				'Cancel changes',
				'Cancel changes to this slideshow?'
			)
			.done( function(){

				self.element
					.find( 'ul.slides')
					.find( 'li' )
					.remove()
					.end()
					.append( self.originals );
				self.options.slider.count = self.originals.size();
				self.destroy();
			});

		} else {
			self.destroy();
		}
	},

	/**
	Remove editor UI and exit
	*/
	_destroy : function() {
		$.boom.log( 'exiting slideshow editor' );
		var self = this;

		self._remove_ui();

		$.ui.chunk.prototype._destroy.call( this );

	},

	/**
	Get slideshow slide data.
	@returns {Array}
	*/
	getData: function(){
		var slides = [];

		this.element
			.find( 'ul.slides li' )
			.not( '.clone' )
			.each( function(){
				var slide = {};
				var $this = $( this );

				slide.caption = $this.find( '.slide-caption' ).text();
				slide.caption = ( slide.caption == 'Default text' ) ? '' : slide.caption;
				slide.asset_id = $this.find( '.slide-asset' ).attr( 'src' ).split( '/' )[3];
				slide.url = $this.find( '.slide-link' ).attr( 'href' );

				slides.push( slide );

			});

		return { slides : slides };
	}

});


/**
@namespace
@name ui
*/

/**
@class
@name ui.chunk
*/
$.widget('ui.chunk', {

	/**
	@lends ui.chunk
	*/

	/**
	default config
	@property
	*/
	options: {
		/** URL prefix for ajax calls */
		urlPrefix:  '/cms/chunk'
	},

	_create : function(){
		$.boom.log( 'CHUNK CREATE' );
		var slot = this.options.slot;
		this.options.slot = {
			type : slot[0],
			name : slot[1],
			rid : parseInt( slot[2], 10 ),
			template: slot[3],
			page : slot[4],
			has_content : slot[5]
		};
	},

	_init : function(){
		$.boom.log( 'CHUNK INIT' );

		this.edit();
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_apply: function(replacedata){

		this._update_html( replacedata );

		$.boom.log( 'push changes onto the stack' );
		this._save_slot();

	},

	/**
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

	_save_slot : function() {

		this.options.slot.has_content = 1;

		$.boom.page.slot_edits.push( {
			slot: this.options.slot,
			data: this.getData()
		} );

		$.boom.page.save_button.button( 'enable' ).attr( 'title', 'Save page' );
	},

	/**
	Get the base ajax URL for previewing the slot HTML
	@function
	@returns {String} URL for this slot's HTML
	*/
	_preview_url : function() {
		return this.options.urlPrefix +
		'/' + this.options.slot.type +
		 '/preview/' + $.boom.page.config.id +
		'/?slotname=' + this.options.slot.name +
		'&template=' + this.options.slot.template;
	},

	/**
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
@class
@name chunkText
@extends ui.chunk
@memberOf ui
*/
$.widget('ui.chunkText', $.ui.chunk, {

	/**
	@lends ui.chunkText
	*/

	title : '',

	content : '',

	/**
	Create an instance of TinyMCE on the selected element.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Text chunk slot edit');

		if ( !$.boom.editor.ready() ) {

			$.boom.dialog.alert('Error', 'The page editor has not been fully downloaded yet, please wait a few seconds before trying again.');

			return;
		}

		var $content = this.element.find( '.slot-content');

		var edit_content = function( $element ) {

			$element[0].id = $element[0].id || $.boom.util.dom.uniqueId('boom-dom-wysiwyg-');

			var old_html = self.element.html();
			self._bring_forward();

			$.boom.editor
				.edit( $element )
				.fail( function(){
					self.element.html( old_html ).show();
					self.destroy();
				})
				.done( function( html ){
					self._apply( html );
				})
				.always( function(){
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

		return { title : this.title, text : this.content.cleanup().safeEscape() };
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
	}

});
/**
@class
@name chunkLinkset
@extends ui.chunk
@memberOf ui
*/
$.widget('ui.chunkLinkset', $.ui.chunk, {
	/**
	@lends ui.chunkLinkset
	*/

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
			url: this.options.urlPrefix + '/linkset/edit/' + $.boom.page.config.id,
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

				self.dialog.find('.boom-tabs:first').tabs('select', 1);
			});
		}
	},

	/**
	Bind button and tree control events.
	*/
	_bindEvents : function(){

		var self = this;

		this.elements.internalLinks.tree($.extend({}, this.options.treeConfig, {
			onClick: function(event){
				event.preventDefault();
				var anchor = $(this).clone();

				self._add(anchor);
			}
		}));

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

		this.dialog.find('.boom-tabs:first').tabs('select', 0);

		link.fadeIn(function(){
			$(this).removeAttr('style');
		});
	},

	/**
	Add a new link to the list in the linkset dialog.
	*/
	_insert: function(){

		var
			self = this,
			url =
			this._preview_url();

		var links = this._getData( this.elements.currentLinks );


		//url += '&data=' + JSON.stringify( this.getData() );

		// get the preview chunk here
		var request = $.get(url, { data : links }, function(data) {

			$.boom.loader.hide('dialog');

			self._apply(data);

			//self.destroy();
		});

		return request;
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
	@param {jQuery Object} element Container element for this linkset.
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
@extends ui.chunk
@memberOf ui
*/
$.widget('ui.chunkFeature', $.ui.chunk, {
	/**
	@lends ui.chunkFeature
	*/

	/**
	Open a dialog with a tree control to pick a page for the current feature
	and a button to remove any existing page without replacing it.
	*/
	edit : function(){

		$.boom.log('Feature chunk slot edit');

		var self = this;

		var treeConfig = $.extend({}, $.boom.config.tree, {
			maxHeight: 200,
			toggleSelected: true,
			onClick: function(event){
				console.debug( event.data );
				self.insert( event.data.rid );

				$.boom.dialog.destroy(self.dialog);

				return false;
			}
		});

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/feature/edit/' + $.boom.page.config.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			// cache: true,
			title: 'Page feature',
			treeConfig: treeConfig,
			destroy: function(){
				self.destroy();
			},
			open: function(){

				if ( self.options.slot.rid > 0 ) {

					var button = $('<button />')
					.addClass('ui-helper-left')
					.text('Remove')
					.button()
					.click(function(){

						$.boom.dialog.destroy(self.dialog);

						self.remove();
					});

					$(this).dialog('widget')
						.find('.ui-dialog-buttonpane')
						.prepend( button );
				}
			},
			buttons: {
				Cancel: function(){
					$.boom.dialog.destroy(self.dialog);

					self.destroy();
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

		var url =
			this._preview_url() +
			'&preview_target_rid=' + rid;

		$.get(url, function( data ){

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

		this.insert( 0 );
	}
});

/**
@class
@name chunkAsset
@extends ui.chunk
@memberOf ui
*/
$.widget('ui.chunkAsset', $.ui.chunk, {
	/**
	@lends ui.chunkAsset
	*/

	/**
	Open an asset manager dialog
	*/
	edit: function(){

		$.boom.log('Asset chunk slot edit ' + this.options.slot.rid);

		var self = this;
		var tagmanager = $.boom.assets;
		var asset_selected = new $.Deferred();

		self.asset = {
			asset_id : self.options.slot.rid,
			title : null,
			caption : null,
			url : null
		};

		// cleanup code when the dialog closes.
		asset_selected
		.fail( function() {
			$.boom.log( 'asset chunk cancelled' );
			self.destroy();
		})
		;

		tagmanager.picker( {
			asset_rid : self.options.slot.rid,
			deferred: asset_selected
		} )
		.pipe( function( rid ){

			self.asset.asset_id = rid;
			return $.boom.links.picker( {
				title: 'Add a link'
			});
		})
		.done( function( link ){
			self.insert( self.asset.asset_id, link );
		})
		.fail( function() {
			self.remove();
		})
		.always( function(){
			$.boom.history.load( '' );
		});
	},

	/**
	Insert selected asset into the page
	@param {Int} rid Asset RID
	*/
	insert: function(rid, link) {
		$.boom.log( 'inserting asset' + rid );

		var self = this;
		link = ( link ) ? link : { url : null };
		self.asset.url = link.url;

		$.boom.loader.show();

		var url =
			this._preview_url() +
			'&asset_id=' + rid;

		url += ( link.url && link.url != '' ) ? '&url=' + link.url : '';

		$.get( url )
		.done( function( data ){

			$.boom.loader.hide();

			self._apply( data );
		})
		.fail( function( data ) {
			$.boom.log( 'asset chunk error ' + data );
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
				caption : null,
				url : this.asset.url
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
@extends ui.chunk
@memberOf ui
*/
$.widget('ui.chunkSlideshow', $.ui.chunk, {
	/**
	@lends ui.chunkSlideshow
	*/

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
					'Delete this slide?',
					function(){
						self._remove_slide( $( slide ) );
					}
				);
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
	@param {jQuery object} $slide Slide to edit
	@returns {Deferred}
	*/
	_edit_slide : function( $slide ){
		var self = this;

		$.boom.log( 'edit slide' );
		var url_segments = $slide.attr( 'src' ).split( '/' );
		var rid = url_segments[3];
		var tagmanager = $.boom.assets;
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

		return tagmanager.picker( {
			asset_rid : rid,
			deferred: asset_selected
		} )
		.pipe( function( rid ){

			url_segments[3] = rid;
			$slide.attr( 'src', url_segments.join( '/' ) );
			return $.boom.links.picker( {
				title: 'Add a link'
			});
		})
		.done( function( link ){
			$slide
				.closest( 'a.slide-link' )
				.attr( 'href', link.url );
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
	@param {jQuery object} $li Insert the new slide after this item
	*/
	_insert_slide : function( $li ) {
		var self = this;
		var tagmanager = $.boom.assets;
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
	@param {jQuery object} $slide Slide to remove
	*/
	_remove_slide : function( $slide ) {

		$slide
			.closest( 'li' )
			.remove();

	//	this._refresh();
		this.options.slider.count--;
		this.edited = true;
	},

	/**
	Edit a caption
	@param {jQuery object} $caption Caption node
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
			height: 500,
			deferred : edited,
			open : function() {
				var $list = $( '<ul class="sort-list"></ul>');
				var new_slides = [];

				for ( i in slides ) {
					var slide = slides[ i ];
					var $li = $( '<li><img src="/asset/thumb/' + slide.asset_rid +'/150/150/85/1" /></li>' )
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
		var request = $.post( url, { data : slides } )
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

		var request = this._get_preview( this.getData() )
		.done( function( data ){

			self._apply( data );

		});

		return request;
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

				src[3] = slide.asset_rid;

				$item.find( '.slide-asset' ).attr( 'src', src.join( '/' ) );
				$item.find( '.slide-link' ).attr( 'href', slide.link );
				$item.find( 'slide-caption' ).text( slide.caption );
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
				slide.link = $this.find( '.slide-link' ).attr( 'href' );

				slides.push( slide );

			});

		return { slides : slides };
	}

});


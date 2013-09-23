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
		var slider = top.$(this.element).hasClass('flexslider')? top.$(this.element).data( 'flexslider' ) : top.$( this.element ).find( '.flexslider' ).data( 'flexslider' );
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

				if ( ! $('a.slide-link').length) {
					$('button.link').hide();
				}
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
				self._remove_ui();

				if (self.edited) {
					self.insert();
				} else {
					$.boom.page.editor.bind();
				}
			})
			.on( 'click', 'button.sort', function(){
				self._sort()
					.done( function(){
						self.edited = true;
						$.boom.log( 'sort finished' );
					});
			})
			.on( 'click', 'button.link', function(){
				var slideshow = self.options.slider;
				var slide = slideshow.slides[ slideshow.currentSlide ];

				self._edit_link($(slide))
					.done( function(){
						self.edited = true;
						$.boom.growl.show('Link updated');
					});
			})
			.on('click', 'button.prev', function(event) {
				var slider = self.options.slider;

				slider.flexAnimate(self.options.slider.getTarget('prev'));
			})
			.on('click', 'button.next', function(event) {
				var slider = self.options.slider;

				slider.flexAnimate(self.options.slider.getTarget('next'));
			});
	},

	_edit_link : function($slide){
		var self = this;

		$.boom.log( 'edit link' );

		var link = $slide.find( 'a.slide-link' );

		return $.boom.links.picker( {
			title: 'Add a link',
			new_link : {
				url : link.attr( 'href' ),
				rid : link.attr( 'rel' ),
			}

		})
		.pipe(function(new_link) {
			link.attr( 'href', new_link.url );
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
			if ($slide.attr( 'src' ).substring(0, 6) == '/asset') {
				url_segments[3] = rid;
				$slide.attr( 'src', url_segments.join( '/' ) );
			} else {
				$slide.attr( 'src', '/asset/view/' + rid);
			}
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

		$new_slide.find('.slide-asset').attr('src', '');
		$new_slide.find('.slide-caption').html('');

		return self._edit_slide( $new_slide.find( '.slide-asset' ) )
			.done( function() {

				var slides = $.makeArray( self.element.find( 'ul.slides li').not( '.clone' ) );
				var target = $.inArray( $new_slide[0], slides );

				self.options.slider.count++;
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
	Apply changes to the HTML
	*/
	insert : function(){
		var data = this.getData();

		if (data.slides.length == 0) {
			return this.remove();
		} else {
			return this._save(data);
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
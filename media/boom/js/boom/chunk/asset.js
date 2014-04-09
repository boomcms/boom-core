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
	_edit : function() {
		var self = this;

		self.elements = this._get_elements();
		self.asset = this._get_asset_details();

		this.originals = this.element.children().clone(true);

		if (self.elements.caption.length || self.elements.link.length || self.elements.title.length) {
			this._build_ui();
		} else {
			self._edit_asset(self.elements.asset);
		}
	},

	/**
	@function
	*/
	_build_ui : function() {
		var self = this;
		this._bring_forward();

		return $.get('/cms/toolbar/asset')
			.done( function( toolbar ){
				$.boom.page.toolbar.hide();
				top.$( 'body' )
					.prepend( toolbar );

				top.$('.b-toolbar-asset')
					.on( 'click', '.b-cancel', function(){
						self._cancel();
					})
					.on( 'click', '.b-accept', function(){
						self.insert();
						self.destroy();
					})
					.on( 'click', '.b-link', function() {
						self._edit_link();
					})
					.on( 'click', '.b-asset', function() {
						self._edit_asset(self.elements.asset);
					});
			});

	},

	/**
	@function
	*/
	_remove_ui : function() {

		this._send_back();

		$.boom.page.toolbar.show();

		this.elements
			.caption
			.removeAttr( 'contentEditable style' )
			.end()
			.off( 'focus mouseover' );

		top.$( 'body' )
			.find('.b-toolbar-asset')
			.remove();
	},

	/**
	Edit the asset
	@param {Object} $caption Caption node
	@returns {Deferred}
	*/
	_edit_asset : function() {

		var self = this, asset_selected = new $.Deferred();

		asset_selected
			.fail(function() {
				$.boom.log('asset chunk cancelled');
				self.destroy();
			});

		return $.boom.assets.picker({
			asset_rid : self.asset.asset_id,
			deferred: asset_selected
		})
		.pipe(function(rid) {
			self.asset.asset_id = rid;
		})
		.done(function() {
			$.boom.page.toolbar.minimise();
			self.insert();
			self.destroy();
		})
		.fail( function() {
			self.remove();
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
	Edit a title
	*/
	_edit_title : function($title) {
		var edited = new $.Deferred();

		$title
			.attr('contentEditable', 'true')
			.on('focus mouseover', function(){
				$(this).css('border', '1px solid black');
			})
			.on('blur mouseout', function() {
				$(this).removeAttr( 'style' );
			})
			.on('blur', function() {
				edited.resolve();
			})
			.on('keyup click', function(e) {
				e.stopPropagation();
				e.preventDefault();
			});

		return edited;
	},

	/**
	 @function
	 */
	 _get_elements: function() {
		var asset_id = this.element.attr('data-boom-target');
		var elements = {};

		var img = this.element.find('img');
		var a = this.element.find('a');

		var regExp = new RegExp("asset\/(thumb|view|download)\/" + asset_id);

		elements.asset = this.element.find('.asset-target');
		elements.link = this.element.find('.asset-link');
		elements.caption = this.element.find('.asset-caption');
		elements.title = this.element.find('.asset-title');

		if (! elements.asset.length) {
			if (img.length && regExp.test(img.attr('src'))) {
				elements.asset = img;
			}
			else if (a.length && regExp.test(a.attr('href'))) {
				elements.asset = a;
			}

			if ( ! elements.asset.length) {
				elements.asset = this.element;
			}
		}

		if ( ! elements.link.length && a.length && elements.asset != a && a.attr('href') && a.attr('href') != '#' && ! regExp.test(a.attr('href'))) {
			elements.link = a;
		}

		return elements;
	 },

	/**
	@function
	*/
	_get_asset_details: function(){
		var asset = {
			asset_id : this.element.attr('data-boom-target'),
			title : this.elements.title.text(),
			caption : this.elements.caption.text(),
			url : this.elements.link.attr('href')
		};

		return asset;
	},

	/**
	Cancel changes and exit
	@function
	*/
	_cancel : function() {

		var self = this;

		if (self.edited) {
			var confirmation = new boomConfirmation('Cancel changes', 'Cancel changes to this asset?');
			confirmation.done(function() {
				self.element
					.children()
					.remove()
					.end()
					.append(self.originals);
				self.destroy();
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

		this._edit();

		$.boom.log('Asset chunk slot edit ' + self.asset.asset_id);

		if (self.elements.caption.length) {
			self.elements.caption.each( function() {
				self
					._edit_caption( $( this ) )
					.done( function(){
						self.asset.caption = self.element.find( '.asset-caption' ).text();
						self.edited = true;
					});
			});
		}

		if (self.elements.title.length) {
			self.elements.title.each(function() {
				self
					._edit_title($(this))
					.done(function() {
						self.asset.title = self.element.find('.asset-title').text();
						self.edited = true;
					});
			});
		}

		if (this.element != self.elements.asset) {
			self.elements.asset
				.on( 'click', function(event) {
					event.preventDefault();

					self._edit_asset(self.elements.asset);
					return false;
				});
		}

		if (self.elements.link.length) {
			self.elements.link
				.on( 'click', function(event) {
					event.preventDefault();

					self._edit_link();
					return false;
				});
		}

		this.element.on('click', function(event) {
			event.preventDefault();

			self._edit_asset(self.elements.asset);
			return false;
		});
	},

	_edit_link: function() {
		var self = this;

		return new boomLinkPiclker('Add a link', {
			url: (self.asset.url)? self.asset.url : '',
			rid: -1,
			title: ''
		})
		.pipe(function(new_link) {
			self.asset.url = new_link.url;
		})
		.done(function() {
			self.insert();
		});
	},

	/**
	Insert selected asset into the page
	*/
	insert : function() {
		var self = this;

		$.boom.log( 'inserting asset' + self.asset.asset_id );

		return self._save();
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
			title : this.asset.title,
			caption : this.asset.caption,
			url : this.asset.url
		};
	},

	destroy : function() {
		this._remove_ui();

		$.ui.chunk.prototype.destroy.call(this);
	},

	remove : function() {
		this.destroy();

		$.ui.chunk.prototype.remove.call(this);

		$.boom.page.toolbar.minimise();
	}
});
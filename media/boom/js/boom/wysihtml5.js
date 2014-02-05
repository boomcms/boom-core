/**
@fileOverview Boom interface for wysihtml5.
*/
/**
* Interface for the wysihtml5 editor.
* @class
* @name $.wysihtml5.editor
* @extends $.boom.editor
*/
$.widget('wysihtml5.editor', $.boom.textEditor,
	/** @lends $.wysihtml5.editor */
	{

	/** @property base_url
	@type string
	@default '/media/boom/js/xing'
	*/
	base_url: '/media/boom/js/xing',
	/** @property path
	@type string
	@default '/wysihtml5-0.3.0.js'
	*/
	path: '/wysihtml5x-0.4.2-toolbar.js',

	/**
	@property parser_rules
	@type string
	*/
	parser_rules : '/parser_rules/hoop.js',

	/**
	@property mode
	@type string
	*/
	mode : 'block',

	/**
	@property options
	@type object
	*/
	options : {
	},

	/**
	* @function
	@returns {Deferred}
	*/
	load : function() {
		var self = this;

		var editor_loaded = this._super();

		if (!top.wysihtml5) {

			$(document)
				.ready(function(){


					$.boom.log('wysihtml5 loading');

					top.$.getScript( self.base_url + self.parser_rules )
					.pipe( function(){
						return top.$.getScript( self.base_url + self.path );
					})
					.done( function(response, textStatus){

						editor_loaded.resolve();

						self._load_extensions( top.wysihtml5 );

						$.boom.log('wysihtml5 loaded');
					});


				});
		}

		return editor_loaded;
	},

	/**
	* @function
	@returns {boolean}
	*/
	ready : function() {
		return (top.wysihtml5)? true : false;
	},

	/**
	* @function
	@param {Object} element The element being edited.
	@returns {Deferred}
	*/
	edit : function ( element ){

		var self = this;
		var element;

		self.mode = element.is('div') ? 'block' : 'inline';
		self.mode = (element.is(':header') ||  element.is('.standFirst'))? 'text' : self.mode;
		self.edited = new $.Deferred();
		self.original_html = element.html();

		self._insert_toolbar(element)
			.done(function() {
				$.boom.page.toolbar.hide();

				self.instance = new top.wysihtml5.Editor(element[0], { // id of textarea element
					toolbar:      "wysihtml5-toolbar", // id of toolbar element
					style: true,
					parserRules:  top.wysihtml5ParserRules, // defined in parser rules set
					useLineBreaks : false,
					contentEditableMode : true,
				});

				top.$('#wysihtml5-toolbar')
					.on('click', '#b-editor-link', function(e) {
						e.preventDefault();
						var href = top.$( '[data-wysihtml5-dialog-field=href]' ).val();
						var match = href.match( /asset\/(thumb|view|get_asset)\/([0-9]+)/ );
						var asset_id = match ? match[2] : 0;
						if (asset_id == 0) {
							self._edit_link();
						} else {
							self._edit_asset(asset_id);
							resizeIframe();
						}
					})
					.on('click', '#b-editor-asset', function(e) {
						e.preventDefault();
						var src = top.$( '[data-wysihtml5-dialog-field=src]' ).val();
						var asset_id = 0;
						if ( src && src != 'http://' ) {
							var match = src.match( /asset\/(thumb|view|get_asset)\/([0-9]+)/ );

							asset_id = match ? match[2] : 0;
						}

						self._edit_asset(asset_id);
					})
					.on('click', '#b-editor-accept', function(event) {
						event.preventDefault();
						self.apply(element);
						return false;
					})
					.on( 'click', '#b-editor-cancel', function( event ){
						event.preventDefault();
						self.cancel(element);
						return false;
					});

				self.instance.on( 'show:dialog', function( options ){
					switch( options.command ) {
						case 'createLink' :
							var href = top.$( '[data-wysihtml5-dialog-field=href]' ).val();

							if ( ! href || href == 'http://') {
								self._edit_link();
							}
							break;
						case 'insertImage' :
							var src = top.$( '[data-wysihtml5-dialog-field=src]' ).val();
							var asset_id = 0;
							if ( src && src != 'http://' ) {
								var match = src.match( /asset\/(thumb|view|get_asset)\/([0-9]+)/ );

								asset_id = match ? match[2] : 0;
							}

							if ( ! asset_id) {
								self._edit_asset(asset_id);
							}
							break;
					}
				});
			});

		return self.edited;

	},

	/**
	* @function
	*/
	remove : function() {
		$.boom.page.toolbar.show();
		top.$('#wysihtml5-toolbar').remove();

		this.instance = null;
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	apply : function(element) {
		this.element.blur();
		this.edited.resolve(element.html());

		this.remove();
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	cancel : function(element) {
		var self = this,
			content = element.html();

		if (content != self.original_html) {
			$.boom.dialog.confirm('Cancel changes', 'Cancel all changes and exit the editor?')
				.done(function() {
					$.boom.log( 'canceling text edits' );

					self.remove(element);

					self.edited.reject();
				});
		} else {
			self.remove(element);
			self.edited.reject();
		}
	},

	/**
	@function
	@param {Object} element The element being edited.
	@returns {Deferred}
	*/
	_insert_toolbar : function(element) {
		var self = this;

		 return $.get('/media/boom/toolbars/text.php?mode=' + self.mode)
			.done( function(response) {
				top.$('body').prepend(response)
			});
	},

	/**
	@function
	@returns {Deferred}
	*/
	_edit_asset : function( asset_rid ) {

		var self = this;
		var ed = self.instance.composer;
		var asset_selected = new $.Deferred();

		var img;
		if ( asset_rid == 0 ) {
			ed.commands.exec( "insertHTML", '<img src="url">' );
			img = top.$( ed.element ).find( '[src=url]' );
			if ( ! img.length)
				img = top.$( ed.element ).find( '[href=url]' );
		} else {
			img = top.$( ed.element ).find( '[src^="/asset/view/' + asset_rid +'"]' );
			if ( ! img.length)
				img = top.$( ed.element ).find( '[href^="/asset/view/' + asset_rid +'"]' );
		}

		// cleanup code when the dialog closes.
		asset_selected
		.fail( function() {
			top.$( ed.element ).find( '[src=url]' ).remove() || top.$( ed.element ).find( '[href=url]' ).remove();
		});

		return $.boom.assets
			.picker({
				asset_rid : asset_rid,
				deferred : asset_selected
			})
			.done( function( rid ) {
				if ( rid > 0 ) {
					$.boom.page.toolbar.minimise();

					$.post( '/asset/embed/' + rid )
					.done( function( response ){
						img.replaceWith(response);
						$(response).on('load', function() {
							self.instance.composer.editableArea.style.height = self.instance.composer.element.scrollHeight + "px";
						});
					})
					.always( function(){
						asset_selected.reject();
					});
				}

			})
			.fail( function(){
				img.remove();
			});
	},

	/**
	@function
	@returns {Deferred}
	*/
	_edit_link : function() {
		var self = this;
		var ed = self.instance.composer;
		var existing_link = ed.commands.state( "createLink" )[0];
		var opts = {};
		var bm = ed.selection.getBookmark();

		if ( existing_link ) {
			var link = {
				url : existing_link.href,
				rid : existing_link.rel,
				title : ( existing_link.textContent || existinglink.innerText )
			};

			opts.link = link;
		}


		 return $.boom.links
			.picker( opts )
			.fail( function(){
				var link = top.$( ed.element ).find( '[rel=new-link]' );

				link
					.after( link.text() )
					.remove();

			})
			.done( function( link ){

				var uri = link.url;
				var page_id = link.page_id;

				if (page_id) {
					uri = $('<a/>').attr('href',uri)[0].pathname.replace(/^[^\/]/,'/');
				}

				if ( existing_link ) {
					existing_link.textContent = existing_link.textContent.replace(existing_link.href, uri);

					top.$(existing_link)
						.attr('href', uri)
						.attr('title', '')
						.attr('rel', page_id);
				} else {
					ed.selection.setBookmark(bm);

					if (page_id) {
						ed.commands.exec("createLink", { href: uri, rel: page_id, title: '', text: link.title});
					} else {
						var text = uri.replace('mailto:', '').replace('tel:', '');

						ed.commands.exec("createLink", { href: uri, title: '', text: text});
					}
				}

			});

	},

	/**
	@function
	@param {Object} wh5 Instance of wysihtml5
	*/
	_load_extensions : function( wh5 ) {

		/** blockquote command */
		(function(wysihtml5) {
			var undef;

			/**
			Indent command for blockquotes
			@static
			@class
			**/
			wysihtml5.commands.indent = {
				exec: function(composer, command) {

					command = this.state( composer ) ? 'outdent' : 'indent';

					/*
					TODO: what happens if a browser doesn't support indent/outdent?
					*/
					if (composer.commands.support(command)) {
						composer.doc.execCommand(command, false, null);

						/* clean inline styles left by webkit */
						var blockquote = this.state( composer );
						if ( blockquote ) {
							blockquote.removeAttribute( 'class' );
							blockquote.removeAttribute( 'style' );
						}
						return;
					}
				},

				state: function(composer) {
					var selectedNode = composer.selection.getSelectedNode();
					return wysihtml5.dom.getParentElement(selectedNode, { nodeName: "BLOCKQUOTE" });
				},

				value: function() {
					return undef;
				}
			};
		})( wh5 );
	}
});
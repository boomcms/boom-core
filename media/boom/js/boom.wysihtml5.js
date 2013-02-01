$.extend($.boom, {
	
	/**
	* @class
	* @name $.boom.editor
	*/
	editor : {
		/** @lends $.boom.editor */
		
		/** @property 
		@type string
		@default '/boom/js/tiny_mce'
		*/
		base_url: '/media/boom/js/xing',
		/** @property 
		@type string
		@default 'tiny_mce_src.js'
		*/
		path: '/wysihtml5-0.3.0.js',
		
		/**
		@property
		@type string
		*/
		parser_rules : '/parser_rules/hoop.js',
		
		/**
		@property
		@type string
		*/
		mode : 'block',
		
		/** 
		@property
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

			var editor_loaded = new $.Deferred();

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
			
			if (!top.wysihtml5) {


				return false;
			}
			
			return true;
		},
		
		/**
		* @function
		@param {jQuery object} element The element being edited.
		@returns {Deferred}
		*/
		edit : function ( element ){
			
			var self = this;
			
			self.mode = element.is( 'div' ) ? 'block' : 'inline';
			self.mode = element.is( ':header' ) ? 'text' : self.mode;
			self.edited = new $.Deferred();
			
			self._insert_toolbar( element )
				.done( function(){
					
					$.boom.page.toolbar.hide();
					
					if ( self.mode == 'text' || self.mode == 'inline' ) {
						
						self.original_html = element.text();
						
						self.inline_editor.init( element );
					} else {
						
						self._insert_textarea( element );
						
						self.instance = new top.wysihtml5.Editor('b-wh5', { // id of textarea element
							toolbar:      "wysihtml5-toolbar", // id of toolbar element
							style: true,
							parserRules:  top.wysihtml5ParserRules // defined in parser rules set 
						});

						self.instance.on( 'load', function(){

							var frame =  self.instance.composer.iframe;
							$( frame )
								.css( 'border', '1px solid #333')
								.contents()
								.find( 'head' )
								.append( '<link rel="stylesheet" href="/media/site/css/main.css" />' );

							

							resizeIframe();

							self.original_html = self.get_content();
							
							var el = self.instance.composer.element;
							
							if (el.addEventListener) {
							  	el.addEventListener("blur", resizeIframe, false);
								el.addEventListener("focus", resizeIframe, false);
								el.addEventListener("keyup", resizeIframe, false);
							} else if (el.attachEvent)  {
								el.attachEvent("onblur", resizeIframe);
								el.attachEvent("onfocus", resizeIframe);
								el.attachEvent("onkeyup", resizeIframe);
							}

						});

						self.instance.on( 'show:dialog', function( options ){

							switch( options.command ) {
								case 'createLink' :
									var href = top.$( '[data-wysihtml5-dialog-field=href]' ).val();
								 	var match = href.match( /asset\/(thumb|view|get_asset)\/([0-9]+)/ );
									var asset_id = match ? match[2] : 0;
									if ( asset_id == 0 ) self._edit_link();
									break;
								case 'insertImage' :
									var src = top.$( '[data-wysihtml5-dialog-field=src]' ).val();
									var asset_id = 0;
									if ( src && src != 'http://' ) {
										var match = src.match( /asset\/(thumb|view|get_asset)\/([0-9]+)/ );
										
										asset_id = match ? match[2] : 0;
									}
									self._edit_asset( asset_id );
									break;
							}
						});

						var resizeIframe = function() {
						    self.instance.composer.iframe.style.height = self.instance.composer.element.scrollHeight + "px";
						};

						self.instance.on( 'paste', resizeIframe );
					}
				});
			
			return self.edited;
			
		},

		/**
		* @function
		@returns {String} HTML content of the editor
		*/
		get_content : function(){
			
			return top.$( '#b-wh5' ).val();
		},

		/**
		* @function
		@param {jQuery object} element The element being edited.
		*/
		remove : function( element ){

			$.boom.page.toolbar.show();
			top.$( '#b-wh5, #wysihtml5-toolbar, iframe.wysihtml5-sandbox' ).remove();
			element.removeAttr( 'contenteditable' );
			self.instance = null;
		},
		
		/**
		* @function
		@param {jQuery object} element The element being edited.
		*/
		apply : function( element ){
			
			var self = this;
			
			self.edited.resolve( self.get_content() );

			self.remove( element );

		},
		
		/**
		* @function
		@param {jQuery object} element The element being edited.
		*/
		cancel : function( element ){
			
			var self = this;
			
			if ( self.mode == 'text' || self.mode == 'inline' ) {
				var content = element.text();
			} else {
				var content = self.get_content();
			}

			if ( content != self.original_html ) {

				$.boom.dialog.confirm(
					'Cancel changes',
					'Cancel all changes and exit the editor?',
					function(){

						$.boom.log( 'canceling text edits' );

						self.remove( element );

						self.edited.reject();
					}
				);

			} else {

				self.remove( element );
				self.edited.reject();
			}

		},
		
		/**
		@function
		@param {jQuery object} element The element being edited.
		@returns {Deferred}
		*/
		_insert_toolbar : function( element ) {
			
			var self = this;
			
			
			 return $.get( '/media/boom/toolbars/text.php?mode=' + self.mode )
			.done( function( response ) {
				top.$('body').prepend( response );
				
				top.$( '#b-editor-accept').on( 'click', function( event ){

					event.preventDefault();
					self.apply( element );
					return false;
				});
				
				top.$( '#b-editor-cancel').on( 'click', function( event ){

					event.preventDefault();
					self.cancel( element );
					return false;
				});
			});
			
			
		},
		
		/**
		@function
		@param {jQuery object} element The element being edited.
		*/
		_insert_textarea : function( element ) {
			var original_html = element.html();
			element
				.html( '<textarea id="b-wh5" style="border: 1px solid #000; overflow: hidden; width: 100%; height: ' + element.innerHeight() + 'px;">' + original_html + '</textarea>');
		},
		
		/**
		@function
		@returns {Deferred}
		*/
		_edit_asset : function( asset_rid ) {
			
			var self = this;
			var ed = self.instance.composer;
			
			 return $.boom.assets
				.picker({
					asset_rid : asset_rid
				})
				.done( function( rid ){
					
					$.post( '/asset/embed/' + rid )
					.done( function( response ) {
						ed.commands.exec( "insertHTML", response );
					});
					
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
			
			if ( !existing_link ) {
				ed.commands.exec("createLink", { href: '', rel: 'new-link'});
				
				existing_link = top.$( ed.element ).find( '[rel=new-link]' );
			}
			
			
			 return $.boom.links
				.picker({})
				.done( function( link ){
					
					var uri = link.url;
					var page_rid = link.rid;
					
					if ( existing_link ) {
						top.$( existing_link )
							.attr( 'href', uri )
							.attr( 'rel', page_rid );
					} else {
						if ( top.getSelection ) {
							console.log( selection );
							new_selection = top.getSelection( selection );
						} else if ( top.document.selection ) {
							new_selection = top.document.selection.createRange();
							console.log( selection );
							new_selection = new_selection.moveToBookmark( selection );
						} else {
							new_selection = top.rangy.getSelection();
						}
						
						ed.commands.exec("createLink", { href: uri, rel: page_rid});
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
		},
	
		/**
		* @class
		* @name $.boom.editor.inline_editor
		*/
		inline_editor : {
			/** @lends $.boom.editor.inline_editor */
			
			/** @property */
			rangy : null,
			
			/** @property */
			toolbar : null,
			
			/** @property */
			selected_node : null,
			
			/** @function */
			init : function( element ) {
				
				var self = this;
				this.toolbar = top.$( '#wysihtml5-toolbar');
				this.rangy = top.rangy;
				
				element
					.attr( 'contenteditable', 'true' )
					.on( 'click', 'a, b, strong, i, em', function( event ){
						event.preventDefault();
						event.stopPropagation();
						self.highlight_command( this );
					} )
					.on( 'click', function( event ){
						event.preventDefault();
						event.stopPropagation();
						top.$( 'button[data-wysihtml5-command]' )
							.removeClass( 'wysihtml5-command-active' );
					} )
					.on( 'keydown', function( event ){
						switch( event.which ) {
							
							case 13:
								top.document.execCommand( 'insertHTML', null, '<br>' );

								return false;
							break;
						}
					});
				self.toolbar
					.on( 'mousedown', 'button[data-wysihtml5-command]', function(){
						
						var command = $( this ).attr( 'data-wysihtml5-command' );
						var button = $( this );
						
						switch( command ) {
							case 'createLink':
								var existing_link = self.selected_node;
								$.boom.links
									.picker({})
									.done( function( link ){

										var command = self.insert_link( link );
										
										switch( command ) {
											case 'unlink':
												button.removeClass( 'wysihtml5-command-active' );
											break;
											case 'createLink':
												button.addClass( 'wysihtml5-command-active' );
											break;
										}

									});
							break;
							
							case 'insertImage':
								var asset_rid = 0;
								$.boom.assets
									.picker({
										asset_rid : asset_rid
									})
									.done( function( rid ){

										$.post( '/asset/embed/' + rid )
										.done( function( response ) {
											top.document.execCommand( "insertHTML", null, response );
										});

									});
							break;
							
							default:
								top.document.execCommand( command, null, null );
								$( this ).toggleClass( 'wysihtml5-command-active' );
						}
						
					});
			},
			
			/** @function */
			select_node : function() {
				// expand the selection range to the clicked node.
				var selection;
				if ( top.getSelection ) {
					selection = top.getSelection();
				} else if ( top.document.selection ) {
					selection = top.document.selection.createRange();
				} else {
					selection = top.rangy.getSelection();
				}
				
				if ( selection.anchorNode ) {
					this.selected_node = selection.anchorNode.parentNode;
					selection.selectAllChildren( this.selected_node );
				} else {
					this.selected_node = selection.parentElement();
					selection.parentElement().focus();
				}
				
			},
			
			/** @function */
			highlight_command : function( node ) {
				
				var command = '';
				
				switch ( node.nodeName ){
					case 'A':
						command = 'createLink';
					break;
					case 'I': case 'EM':
						command = 'italic';
					break;
					case 'B': case 'STRONG':
						command = 'bold';
					break;
				}
				
				this.select_node();
				top.$( 'button[data-wysihtml5-command=' + command + ']' )
					.addClass( 'wysihtml5-command-active' );
					
			},
			
			/** @function */
			insert_link : function( link ) {
				
				var url = link.url;
				var page_rid = link.rid;
				var existing_link = self.selected_node;
				console.log( link.url );
				
				command = ( url == 'http://' ) ? 'unlink' : 'createLink';
				
				if (document.selection ) {
					document.selection.createRange( self.selected_node );
				}

				if ( existing_link && existing_link.nodeName == 'A' ) {
					top.$( existing_link )
						.attr( 'href', uri )
						.attr( 'rel', page_rid );
				} else {
					console.log( command );
					top.document.execCommand( command, null, url );
				}
				
				return command;
			}
		}
	}
});

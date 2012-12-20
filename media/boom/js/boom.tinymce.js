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
		base_url: '/media/boom/js/tiny_mce',
		/** @property 
		@type string
		@default 'tiny_mce_src.js'
		*/
		path: '/jquery.tinymce.js',
		/** @property 
		@type DOM node
		*/
		element: {},
		
		/**
		@property
		@type boolean
		*/
		loaded: false,
		
		/**
		@property
		@type boolean
		*/
		inline: false,
		
		/** 
		@property
		@type object
		*/
		options : {
			script_url : '/media/boom/js/tiny_mce/tiny_mce.js', //jquery option
			theme: 'advanced',
			skin: 'o2k7',
			skin_variant : '',
			language: 'en',
			plugins: 'accept,cancel,paste,jqueryinlinepopups,boomasset,boomlink',
			theme_advanced_buttons1: 'accept,cancel,bold,italic,|,bullist,numlist,|,formatselect,|,image,|,link,unlink,|,removeformat',
			theme_advanced_buttons2: '',
			theme_advanced_buttons3: '',
			theme_advanced_path_location: false,
			theme_advanced_resizing: false, 
			theme_advanced_blockformats: "p,h2,h3,blockquote",
			theme_advanced_toolbar_location: "top",
			theme_advanced_toolbar_align: "center",
			apply_source_formatting: true, 
			force_hex_style_colors: true, 
			doctype: '<!DOCTYPE html>',
			paste_text_use_dialog: true,
			paste_remove_styles_if_webkit: false,
			element_format: 'html',
			object_resizing: false,
			valid_children: 'h2'
		},
		
		/**
		* @function
		*/
		load : function() {
			
			var self = this;

			var editor_loaded = new $.Deferred();

			if (!top.tinyMCE) {

				top.tinyMCEPreInit = {
					base: self.base_url,
					suffix: '',
					query: ''
				};

				$(document)
					.ready(function(){


						$.boom.log('tinyMCE loading');

						top.$.getScript( self.base_url + self.path)
						.done( function(response, textStatus){

							//if (textStatus != 'success' || !top.tinymce) return;
							self.loaded = true;

							//tinymce.dom.Event.domLoaded = true;
							editor_loaded.resolve();

							$.boom.log('tinyMCE loaded');
						});


					});
			}

			return editor_loaded;
		},
		
		/**
		* @function
		*/
		ready : function() {
			
			if ( !this.loaded ) {


				return false;
			}
			
			return true;
		},
		
		/**
		* @function
		*/
		edit : function ( element ){
			console.log( 'opening wysiwyg' );
			console.log( element );
			
			var self = this;
			this.element = element;
			self.edited = new $.Deferred();
			
			var elemConfig = {
				heading: {
					valid_elements: 'span,b,strong,i,em,a',
					forced_root_block: false,
					theme_advanced_buttons1: 'accept,cancel,bold,italic,|,link,unlink'
				},
				div: {
					valid_elements: 'span,b,strong,i,em,img[!src|border:0|alt|title|width|height|style|id],a[name|href|target|title|onclick],object,p,h2,blockquote,ul,ol,li',
					forced_root_block: 'div',
					theme_advanced_buttons1: self.options.theme_advanced_buttons1
				}
			};

			switch ( element[0].nodeName ) {
				case 'H1' :
				case 'H2' : self.inline = true; elemConfig = elemConfig.heading; break;
				case 'H3' : self.inline = true; elemConfig = elemConfig.heading; break;
				case 'DIV' : self.inline = false; elemConfig = elemConfig.div; break;
				default: self.inline = true; elemConfig = elemConfig.heading;
			}

			var editelem = element.clone( true, true );

			editelem[0].className = '';
			editelem[0].id = '';

			element.data('clone', editelem);

			var config = $.extend({}, self.options, {
				valid_elements: elemConfig.valid_elements,
				window: window.parent,
				forced_root_block: elemConfig.forced_root_block,
				theme_advanced_buttons1: elemConfig.theme_advanced_buttons1,
				element_id: element[0].id,
				auto_focus: element[0].id,
				height: 0,
				setup: function(ed) {

					ed.onLoadContent.add(self.onLoadContent);
					ed.onPaste.add(self.onPaste);
					ed.onKeyDown.add(self.onKeydown);
					ed.onSetContent.add( function( ed, o ){
						$( ed.contentDocument )
							.contents()
							.find( 'body' )
							.html( editelem );
					})
					//self.tinyMCEAutoResize(ed, config.window);
				}
			});

			// if ($.boom.page.editor.options.toolbar.length) {
			// 
			// 				$.boom.page.editor.options.toolbar.unshift('acceptimage');
			// 
			// 				config.theme_advanced_buttons1 = this.options.toolbar.join(',');
			// 			}

			top.$(this.element).tinymce(config);

			//tinyMCE.execCommand('mceAddFrameControl', false, config);
			return self.edited;
			
		},
		
		/**
		* @function
		*/
		onLoadContent : function(ed, cmd, ui, val){

			$.boom.log('TinyMCE content loaded');
			
			console.log( top.tinyMCE );
			// hacking tinyMCE across documents.
			tinyMCE = top.tinyMCE;
			tinymce = top.tinymce;

			var frame =  $.boom.page.editor.elements.page_body.contents().find( '#'+ ed.id +"_ifr" );
			frame
				.contents()
				.find( 'head' )
				.append( '<link rel="stylesheet" href="/media/site/css/main.css" />' );
		},

		/**
		* @function
		*/
		onPaste : function(ed, e){

			$.boom.log('TinyMCE pasted');

		},

		/**
		* @function
		*/
		onKeydown : function(ed, e){

			$.boom.log('TinyMCE keydown');

			return false;
		},

		/**
		* @function
		*/
		get_content : function(){

			return top.$( this.element ).tinymce().getContent( {format: 'raw'} )
		},
		
		/**
		Remove the toolbar without applying changes.
		*/
		cancel : function(){

			var self = this;

			var old_html = self.element.html();

			if ( $( self.get_content() ).html() != old_html ) {

				$.boom.dialog.confirm(
					'Cancel changes',
					'Cancel all changes and exit the editor?',
					function(){

						$.boom.log( 'canceling text edits' );

						self.remove();

						self.edited.reject();
					}
				);

			} else {

				self.remove();
				self.edited.reject();
			}

		},

		/**
		* @function
		*/
		remove : function(){
			var self = this;

			tinyMCE.execCommand('mceRemoveControl', false, self.element[0].id);
		},
		
		/**
		* @function
		*/
		apply : function(){
			
			var self = this;
			var replacedata = self.get_content();

			self.remove();
			
			var new_content = ( self.inline ) ? $( replacedata ).html() : replacedata;
			self.edited.resolve( new_content );

		}
	}
});
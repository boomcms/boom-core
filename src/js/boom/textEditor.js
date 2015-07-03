/**
@fileOverview Boom interface for wysihtml5.
*/
/**
* Interface for the wysihtml5 editor.
* @class
* @name $.boom.textEditor
*/
$.widget('boom.textEditor', {
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
	_create : function () {
		var self = this,
			element = this.element;

		self.mode = element.is('div') ? 'block' : 'inline';
		self.mode = (element.is(':header') ||  element.is('.standFirst') || element.is('.standfirst'))? 'text' : self.mode;
		self.original_html = element.html();

		self.toolbar = $('#wysihtml5-toolbar').find('[data-buttonset=' + self.mode  + ']').first().clone(true, true).appendTo('#wysihtml5-toolbar');

		if (self.mode === 'block') {
			self.instance = new wysihtml5.Editor(element[0], { // id of textarea element
				toolbar : self.toolbar[0],
				style : true,
				parserRules :  (self.mode == 'block')? wysihtml5ParserRules : wysihtml5ParserRulesInline, // defined in parser rules set
				useLineBreaks : false,
				contentEditableMode : true,
				autoLink : false,
				uneditableContainerClassname : 'b-asset-embed',
				handleTables: true
			});
		} else {
			element
				.attr('contenteditable', true)
				.on('keydown', function(event) {
					switch(event.which) {
						case 13:
							event.preventDefault();
						break;
					}
				});

			element[0].onpaste = function(e) {
				var html = e.clipboardData.getData('text'),
					text = $('<div>' + html + '</div>').text().replace(/\n|\r|\n\r/g, '');

				e.preventDefault();
				element.text(text);
			};
		}

		element
			.on('focus', function() {
				if ( ! self.toolbar.is(':visible')) {
					self.showToolbar();
				}
			});

		this.enableAutoSave();

		if (self.mode === 'block') {
			$(self.instance.composer)
				.on('before:boomdialog', function() {
					self.disableAutoSave();
				})
				.on('after:boomdialog', function() {
					self.element.focus();
					self.enableAutoSave();
				});

			self.instance
				.on('show:dialog', function(options) {
					if (options.command == 'createBoomLink') {
						if ( ! wysihtml5.commands.createBoomLink.state(self.instance.composer)) {
							wysihtml5.commands.createBoomLink.exec(self.instance.composer);
						}

						self.toolbar.find('[data-wysihtml5-command=createBoomLink]').removeClass('wysihtml5-command-dialog-opened');
					}
				})
				.on('tableselect:composer', function(e) {
					$.boom.page.toolbar.element.width('160px');
					self.toolbar.parents('#b-topbar').width('160px');
					top.$('body').first().animate({'margin-left': '160px'}, 500);

					$('#wysihtml5-toolbar').width('160px');
					self.toolbar.find('[data-wysihtml5-hiddentools=table]').addClass('visible');
				});
				self.instance.on('tableunselect:composer', function(e) {
					$.boom.page.toolbar.element.width('60px');
					self.toolbar.parents('#b-topbar').width('60px');
					top.$('body').first().animate({'margin-left': '60px'}, 500);
					$('#wysihtml5-toolbar').width('60px');
					$('#wysihtml5-toolbar [data-wysihtml5-hiddentools=table]').removeClass('visible');
				});
		}

		this.toolbar
			.on('click', '.b-editor-accept', function(event) {
				event.preventDefault();

				self.apply(self.element);

				return false;
			})
			.on('click', '.b-editor-cancel', function(event) {
				event.preventDefault();
				self.cancel(self.element);
				return false;
			})
			.on('click', '.b-editor-link', function() {
				wysihtml5.commands.createBoomLink.edit(self.instance.composer);
			})
			.on('mousedown', '.b-editor-table', function() {
				self.disableAutoSave();
			})
			.on('click', '.b-editor-table', function(e) {
				e.preventDefault();

				wysihtml5.commands.createTable.exec(self.instance.composer, 'createTable', {
					rows: 2,
					cols: 2
				});

				self.element.focus();
				self.enableAutoSave();
				self.instance.fire('tableselect:composer');
			});
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	apply : function(element) {
		var html = element.html();

		this.hideToolbar();

		if (this.mode !== 'block') {
			html = html.replace(/<br>|\n|\r|\n\r/g, ' ');
			element.html(html);
		}

		this._trigger('edit', html);
	},

	blur : function(element) {
		this.apply(element);
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	cancel : function() {
		var textEditor = this;

		this.disableAutoSave();
		this.element.blur();

		if (this.hasBeenEdited()) {
			new boomConfirmation('Cancel changes', 'Cancel all changes and exit the editor?')
				.done(function() {
					textEditor.element.html(textEditor.original_html);
					textEditor.hideToolbar();
				})
				.fail(function() {
					textEditor.element.focus();
					textEditor.enableAutoSave();
				});
		}
	},

	disableAutoSave : function() {
		this.element.unbind('blur');
	},

	enableAutoSave : function() {
		var editor = this;

		this.element.on('blur', function() {
			if ( ! editor.toolbar.children(':focus').length) {
				editor.apply(editor.element);
			}
		});
	},

	hasBeenEdited : function() {
		return this.element.html() !== this.original_html;
	},

	hideToolbar : function() {
		$('#wysihtml5-toolbar').hide().children('[data-buttonset]').hide();
	},

	showToolbar : function() {
		this.toolbar.show();
		$('#wysihtml5-toolbar').show().children().not(this.toolbar).hide();
	}
});
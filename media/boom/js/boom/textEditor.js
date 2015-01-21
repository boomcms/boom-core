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

		self.toolbar = $('#wysihtml5-toolbar').find('[data-buttonset=' + self.mode  + ']').first().clone().appendTo('#wysihtml5-toolbar');

		self.instance = new wysihtml5.Editor(element[0], { // id of textarea element
			toolbar : self.toolbar[0],
			style : true,
			parserRules :  (self.mode == 'block')? wysihtml5ParserRules : wysihtml5ParserRulesInline, // defined in parser rules set
			useLineBreaks : false,
			contentEditableMode : true,
			autoLink : false
		});

		element
			.on('focus', function() {
				if ( ! self.toolbar.is(':visible')) {
					self.showToolbar();
				}
			});

		this.enableAutoSave();

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
				}
			});

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
			});
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	apply : function(element) {
		this.hideToolbar();

		this._trigger('edit', element.html());
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
		this.hideToolbar();

		if (this.hasBeenEdited()) {
			new boomConfirmation('Cancel changes', 'Cancel all changes and exit the editor?')
				.done(function() {
					textEditor.element.html(textEditor.original_html);
				})
				.fail(function() {
					textEditor.element.focus();
				})
				.always(function() {
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
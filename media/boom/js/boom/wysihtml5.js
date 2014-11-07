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
	/**
	@property mode
	@type string
	*/
	mode : 'block',

	hasBeenEdited : false,

	/**
	@property options
	@type object
	*/
	options : {
	},

	/**
	* @function
	@param {Object} element The element being edited.
	@returns {Deferred}
	*/
	edit : function (element) {
		var self = this,
			element;

		self.mode = element.is('div') ? 'block' : 'inline';
		self.mode = (element.is(':header') ||  element.is('.standFirst') || element.is('.standfirst'))? 'text' : self.mode;
		self.edited = new $.Deferred();
		self.original_html = element.html();
		self.dialogOpen = false;

		var toolbar = $('#wysihtml5-toolbar').find('[data-buttonset=' + self.mode  + ']');

		self.instance = new wysihtml5.Editor(element[0], { // id of textarea element
			toolbar : toolbar[0],
			style : true,
			parserRules :  (self.mode == 'block')? wysihtml5ParserRules : wysihtml5ParserRulesInline, // defined in parser rules set
			useLineBreaks : false,
			contentEditableMode : true,
			autoLink : false
		});

		setTimeout(function() {
			self.showToolbar();
		}, 0);

		toolbar
			.on('click', '.b-editor-accept', function(event) {
				event.preventDefault();

				self.apply(element);
				return false;
			})
			.on( 'click', '.b-editor-cancel', function( event ){
				event.preventDefault();
				self.cancel(element);
				return false;
			})
			.on('mousedown', '.b-editor-link', function() {
				self.dialogOpen = true;
			})
			.on('click', '.b-editor-link', function() {
				wysihtml5.commands.createBoomLink.edit(self.instance.composer);
			});

		$(self.instance.composer)
			.on('before:boomdialog', function() {
				self.dialogOpen = true;
			})
			.on('after:boomdialog', function() {
				self.dialogOpen = false;
				element.focus();
			});

		self.instance
			.on('show:dialog', function(options) {
				if (options.command == 'createBoomLink') {
					if ( ! wysihtml5.commands.createBoomLink.state(self.instance.composer)) {
						wysihtml5.commands.createBoomLink.exec(self.instance.composer);
					}
				}
			});

		return self.edited;

	},

	/**
	* @function
	*/
	remove : function(element) {
		this.hideToolbar();

		element.removeAttr('contenteditable');

		this.instance = null;
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	apply : function(element) {
		this.edited.resolve(element.html());
		this.remove(element);
	},

	blur : function(element) {
		if ( ! this.dialogOpen) {
			this.apply(element);
		}
	},

	/**
	* @function
	@param {Object} element The element being edited.
	*/
	cancel : function(element) {
		var self = this,
			content = element.html();

		if (self.hasBeenEdited) {
			var confirmation = new boomConfirmation('Cancel changes', 'Cancel all changes and exit the editor?');

			confirmation
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

	hideToolbar : function() {
		$('#wysihtml5-toolbar').hide().children('[data-buttonset=' + this.mode + ']').hide();
	},

	showToolbar : function() {
		$('#wysihtml5-toolbar').show().find('[data-buttonset=' + this.mode + ']').show();
	}
});
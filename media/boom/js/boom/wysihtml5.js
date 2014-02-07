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

		self._insert_toolbar(element)
			.done(function() {
				$.boom.page.toolbar.hide();

				setTimeout(function() {
					element.focus();
				}, 10);

				self.instance = new wysihtml5.Editor(element[0], { // id of textarea element
					toolbar : top.$('#wysihtml5-toolbar')[0],
					style : true,
					parserRules :  (self.mode == 'block')? wysihtml5ParserRules : wysihtml5ParserRulesInline, // defined in parser rules set
					useLineBreaks : false,
					contentEditableMode : true,
					autoLink : false
				});

				top.$('#wysihtml5-toolbar')
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

				top.$('#b-editor-link')
					.on('mousedown', function() {
						self.dialogOpen = true;
					})
					.on('click', function() {
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
			});

		return self.edited;

	},

	/**
	* @function
	*/
	remove : function(element) {
		top.$('#wysihtml5-toolbar').remove();
		$.boom.page.toolbar.show();

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

		 return $.ajax({
			 url : '/cms/toolbar/text?mode=' + self.mode,
			 cache : true,
		 })
		.done(function(response) {
			top.$('body').prepend(response)
		});
	}
});
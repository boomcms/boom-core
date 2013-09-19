/**
* Common functionality for the embedded CMS toolbar
* @class
* @name $.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
	options : {
		height : '90px'
	},

	_create : function() {
		$.boom.log( 'init CMS toolbar' );

		this.document
			.find('body')
			.css({'margin-top' : this.options.height});

		this.element
			.css({'margin-top' : '-' + this.options.height})
			.animate({'height' : this.options.height});
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		$.boom.log('maximise iframe');
		this.element.show();
		this.element.css && this.element.css('height', '120%');
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		$.boom.log( 'minimise iframe' );
		this.element.css && this.element.css('height', this.options.height);
	},

	/**
	@function
	*/
	hide : function() {
		this.element.slideUp().hide();
		this.document
			.find( 'body' )
			.css('margin-top', 0);
	},

	/**
	@function
	*/
	show : function() {
		this.element.show().slideDown();
		this.document
			.find( 'body' )
			.css('margin-top', this.options.height);
	}
});
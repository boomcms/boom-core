/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', $.boom.page, {
	options : {
		height : '90px'
	},

	_bindButtonEvents : function() {
		var self = this;

		this.element.contents()
			.on('click', '#b-page-delete', function() {
				self.options.page.delete();
			})
			.on('click', '#b-page-addpage', function() {
				self.options.page.add();
			})
			.on('click', '#boom-page-editlive', function() {
				self.options.page.stash();
			})
			.on('click', '#b-page-readability', function() {
				self.boom.dialog.open({
					url: '/media/boom/html/readability.html'
				});
			});
	},

	_create : function() {
		this.boom.log( 'init CMS toolbar' );

		this.document
			.find('body')
			.css({'margin-top' : this.options.height});

		this.element
			.css({'margin-top' : '-' + this.options.height})
			.animate({'height' : this.options.height});

		this._bindButtonEvents();
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		this.boom.log('maximise iframe');

		this.element
			.show()
			.css('height', '120%');
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		this.boom.log('minimise iframe');

		this.element.css && this.element.css('height', this.options.height);
	},

	/**
	@function
	*/
	hide : function() {
		this.element
			.slideUp()
			.hide();

		this.document
			.find('body')
			.css('margin-top', 0);
	},

	/**
	@function
	*/
	show : function() {
		this.element
			.show()
			.slideDown();

		this.document
			.find('body')
			.css('margin-top', this.options.height);
	}
});
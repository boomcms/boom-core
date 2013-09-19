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
				self.boom.dialog.open({
					width: 350,
					url: '/cms/page/delete/' + $.boom.page.options.id,
					title: 'Please confirm',
					callback: function(){

						$.post('/cms/page/delete/' + $.boom.page.options.id, $(this).find('form').serialize(), function(response){
							self.boom.growl.show("Page deleted, redirecting to parent.");
							top.location = response;
						});
					}
				});
			})
			.on('click', '#b-page-addpage', function() {
				self.boom.loader.show();

				$.post('/cms/page/add/' + $.boom.page.options.id, {csrf : $('#b-csrf').val()}, function(response){
					self.boom.loader.hide();

					if (new RegExp('^' + "\/").test( response)) {
						top.location = response;
					} else {
						self.boom.dialog.alert('Error', response);
					}
				});
			})
			.on('click', '#boom-page-editlive', function() {
				self.boom.dialog.confirm(
					'Edit live',
					'Discard changes and edit the live page?'
				)
				.done(function(){
					self.boom.log('stashing page edits');

					$.post('/cms/page/stash/' + $.boom.page.options.id)
						.done(function(response) {
							self.boom.history.refresh();
						});
				});
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
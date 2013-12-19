/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;

	boomPage.prototype.add = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post('/cms/page/add/' + page_id, {csrf : $.boom.options.csrf}, function(response){
			var json = $.parseJSON(response);

			(typeof json === 'object')? promise.resolve(json) : promise.reject(response);
		});

		return promise;
	};

	/*
	 * I wanted to call this delete(), but that makes IE8 sad.
	 * 
	 * http://stackoverflow.com/a/17526553
	 */
	boomPage.prototype.remove = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.boom.dialog.open({
			width: 350,
			url: '/cms/page/delete/' + page_id,
			title: 'Please confirm',
			callback: function(){
				$.post('/cms/page/delete/' + page_id, $(this).find('form').serialize(), function(response) {
					promise.resolve(response);
				});
			}
		});

		return promise;
	};

	boomPage.prototype.embargo = function() {
		var url = '/cms/page/version/embargo/' + this.id;
		var promise = new $.Deferred();

		$.boom.dialog.open({
			url: url,
			title: 'Page embargo',
			width: 440,
			callback: function(){
				savePageSettings(
					url,
					$(this).find("form").serialize(),
					"Page embargo saved."
				)
				.done(function(response) {
					promise.resolve(response);
				});
			}
		});

		return promise;
	},

	boomPage.prototype.publish = function() {
		var promise = new $.Deferred();

		$.post('/cms/page/version/embargo/' + this.id, {csrf : $.boom.options.csrf})
			.done(function(response) {
				promise.resolve(response);
			});

		return promise;
	};

	boomPage.prototype.requestApproval = function() {
		var url = '/cms/page/version/request_approval/' + this.id;

		return $.post(url, {csrf : $.boom.options.csrf});
	};

	boomPage.prototype.revertToPublished = function() {
		var	promise = new $.Deferred(),
			page = this;

		$.boom.dialog.confirm(
			'Discard changes',
			'Are you sure you want to discard any unpublished changes and revert this page to it\'s published state?'
		)
		.done( function(){

			$.post('/cms/page/discard/' + page.id, {csrf : $.boom.options.csrf})
				.done(function() {
					promise.resolve();
				});
		});

		return promise;
	},

	boomPage.prototype.setTitle = function(title) {
		return $.post('/cms/page/version/title/' + this.id, {
			csrf : $.boom.options.csrf,
			title : title
		});
	},

	boomPage.prototype.stash = function() {
		var page_id = this.id;

		$.boom.dialog.confirm(
			'Edit live',
			'Discard changes and edit the live page?'
		)
		.done(function(){
			$.boom.log('stashing page edits');

			$.post('/cms/page/stash/' + page_id)
				.done(function(response) {
					$.boom.history.refresh();
				});
		});
	};
};
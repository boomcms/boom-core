/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;

	boomPage.prototype.add = function() {
		var promise = new $.Deferred(),
			page_id = this.id;

		$.post('/cms/page/add/' + page_id, {csrf : $('#b-csrf').val()}, function(response){
			if (new RegExp('^' + "\/").test( response)) {
				promise.resolve(response);
			} else {
				promise.reject(response);
			}
		});

		return promise;
	};

	boomPage.prototype.delete = function() {
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
/**
* @class
* @name boomPage
*/
function boomPage(page_id) {
	this.id = page_id;

	boomPage.prototype.add = function() {
		var self = this;

		self.boom.loader.show();

		$.post('/cms/page/add/' + self.id, {csrf : $('#b-csrf').val()}, function(response){
			if (new RegExp('^' + "\/").test( response)) {
				top.location = response;
			} else {
				self.boom.dialog.alert('Error', response);
				self.boom.loader.hide();
			}
		});
	};

	boomPage.prototype.delete = function() {
		var self = this;

		self.boom.dialog.open({
			width: 350,
			url: '/cms/page/delete/' + self.id,
			title: 'Please confirm',
			callback: function(){

				$.post('/cms/page/delete/' + self.id, $(this).find('form').serialize(), function(response){
					self.boom.growl.show("Page deleted, redirecting to parent.");
					top.location = response;
				});
			}
		});
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

		$.boom.loader.show();
		$.post('/cms/page/version/embargo/' + this.id, {csrf : $.boom.options.csrf})
			.done(function(response) {
				promise.resolve(response);
			})
			.always(function() {
				$.boom.loader.hide();
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
			$.boom.loader.show();

			$.post('/cms/page/discard/' + page.id, {csrf : $.boom.options.csrf})
				.always(function() {
					$.boom.loader.hide();
				})
				.done(function() {
					promise.resolve();
				});
		});

		return promise;
	},

	boomPage.prototype.stash = function() {
		var self = this;

		self.boom.dialog.confirm(
			'Edit live',
			'Discard changes and edit the live page?'
		)
		.done(function(){
			self.boom.log('stashing page edits');

			$.post('/cms/page/stash/' + self.id)
				.done(function(response) {
					self.boom.history.refresh();
				});
		});
	};
};
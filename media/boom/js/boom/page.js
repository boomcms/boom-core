/**
* @class
* @name $.boom.page
*/
$.boom.page = function(page_id) {
	this.page_id = page_id;

	/**
	 @property status_button
	 */
	this.status = $('#b-page-version-status').pageStatus({}).data('boomPageStatus');

	$.boom.page.protoptype.add = function() {
		var self = this;

		self.boom.loader.show();

		$.post('/cms/page/add/' + self.page_id, {csrf : $('#b-csrf').val()}, function(response){
			if (new RegExp('^' + "\/").test( response)) {
				top.location = response;
			} else {
				self.boom.dialog.alert('Error', response);
				self.boom.loader.hide();
			}
		});
	};

	$.boom.page.protoptype.delete = function() {
		var self = this;

		self.boom.dialog.open({
			width: 350,
			url: '/cms/page/delete/' + self.page_id,
			title: 'Please confirm',
			callback: function(){

				$.post('/cms/page/delete/' + self.page_id, $(this).find('form').serialize(), function(response){
					self.boom.growl.show("Page deleted, redirecting to parent.");
					top.location = response;
				});
			}
		});
	};

	$.boom.page.protoptype.stash = function() {
		var self = this;

		self.boom.dialog.confirm(
			'Edit live',
			'Discard changes and edit the live page?'
		)
		.done(function(){
			self.boom.log('stashing page edits');

			$.post('/cms/page/stash/' + self.page_id)
				.done(function(response) {
					self.boom.history.refresh();
				});
		});
	};
};
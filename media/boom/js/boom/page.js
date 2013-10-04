/**
@fileOverview Page editor, toolbar and page settings.
*/
/**
* Page editor UI.
* @class
* @name $.boom.page
*/
$.widget( 'boom.page', {

	/** @lends $.boom.page */

	boom : $.boom,

	/**
	default config
	@property options
	*/
	options : {},

	/**
	 @property status_button
	 */
	status: $('#b-page-version-status').pageStatus({}).data('boomPageStatus'),

	_init : function() {

	},

	_destroy : function() {

	},

	add : function() {
		var self = this;

		self.boom.loader.show();

		$.post('/cms/page/add/' + self.options.id, {csrf : $('#b-csrf').val()}, function(response){
			if (new RegExp('^' + "\/").test( response)) {
				top.location = response;
			} else {
				self.boom.dialog.alert('Error', response);
				self.boom.loader.hide();
			}
		});
	},

	delete : function() {
		var self = this;

		self.boom.dialog.open({
			width: 350,
			url: '/cms/page/delete/' + self.options.id,
			title: 'Please confirm',
			callback: function(){

				$.post('/cms/page/delete/' + self.options.id, $(this).find('form').serialize(), function(response){
					self.boom.growl.show("Page deleted, redirecting to parent.");
					top.location = response;
				});
			}
		});
	},

	stash : function() {
		var self = this;

		self.boom.dialog.confirm(
			'Edit live',
			'Discard changes and edit the live page?'
		)
		.done(function(){
			self.boom.log('stashing page edits');

			$.post('/cms/page/stash/' + self.options.id)
				.done(function(response) {
					self.boom.history.refresh();
				});
		});
	}
} );
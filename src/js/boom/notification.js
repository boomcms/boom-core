function boomNotification(message) {
	boomNotification.prototype.$document = $(top.document);

	boomNotification.prototype.options = {
		theme : 'default',
		speed : 240,
		closer : false,
		sticky : false,
		open : function(elem, message){
			$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
		}
	};

	boomNotification.prototype.open = function(message) {
		if ( ! this.$document.find('#b-notification').length) {
			$('<div id="b-notification"></div>')
					.appendTo(this.$document.find('body'));
		}

		$.jGrowl(message, this.options);

		this.$document.find('#b-notification').append($('#jGrowl'));
	};

	this.open(message);
};
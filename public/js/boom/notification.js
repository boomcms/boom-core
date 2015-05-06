function boomNotification(message) {
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
		$.jGrowl(message, this.options);

		this.$jgrowl = this.$jgrowl? this.$jgrowl : $('#jGrowl');

		if (this.$jgrowl.length) {
			top.$('body').append(this.$jgrowl);
		}
	};

	this.open(message);
};
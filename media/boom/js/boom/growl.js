$.widget('boom.boomGrowl', {
	options : {
		theme: 'default',
		speed: 240,
		closer: false,
		open: function(elem, message){
			$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
		}
	},

	_create : function() {

	},

	/** @function */
	show : function(msg, sticky){

		$.jGrowl(msg, $.extend({}, this.options, {
			sticky: sticky,
		}));

		var $jgrowl = $('#jGrowl');

		if ($jgrowl.length) {
			top.$('body').append($jgrowl);
		}
	},

	/** @function */
	hide : function(id){
		$('.jGrowl-notification').trigger('jGrowl.close');
		top.$('#jGrowl').remove();
	}
});
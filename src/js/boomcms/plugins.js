/**
@fileOverview jQuery plugins written specifically for Boom.
*/

/**
@namespace
@name $.fn
*/

(function($) {

	/**
	@function
	*/
	$.fn.ui = function() {
		this.find('.boom-datepicker').datetimepicker({
			format: 'd F Y H:i'
		});

		this.find('time').localTime();

		return this;
	};

	$.fn.localTime = function() {
		var $this = $(this);

		if ($this.length) {
			var tz = BoomCMS.getTimezone();

			$this.each(function() {
				var $el = $(this),
					time = moment($el.attr('datetime')).tz(tz).format('Do MMMM YYYY HH:mm');

				$el.text(time);
			});
		}
	};
})( jQuery );
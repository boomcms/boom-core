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

	$.fn.assetManagerImages = function() {
		$(this).each(function() {
			var $this = $(this),
				asset = new BoomCMS.Asset({id: $this.attr('data-asset')}),
				url  = asset.getUrl('thumb', $this.width(), $this.height()) + '?' + Math.floor(Date.now() / 1000);

			$this.find('img')
				.attr('src', url)
				.on('load', function() {
					$(this).removeClass('loading');
				});
		});
	};
})( jQuery );
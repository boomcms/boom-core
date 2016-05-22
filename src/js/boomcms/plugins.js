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
		this.find('.boom-tabs').tabs();
		this.find('.boom-datepicker').datetimepicker({
			format: 'd F Y H:i'
		});

		return this;
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
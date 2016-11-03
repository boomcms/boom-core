/**
@fileOverview jQuery plugins written specifically for Boom.
*/

/**
@namespace
@name $.fn
*/

(function($) {
	'use strict';

	$.fn.dblclick = function() {
		var $this = $(this);

		$this.each(function() {
            var $el = $(this);

            $el.on('click', function() {
                var clicks = $el.data('clicks');

                $el.data('clicks', clicks ? ++clicks : 1);

                if ($el.data('clicks') > 1) {
                    $el.data('clicks', 0);

                    $el.trigger('dclick');
                } else {
                    setTimeout(function() {
                        if ($el.data('clicks') === 1) {
                            $el.data('clicks', 0);
                            $el.trigger('sclick');
                        }
                    }, 200);
                }
            });
        });

		return this;
	};

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

	$.fn.boomTabs = function() {
		var selectedClass = 'selected';

		$(this).on('click', function(e) {
			var $link = $(this),
				href = $link.attr('href'),
				$target;

			if (href === '#') {
				return;
			}

			$target = $(href);

			if ($target.length) {
				e.preventDefault();

				$link.parents('ul').find('a').removeClass(selectedClass);
				$link.addClass(selectedClass);

				$target.siblings().removeClass(selectedClass);
				$target.addClass(selectedClass);
			}
		});
	};
})(jQuery);

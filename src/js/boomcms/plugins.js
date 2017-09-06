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
        this.find('.boom-datepicker')
            .each(function() {
                var $this = $(this),
                    timestamp = parseInt($this.attr('data-timestamp'));

                if (timestamp) {
                    $this.val(moment(timestamp, 'X').format('DD MMMM YYYY HH:mm'));
                }
            })
            .datetimepicker({
                format: 'd F Y H:i'
            });

        this.find('time').localTime();

        // Used in the login / password reset forms. To be extended to other forms.
        this.find('.input input').boomcmsInput();

        return this;
    };

    $.fn.localTime = function() {
        var $this = $(this);

        if ($this.length) {
            var tz = BoomCMS.getTimezone();

            $this.each(function() {
                var $el = $(this),
                    time = moment($el.attr('datetime')).tz(tz),
                    text = $el.hasClass('since') ? time.fromNow() : time.format('Do MMMM YYYY HH:mm');

                $el.text(text);
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

    // Used for the editable name in people manager person and group view.
    $.fn.boomcmsEditableHeading = function() {
        var $this = $(this),
            edit = function($el) {
                $el
                    .removeClass(BoomCMS.editableClass)
                    .focus();
            };

        $this
            .addClass(BoomCMS.editableClass)
            .on('click', function() {
                edit($(this));
            })
            .on('blur', function() {
                $(this).addClass(BoomCMS.editableClass)
            })
            .next('a')
            .on('click', function(e) {
                e.preventDefault();

                edit($(this).prev());
            });

        return $this;
    };

    $.fn.boomcmsInput = function() {
        $(this).on('input paste change keyup', function() {
            var $this = $(this),
                className = 'has-content';

            $this.val() ? $this.addClass(className) : $this.removeClass(className);
        }).trigger('input');
    };

    $.fn.boomcmsMenuButton = function() {
        $(this).on('click', function() {
            var $body = $('body'),
                $window = $(top.window);

            if ($body.hasClass('menu-open')) {
                $body.removeClass('menu-open');

                setTimeout(function() {
                    $window.trigger('boom:dialog:close');
                }, 250);
            } else {
                $window.trigger('boom:dialog:open');
                $body.addClass('menu-open');
            }
        });
    };
})(jQuery);

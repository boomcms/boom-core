$.widget('boom.justifyAssets', {
    targetRightOffset : null,
    windowWidth : null,

    closeRemainingGap: function() {
        var lastRowGap = this.currentRow.determineGap(this.targetRightOffset);

        if (lastRowGap <= (this.element.outerWidth(true) * 0.75)) {
            this.currentRow.expandTo(this.targetRightOffset);
        } else if (this.rows > 1) {
            this.prevRow.merge(this.currentRow);
        }
    },

    _create: function() {
        var justifyAssets = this,
            resizeTimeout;

        this.window.on('resize', function() {
            if (resizeTimeout !== undefined) {
                clearTimeout(resizeTimeout);
            }

            resizeTimeout = setTimeout(function() {
                justifyAssets.justify();
            }, 50);
        });
    },

    _init: function() {
        this.justify();
    },

    _getOffset: function($el) {
        var offset = $el.offset();
        offset.right = this.windowWidth - (offset.left + $el.outerWidth(true));

        return offset;
    },

    hasElements: function() {
        return this.element.children().length > 1;
    },

    justify: function() {
        this.currentRow = new Row();
        this.prevRow = null;
        this.rows = 0;
        this.windowWidth = $(window).width();
        this.targetRightOffset = (this.windowWidth - (this.element.offset().left + this.element.innerWidth()));

        if (this.hasElements()) {
            this.resetInitialDimensions();
            this.resizeElements();
            this.closeRemainingGap();
        }
    },

    resetInitialDimensions: function() {
        this.element.children().each(function(index, element) {
            var $child = $(element);

            if (!$child.css('height') || !$child.attr('data-aspect-ratio')) {
                $child.remove();
                return true;
            }

            $child.css({
                height: '160px',
                width: Math.floor(160 * $child.attr('data-aspect-ratio')) + 'px'
            });
        });
    },

    resizeElements: function() {
        var justifyAssets = this;

        this.element.children().each(function(index, element) {
            var $child = $(element);

            $child.offset = justifyAssets._getOffset($child);

            justifyAssets.prevRow = jQuery.extend({}, justifyAssets.currentRow);
            justifyAssets.currentRow.addElementToRow($child);

            if (justifyAssets.currentRow.isAtStart() && index > 0) {
                this.rows++;
                justifyAssets.prevRow.expandTo(justifyAssets.targetRightOffset);
            }
        });
    }
});

function Row() {
    this.elements = [];
    this.aspectRatioSum = 0;

    Row.prototype.addElementToRow = function($el) {
        if (this._elementStartsRow($el)) {
            this.elements = [];
            this.aspectRatioSum = 0;
        }

        this.elements.push($el);
        this.aspectRatioSum += parseFloat($el.attr('data-aspect-ratio'));

        return this;
    };

    Row.prototype.determineGap = function(offset) {
        return this.elements[this.elements.length - 1].offset.right - offset;
    };

    Row.prototype.expandTo = function(offset) {
        var endOfRowGap = this.determineGap(offset) - 1;

        if (endOfRowGap > 0) {
            var increaseBy = Math.floor(endOfRowGap / this.aspectRatioSum);
            var remainder = Math.floor(endOfRowGap) - 1;

            if (increaseBy <= endOfRowGap) {
                $.each(this.elements, function(index, $el) {
                    var incWidth = Math.floor(increaseBy * $el.attr('data-aspect-ratio'));
                    remainder -= incWidth;

                    $el
                        .height('+=' + increaseBy)
                        .width('+=' + incWidth);
                });
            }

            if (remainder > 0) {
                while (remainder > 1) {
                    $.each(this.elements, function(index, $el) {
                        if (remainder <= 1) {
                            return false;
                        }

                        $el.css('margin-right', '+=1');
                        remainder -= 1;
                    });
                }
            }
        }

        $.each(this.elements, function(index, $el) {
            $el.trigger('justified');
        });
    };

    Row.prototype.shrinkBy = function(size) {

        var total_aspect_ratio = 0;
        $.each(this.elements, function(index, $el) {
            total_aspect_ratio += $el.data('aspect-ratio');
        });

        $.each(this.elements, function(index, $el) {
            var reduceBy = size * ($el.data('aspect-ratio') / total_aspect_ratio);

            $el
                .width('-=' + Math.ceil(reduceBy))
                .height('-=' + Math.ceil(reduceBy / $el.data('aspect-ratio')));
        });
    };

    Row.prototype.getWidth = function() {
        var width = 0;

        $.each(this.elements, function(index, $el) {
            width += $el.outerWidth(true);
        });

        return width;
    };

    Row.prototype.isAtStart = function() {
        return this.elements.length <= 1;
    };

    Row.prototype.merge = function(row) {
        var width = row.getWidth();
        this.shrinkBy(width);

        var heightDif = this.elements[0].height() - row.elements[0].height();

        $.each(row.elements, function(index, $el) {
            $el.height('+=' + heightDif);
        });
    };

    Row.prototype._elementStartsRow = function($el) {
        if ( ! this.elements.length) {
            return true;
        }

        return ($el.offset.top >= (this.elements[this.elements.length - 1].offset.top + $el.height()));
    };
}
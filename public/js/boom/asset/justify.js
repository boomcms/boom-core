$.widget('boom.justifyAssets', {
	$el : null,
	targetRightOffset : null,
	windowWidth : null,

	_create : function() {
		this.$el = $(this.element);

		this._setDimensions();
		this.justify();
	},

	_getOffset : function($el) {
		var offset = $el.offset();
		offset.right = this.windowWidth - (offset.left + $el.outerWidth(true));

		return offset;
	},

	justify : function() {
		var currentRow = new Row();
		var prevRow;
		var self = this;

		if (this.$el.children().length > 1) {
			this.$el.children().each(function(index, element) {
				var $child = $(element);
				$child.offset = self._getOffset($child);

				if ( ! $child.css('height') || ! $child.attr('data-aspect-ratio')) {
					$child.remove();
					return true;
				}

				prevRow = jQuery.extend({}, currentRow);
				currentRow.addElementToRow($child);

				if (currentRow.isAtStart() && index > 0) {
					prevRow.expandTo(self.targetRightOffset);
				}

			});

			var lastRowGap = currentRow.determineGap(this.targetRightOffset);

			if (lastRowGap <= (this.$el.outerWidth(true) * 0.75)) {
				currentRow.expandTo(self.targetRightOffset);
			} else {
				prevRow.merge(currentRow);
			}
		}
	},

	_setDimensions : function() {
		this.windowWidth = $(window).width();
		this.targetRightOffset = (this.windowWidth - (this.$el.offset().left + this.$el.innerWidth()));
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
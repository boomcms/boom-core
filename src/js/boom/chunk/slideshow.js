/**
@class
@name chunkSlideshow
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkSlideshow', $.ui.chunk,
	/**
	@lends $.ui.chunkSlideshow
	*/
	{

	/**
	Open a slideshow dialog
	*/
	edit: function() {
		var chunk = this,
			editor = new boomChunkSlideshowEditor(this.options.page, this.options.name, {
				title : chunk.hasClass('slide-title'),
				caption : chunk.hasClass('slide-caption'),
				link : chunk.hasClass('slide-link'),
				linkText : chunk.hasClass('slide-linktext')
			});

		editor
			.done(function(slides) {
				if ( ! slides || ! slides.length) {
					chunk.remove();
				} else {
					chunk.slides = slides;
					chunk._save();
				}

				chunk.bind();
			})
			.fail(function() {
				chunk.bind();
			});
	},

	hasClass : function(c) {
		return this.element.hasClass(c) || this.element.find('.' + c).length > 0;
	},

	/**
	Get slideshow slide data.
	@returns {Array}
	*/
	getData: function() {
		return {slides : this.slides};
	}
});
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
		var editor = new boomChunkSlideshowEditor(this.options.page, this.options.name),
			chunk = this;

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

	/**
	Get slideshow slide data.
	@returns {Array}
	*/
	getData: function() {
		return {slides : this.slides};
	},

	_update_html : function(html) {
		$.ui.chunk.prototype._update_html.call(this, html);

		var slideshowScript = new Function($(html).find('script').text());
		slideshowScript();
	}
});
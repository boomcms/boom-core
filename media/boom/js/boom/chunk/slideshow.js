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
		new boomAlert('Your changes have been saved and the page will now reload to update the slideshow')
			.done(function() {
				top.location.reload();
			});
	}
});
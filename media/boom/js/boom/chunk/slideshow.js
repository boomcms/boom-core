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
				title : chunk.hasTitle(),
				caption : chunk.hasCaption(),
				link : chunk.hasLink(),
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
	
	hasCaption : function() {
		return this.element.hasClass('slide-caption') || this.element.find('.slide-caption').length > 0;
	},
	
	hasLink : function() {
		return this.element.hasClass('slide-link') || this.element.find('.slide-link').length > 0;
	},

	hasTitle : function() {
		return this.element.hasClass('slide-title') || this.element.find('.slide-title').length > 0;
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
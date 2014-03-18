function boomChunkSlideshowEditor(page_id, slotname) {
	this.page_id = page_id;
	this.slotname = slotname;

	boomChunkSlideshowEditor.prototype.bind = function() {
		var slideshowEditor = this;

		this.dialog
			.on('click', '#b-slideshow-editor-delete', function() {
				slideshowEditor.deferred.resolveWith({});
				$.boom.dialog.destroy(slideshowEditor.dialog);
			})
			.on('change', '#b-slideshow-editor-slides input[type=radio]', function() {
				var slide = slideshowEditor._getSlideDetails($(this));
				slideshowEditor.editSlide(slide);
			});

		this.dialog.find('#b-slideshow-editor-slides').sortable({});
	};

	boomChunkSlideshowEditor.prototype.editSlide = function(slide) {
		console.log(slide);
	};

	boomChunkSlideshowEditor.prototype._getSlideDetails = function($element) {
		return {
			asset_id : $element.val(),
			url : $element.data('url'),
			caption : $element.data('caption'),
			title : $element.data('title')
		};
	};

	boomChunkSlideshowEditor.prototype.open = function() {
		var slideshowEditor = this;
		this.deferred = new $.Deferred();

		this.dialog = $.boom.dialog.open({
			url : '/cms/chunk/slideshow/edit/' + this.page_id + '?slotname=' + this.slotname,
			id : 'b-slideshow-editor',
			width: 860,
			open : function() {
				slideshowEditor.bind();
			},
			callback : function(data) {
				slideshowEditor.deferred.resolve(data);
			}
		});

		return this.deferred;
	};
};
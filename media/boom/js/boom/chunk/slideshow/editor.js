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
			})
			.on('click', '#b-slideshow-editor-current-delete', function(e) {
				e.preventDefault();

				slideshowEditor.deleteCurrentSlide();
			})
			.on('click', '#b-slideshow-editor-current form a', function(e) {
				e.preventDefault();

				var currentAssetId = $(this).find('img').attr('src').replace('/asset/view/', '');
				slideshowEditor.editCurrentSlideAsset(currentAssetId);
			})
			.find('#b-slideshow-editor-slides')
			.sortable({});
	};

	boomChunkSlideshowEditor.prototype.deleteCurrentSlide = function() {
		this.dialog
			.find('#b-slideshow-editor-slides input[type=radio]:checked')
			.parents('li')
			.remove()
			.end()
			.end()
			.find('#b-slideshow-editor-current')
			.find('.default')
			.show()
			.end()
			.find('form')
			.hide();
	};

	boomChunkSlideshowEditor.prototype.editCurrentSlideAsset = function(currentAssetId) {
		var slideshowEditor = this;

		$.boom.assets.picker({
			asset_rid : currentAssetId
		})
		.pipe(function(asset_id) {
			slideshowEditor.dialog
				.find('#b-slideshow-editor-current img, #b-slideshow-editor-slides input[type=radio]:checked + img')
				.attr('src', '/asset/view/' + asset_id);
		})
		.always(function() {
			$.boom.history.load( '' );
		});
	};

	boomChunkSlideshowEditor.prototype.editSlide = function(slide) {
		this.dialog.find('#b-slideshow-editor-current')
			.find('.default')
			.hide()
			.end()
			.find('form')
			.show()
			.end()
			.find('img')
			.attr('src', '/asset/view/' + slide.asset_id)
			.end()
			.find('input[name=title]')
			.val(slide.title)
			.find('input[name=caption]')
			.val(slide.caption)
			.find('input[name=url]')
			.val(slide.url);
	};

	boomChunkSlideshowEditor.prototype.getAllSlideDetails = function() {
		var slideshowEditor = this;

		return this.dialog
			.find('#b-slideshow-editor-slides li input')
			.map(function(index, el) {
				var $el = $(el);

				return slideshowEditor._getSlideDetails($el);
			})
			.get();
	};

	boomChunkSlideshowEditor.prototype._getSlideDetails = function($element) {
		return {
			asset_id : $element.siblings('img').attr('src').replace('/asset/view/', ''),
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
			width: 920,
			open : function() {
				slideshowEditor.bind();
			},
			callback : function(data) {
				slideshowEditor.deferred.resolve(slideshowEditor.getAllSlideDetails());
			}
		});

		return this.deferred;
	};
};
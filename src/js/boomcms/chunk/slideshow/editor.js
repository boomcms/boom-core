function boomChunkSlideshowEditor(page_id, slotname, options) {
	this.page_id = page_id;
	this.slotname = slotname;
	this.options = options;

	boomChunkSlideshowEditor.prototype.bind = function() {
		var slideshowEditor = this;

		if ( ! this.options.title) {
			this.dialog.contents.find('.b-slideshow-title').hide();
		}

		if ( ! this.options.caption) {
			this.dialog.contents.find('.b-slideshow-caption').hide();
		}

		if ( ! this.options.link) {
			this.dialog.contents.find('.b-slideshow-link').hide();
		}

		if ( ! this.options.linkText) {
			this.dialog.contents.find('.b-slideshow-linktext').hide();
		}

		this.dialog.contents
			.on('click', '#b-slideshow-editor-delete', function() {
				slideshowEditor.deferred.resolveWith({});
				$.boom.dialog.destroy(slideshowEditor.dialog);
			})
			.on('change', '#b-slideshow-editor-slides input[type=radio]', function() {
				var slide = slideshowEditor._getSlideDetails($(this));
		
				slide.asset = new boomAsset(slide.asset_id);
				slideshowEditor.editSlide(slide);
			})
			.on('click', '#b-slideshow-editor-current-delete', function(e) {
				e.preventDefault();

				slideshowEditor.deleteCurrentSlide();
			})
			.on('click', '#b-slideshow-editor-current form a', function(e) {
				e.preventDefault();

				var currentAssetId = $(this).find('img').attr('src').replace(/\/asset\/(\d+)(.*?)/, "$1");
				slideshowEditor.editCurrentSlideAsset(new boomAsset(currentAssetId));
			})
			.on('keydown, change', '#b-slideshow-editor-current form input[type=text]', function() {
				var $this = $(this),
					name = $this.attr('name'),
					val = $this.val();

				slideshowEditor.getCurrentSlide().data(name, val);
			})
			.on('click', '#b-slideshow-editor-current .b-slideshow-link button', function(e) {
				e.preventDefault();

				var $input = $(this).parent().find('input'),
					currentSlide = slideshowEditor.getCurrentSlide();

				new boomLinkPicker(new boomLink($input.val(), currentSlide.data('page')))
					.done(function(link) {
						currentSlide
							.data('url', link.getUrl())
							.data('page', link.getPageId());

						$input
							.val(link.getUrl())
							.blur();
					});
			})
			.on('click', '#b-slideshow-editor-add', function(e) {
				e.preventDefault();

				slideshowEditor.addSlide();
			})
			.find('#b-slideshow-editor-slides')
			.sortable({});
	};

	boomChunkSlideshowEditor.prototype.addSlide = function() {
		var slideshowEditor = this;

		new boomAssetPicker()
			.done(function(asset) {
				var $new_slide = $('<li><label><input type="radio" value="" name="slide" data-asset="' + asset.getId() + '"  /><img src="' + asset.getUrl('view', 0, 100) + '" /></label></li>');

				slideshowEditor.dialog.contents
					.find('#b-slideshow-editor-slides')
					.append($new_slide);

				$new_slide.find('input').attr('checked', true);
				slideshowEditor.editSlide({asset : asset});
			});
	};

	boomChunkSlideshowEditor.prototype.deleteCurrentSlide = function() {
		this.getCurrentSlide()
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

	boomChunkSlideshowEditor.prototype.editCurrentSlideAsset = function(currentAsset) {
		var slideshowEditor = this;

		new boomAssetPicker(currentAsset)
			.done(function(asset) {
				slideshowEditor.dialog.contents
					.find('#b-slideshow-editor-current img, #b-slideshow-editor-slides input[type=radio]:checked + img')
					.attr('src', asset.getUrl('view', 300))
					.siblings('input')
					.attr('data-asset', asset.getId());
			});
	};

	boomChunkSlideshowEditor.prototype.editSlide = function(slide) {
		this.dialog.contents.find('#b-slideshow-editor-current')
			.find('.default')
			.hide()
			.end()
			.find('form')
			.show()
			.find('img')
			.attr('src', slide.asset.getUrl('view', 300))
			.end()
			.find('input[name=title]')
			.val(slide.title)
			.end()
			.find('input[name=caption]')
			.val(slide.caption)
			.end()
			.find('input[name=url]')
			.val(new boomLink(slide.url).getUrl())
			.end()
			.find('input[name=linktext]')
			.val(slide.linktext);
	};

	boomChunkSlideshowEditor.prototype.getAllSlideDetails = function() {
		var slideshowEditor = this;

		return this.dialog.contents
			.find('#b-slideshow-editor-slides li')
			.map(function(index, el) {
				var $el = $(el);

				return slideshowEditor._getSlideDetails($el.find('input'));
			})
			.get();
	};

	boomChunkSlideshowEditor.prototype.getCurrentSlide = function() {
		return this.dialog.contents.find('#b-slideshow-editor-slides input[type=radio]:checked');
	};

	boomChunkSlideshowEditor.prototype._getSlideDetails = function($element) {
		return {
			asset_id: $element.attr('data-asset'),
			url: $element.data('url'),
			page: $element.data('page'),
			caption: $element.data('caption'),
			title: $element.data('title'),
			linktext: $element.data('linktext')
		};
	};

	boomChunkSlideshowEditor.prototype.open = function() {
		var slideshowEditor = this;
		this.deferred = new $.Deferred();

		this.dialog = new boomDialog({
			url : '/boomcms/page/' + this.page_id + '/chunk/edit?slotname=' + this.slotname + '&type=slideshow',
			id : 'b-slideshow-editor',
			width: 920,
			closeButton: false,
			saveButton: true,
			open : function() {
				slideshowEditor.bind();
			}
		})
		.done(function() {
			slideshowEditor.deferred.resolve(slideshowEditor.getAllSlideDetails());
		})
		.fail(function() {
			slideshowEditor.deferred.reject();
		});

		return this.deferred;
	};

	return this.open();
};

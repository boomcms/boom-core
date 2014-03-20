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
			.on('keydown, change', '#b-slideshow-editor-current form input[type=text]', function() {
				var $this = $(this),
					name = $this.attr('name'),
					val = $this.val();

				slideshowEditor.getCurrentSlide().data(name, val);
			})
			.on('click', '#b-slideshow-editor-current form input[name=url]', function() {
				var $input = $(this),
					currentSlide = slideshowEditor.getCurrentSlide();

				$.boom.links.picker( {
					title: 'Edit slide link',
					link : {
						url : $input.val(),
						rid : currentSlide.data('page')
					}
				})
				.pipe(function(new_link) {
					var url = new_link.url.replace(window.location.protocol + '//' + window.location.hostname, '')
					currentSlide
						.data('url', url)
						.data('page', new_link.page_rid);

					$input
						.val(url)
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

		$.boom.assets.picker()
			.pipe(function(asset_id) {
				var $new_slide = $('<li><label><input type="radio" value="" name="slide" data-asset="' + asset_id + '"  /><img src="/asset/view/' + asset_id + '" /></label></li>');

				slideshowEditor.dialog
					.find('#b-slideshow-editor-slides')
					.append($new_slide);

				$new_slide.find('input').attr('checked', true);
				slideshowEditor.editSlide({asset_id : asset_id});
			})
			.always(function() {
				$.boom.history.load( '' );
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
			.find('img')
			.attr('src', '/asset/view/' + slide.asset_id)
			.end()
			.find('input[name=title]')
			.val(slide.title)
			.end()
			.find('input[name=caption]')
			.val(slide.caption)
			.end()
			.find('input[name=url]')
			.val(slide.url);
	};

	boomChunkSlideshowEditor.prototype.getAllSlideDetails = function() {
		var slideshowEditor = this;

		return this.dialog
			.find('#b-slideshow-editor-slides li')
			.map(function(index, el) {
				var $el = $(el);

				return slideshowEditor._getSlideDetails($el.find('input'));
			})
			.get();
	};

	boomChunkSlideshowEditor.prototype.getCurrentSlide = function() {
		return this.dialog.find('#b-slideshow-editor-slides input[type=radio]:checked');
	};

	boomChunkSlideshowEditor.prototype._getSlideDetails = function($element) {
		return {
			asset_id : $element.siblings('img').attr('src').replace('/asset/view/', ''),
			url : $element.data('url'),
			page : $element.data('page'),
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
			deferred : this.deferred,
			width: 920,
			open : function() {
				slideshowEditor.bind();
			},
			callback : function() {
				slideshowEditor.deferred.resolve(slideshowEditor.getAllSlideDetails());
			}
		});

		return this.deferred;
	};

	return this.open();
};
boomPageFeatureEditor = function(page) {
	this.changed = false;
	this.deferred = new $.Deferred();
	this.page = page;
	this.url = '/cms/page/settings/feature/' + this.page.id;

	boomPageFeatureEditor.prototype.done = function(callback) {
		this.deferred.done(callback);

		return this;
	};

	boomPageFeatureEditor.prototype.getImagesInPage = function() {
		return top.$('body')
			.find('img[src^="/asset/view/"]')
			.filter(function(){
				return $(this).parents('.b-dialog').length === 0
			})
			.map(function() {
				var $this = $(this),
					assetId,
					src = $this.attr('src').replace('/asset/view/', '');

				return src.indexOf('/')? src : src.substring(0, src.indexOf('/'));
			});
	};

	boomPageFeatureEditor.prototype._open = function() {
		var pageFeatureEditor = this,
			$imagesInPageContainer = this.dialog.contents.find('.images-in-page');

		this.currentImage = this.dialog.contents.find('#b-page-feature-current').attr('src').replace('/asset/view/', '');

		this.dialog.contents
			.on('click', '.b-page-feature-set', function() {
				pageFeatureEditor.setFeature($(this).data('asset-id'));
			})
			.on('click', '#b-page-feature-remove', function() {
				pageFeatureEditor.removeFeature();
			})
			.on('click', '#b-page-feature-edit', function() {
				new boomAssetPicker(pageFeatureEditor.currentImage)
					.done(function(assetId) {
						pageFeatureEditor.setFeature(assetId);
					});
			});

		if (this.imagesInPage.length) {
			for (var i = 0; i < this.imagesInPage.length; i++) {
				$imagesInPageContainer.append("<li><a href='#' class='b-page-feature-set' data-asset-id='" + this.imagesInPage[i] + "'><img src='/asset/view/" + this.imagesInPage[i] + "' /></a></li>");
			}
		} else {
			$imagesInPageContainer.replaceWith("<p>This page doesn't contain any images.</p>");
		}

		if (this.currentImage) {
			this.dialog.contents.find('#b-page-feature-none').hide();
		} else {
			this.dialog.contents.find('#b-page-feature-current').hide();
			this.dialog.contents.find('#b-page-feature-remove').button('disable');
		}
	};

	boomPageFeatureEditor.prototype.open = function() {
		var pageFeatureEditor = this;
		this.imagesInPage = this.getImagesInPage();

		this.dialog = new boomDialog({
			url: this.url,
			title: 'Page feature image',
			closeButton: false,
			saveButton: true,
			onLoad: function() {
				pageFeatureEditor._open();
			}
		}).done(function() {
			pageFeatureEditor.save()
				.done(function() {
					pageFeatureEditor.deferred.resolve(pageFeatureEditor.currentImage);
				})
				.fail(function() {
					pageFeatureEditor.deferred.reject();
				});
		})
		.fail(function() {
			pageFeatureEditor.deferred.reject();
		});

		return this.deferred;
	};

	boomPageFeatureEditor.prototype.removeFeature = function() {
		var pageFeatureEditor = this,
			confirmation = new boomConfirmation('Please confirm', "Are you sure you want to do delete this page's feature image?");

		confirmation
			.done(function() {
				pageFeatureEditor.setFeature(0);
			});
	};

	boomPageFeatureEditor.prototype.save = function() {
		var pageFeatureEditor = this;

		if (this.changed) {
			return $.post(this.url, {feature_image_id : this.currentImage})
				.done(function(response) {
					new boomNotification('Page feature image saved');
				});
		}
	};

	boomPageFeatureEditor.prototype.setFeature = function(assetId) {
		if (assetId !== this.currentImage) {
			this.changed = true;
			this.currentImage = assetId;

			if (assetId) {
				this.dialog.contents.find('#b-page-feature-current').attr('src', '/asset/view/' + assetId + '/' + '500').show();
				this.dialog.contents.find('#b-page-feature-remove').button('enable');
				this.dialog.contents.find('#b-page-feature-none').hide();
			} else {
				this.dialog.contents.find('#b-page-feature-current').hide();
				this.dialog.contents.find('#b-page-feature-remove').button('disable');
				this.dialog.contents.find('#b-page-feature-none').show();
			}
		}
	};

	return this.open();
};
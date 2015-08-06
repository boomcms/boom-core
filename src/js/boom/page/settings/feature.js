$.widget('boom.pageSettingsFeature', {
	changed: false,

	getImagesInPage: function() {
		return top.$('body:first-of-type')
			.find('img[src^="/asset/view/"]')
			.map(function() {
				var $this = $(this),
					assetId,
					src = $this.attr('src').replace('/asset/view/', '');

				return src.indexOf('/')? src : src.substring(0, src.indexOf('/'));
			});
	},

	bind: function() {
		var pageFeatureEditor = this,
			$imagesInPageContainer = this.element.find('.images-in-page');

		this.element
			.on('click', '#b-page-feature-remove', function() {
				pageFeatureEditor.removeFeature();
			})
			.on('click', '#b-page-feature-edit', function() {
				new boomAssetPicker(pageFeatureEditor.currentImage)
					.done(function(assetId) {
						pageFeatureEditor.setFeature(assetId);
					});
			})
			.on('click', '.b-button-cancel', function() {
				pageFeatureEditor.setFeature(pageFeatureEditor.intial);
				pageFeatureEditor.changed = false;
			}).on('click', '.b-button-save', function() {
				pageFeatureEditor.save();
			});

		if (this.imagesInPage.length) {
			for (var i = 0; i < this.imagesInPage.length; i++) {
				$imagesInPageContainer.append("<li><a href='#' class='b-page-feature-set' data-asset-id='" + this.imagesInPage[i] + "'><img src='/asset/view/" + this.imagesInPage[i] + "' /></a></li>");
			}
		} else {
			$imagesInPageContainer.parent('section').hide();
		}

		if (this.currentImage) {
			this.element.find('#b-page-feature-none').hide();
		} else {
			this.element.find('#b-page-feature-current').hide();
			this.element.find('#b-page-feature-remove').prop('disabled', true);
		}
	},

	_create: function() {
		this.currentImage = this.initial = this.element.find('#b-page-feature-current').attr('src').replace('/asset/view/', '');
		this.imagesInPage = this.getImagesInPage();
		this.bind();
	},

	removeFeature: function() {
		var pageFeatureEditor = this,
			confirmation = new boomConfirmation('Please confirm', "Are you sure you want to do delete this page's feature image?");

		confirmation
			.done(function() {
				pageFeatureEditor.setFeature(0);
			});
	},

	save: function() {
		var pageFeatureEditor = this;

		if (this.changed) {
			pageFeatureEditor.options.page.setFeatureImage(this.currentImage)
				.done(function(response) {
					new boomNotification('Page feature image saved');
			
					pageFeatureEditor._trigger('done', null, pageFeatureEditor.currentImage);
				});
		}
	},

	setFeature: function(assetId) {
		if (assetId !== this.currentImage) {
			this.changed = true;
			this.currentImage = assetId;

			if (assetId) {
				this.element.find('#b-page-feature-current').attr('src', '/asset/view/' + assetId + '/' + '500').show();
				this.element.find('#b-page-feature-remove').prop('disabled', false);
				this.element.find('#b-page-feature-none').hide();
			} else {
				this.element.find('#b-page-feature-current').hide();
				this.element.find('#b-page-feature-remove').prop('disabled', true);
				this.element.find('#b-page-feature-none').show();
			}
		}
	}
});
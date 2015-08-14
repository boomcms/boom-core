$.widget('boom.pageSettingsFeature', {
	changed: false,

	getImagesInPage: function() {
		return top.$('body:first-of-type')
			.find('img[src^="/asset/"]')
			.map(function() {
				var $this = $(this),
					assetId,
					src = $this.attr('src').replace(/\/asset\/(\d+)(.*?)/, "$1");

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
					.done(function(asset) {
						pageFeatureEditor.setFeature(asset);
					});
			})
			.on('click', '.b-button-cancel', function() {
				pageFeatureEditor.setFeature(pageFeatureEditor.intial);
				pageFeatureEditor.changed = false;
			}).on('click', '.b-button-save', function() {
				pageFeatureEditor.save();
			})
			.on('click', '.b-page-feature-set', function() {
				pageFeatureEditor.setFeature(new boomAsset($(this).attr('data-asset-id')));
			});

		if (this.imagesInPage.length) {
			for (var i = 0; i < this.imagesInPage.length; i++) {
				var asset = new boomAsset(this.imagesInPage[i]);

				$imagesInPageContainer.append("<li><a href='#' class='b-page-feature-set' data-asset-id='" + asset.getId() + "'><img src='" + asset.getUrl() + "' /></a></li>");
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
		this.currentImage = this.initial = new boomAsset(this.element.find('#b-page-feature-current').attr('src').replace(/\/asset\/(\d+)(.*)/, "$1"));
		this.imagesInPage = this.getImagesInPage();
		this.bind();
	},

	removeFeature: function() {
		this.setFeature(new boomAsset());
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

	setFeature: function(asset) {
		if (asset.getId() !== this.currentImage.getId()) {
			this.changed = true;
			this.currentImage = asset;

			if (asset.getId()) {
				this.element.find('#b-page-feature-current').attr('src', asset.getUrl('view', 500)).show();
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
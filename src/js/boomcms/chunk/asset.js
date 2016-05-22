$.widget('ui.chunkAsset', $.ui.chunk, {
	editAssetOnly: function() {
		var chunkAsset = this;

		new boomAssetPicker(this.asset, this.getPickerFilters())
		.done(function(asset) {
			chunkAsset.asset = asset;

			if (asset.getId()) {
				chunkAsset.save({
					asset_id : asset.getId()
				});
			} else {
				chunkAsset.remove();
			}
		})
		.fail(function() {
			chunkAsset.destroy();
		});
	},

	editAllElements: function() {
		var chunkAsset = this;

		new boomChunkAssetEditor(this.options.page, this.options.name, {
			caption : this.elements.caption.length > 0,
			link : this.elements.link.length > 0,
			title : this.elements.title.length
		})
		.done(function(chunkData) {
			chunkAsset.asset = new BoomCMS.Asset({id: chunkData['asset_id']});
			chunkAsset.save(chunkData);
		})
		.fail(function() {
			chunkAsset.destroy();
		});
	},

	/**
	 @function
	 */
	 getElements: function() {
		var elements = {};

		var img = this.element.find('img');
		var a = this.element.find('a');

		var regExp = new RegExp("asset\/(thumb|view|download)\/" + this.assetId);

		elements.asset = this.element.find('.asset-target');
		elements.link = this.element.hasClass('asset-link')? this.element : this.element.find('.asset-link');
		elements.caption = this.element.hasClass('asset-caption')? this.element : this.element.find('.asset-caption');
		elements.title = this.element.hasClass('asset-title')? this.element : this.element.find('.asset-title');

		if (! elements.asset.length) {
			if (img.length && regExp.test(img.attr('src'))) {
				elements.asset = img;
			}
			else if (a.length && regExp.test(a.attr('href'))) {
				elements.asset = a;
			}

			if ( ! elements.asset.length) {
				elements.asset = this.element;
			}
		}

		if ( ! elements.link.length && a.length && elements.asset != a && a.attr('href') && a.attr('href') != '#' && ! regExp.test(a.attr('href'))) {
			elements.link = a;
		}

		return elements;
	 },

	edit: function() {
		this.elements = this.getElements();
		this.asset = new BoomCMS.Asset({id: this.element.attr('data-boom-target')});

		if (this.hasMetadata()) {
			this.editAllElements();
		} else {
			this.editAssetOnly();
		}
	},

	 getPickerFilters: function() {
		 if (this.element.attr('data-boom-filterbytype')) {
			 return {
				 type : this.element.attr('data-boom-filterByType')
			 };
		 }
	 },

	hasMetadata: function() {
		return (this.elements.caption.length || this.elements.link.length || this.elements.title.length);
	},

	save: function(data) {
		this._save(data);
		this.destroy();
	}
});

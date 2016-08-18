function boomAssetPicker(currentAsset, filters) {
	this.currentAsset = typeof(currentAsset) === 'object' ? 
		currentAsset : new BoomCMS.Asset();

	this.deferred = new $.Deferred();
	this.document = $(document);

	this.filters = filters ? filters : {};

	boomAssetPicker.prototype.url = '/boomcms/assets/picker';

	boomAssetPicker.prototype.assetsUploaded = function(assetIds) {
		if (assetIds.length === 1) {
			this.pick(new BoomCMS.Asset({id: assetIds[0]}));
		} else {
			this.clearFilters();
			this.getAssets();
		}
	};

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.picker
			.on('click', '.thumb', function(e) {
				e.preventDefault();

				var assetId = $(this).attr('data-asset');

				assetPicker.pick(new BoomCMS.Asset({id: assetId}));
			})
			.on('click', '#b-assets-picker-close', function() {
				assetPicker.cancel();
			})
			.on('click', '#b-assets-picker-current-remove', function() {
				assetPicker.pick(new BoomCMS.Asset());
			})
			.find('#b-assets-upload-form')
			.assetUploader({
				uploadFinished: function(e, data) {
					assetPicker.assetsUploaded(data.result);
				}
			});
	};

	boomAssetPicker.prototype.cancel = function() {
		this.deferred.reject();
		this.dialog.cancel();
	};

	boomAssetPicker.prototype.close = function() {
		this.dialog.cancel();
	};

	boomAssetPicker.prototype.hideCurrentAsset = function() {
		this.picker
			.find('#b-assets-picker-current')
			.hide();
	};

	boomAssetPicker.prototype.loadPicker = function() {
		var assetPicker = this;

		this.dialog = new boomDialog({
			url : this.url,
			onLoad: function() {
				assetPicker.dialog.contents.parent().css({
					position: 'fixed',
					height: '100vh',
					width: '100vw',
					transform: 'none'
				});

				assetPicker.picker = assetPicker.dialog.contents.find('#b-assets-picker');

				if (typeof(assetPicker.filters.type) !== 'undefined') {
					assetPicker.showActiveTypeFilter(assetPicker.filters.type);
				}

				assetPicker.dialog.contents.assetSearch({
					filters: assetPicker.filters
				});

				assetPicker.bind();

				if (assetPicker.currentAsset && assetPicker.currentAsset.getId() > 0) {
					assetPicker.picker
						.find('#b-assets-picker-current img')
						.attr('src', assetPicker.currentAsset.getUrl());
				} else {
					assetPicker.hideCurrentAsset();
				}
			}
		});
	};

	boomAssetPicker.prototype.open = function() {
		this.loadPicker();

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset) {
		this.deferred.resolve(asset);

		this.close();
	};

	/**
	 * Selects an option in the type filter select box to show that the type filter is active.
	 * Used when the asset picker is opened with an active type filter.
	 *
	 * @param {string} type
	 * @returns {boomAssetPicker.prototype}
	 */
	boomAssetPicker.prototype.showActiveTypeFilter = function(type) {
		var assetPicker = this,
			$types = this.dialog.contents.find('#b-assets-types');

		$types.find('option').each(function() {
			var $this = $(this);

			if ($this.val().toLowerCase() === type.toLowerCase()) {
				$types.val($this.val());
			}
		});

		return this;
	};

	return this.open();
};
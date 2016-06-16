function boomAssetSelection(assetIds) {
	this.assets = typeof(assetIds) === 'object' ? assetIds : [];

	boomAssetSelection.prototype.baseUrl = '/boomcms/assets/';

	boomAssetSelection.prototype.add = function(assetId) {
		var index = this.assets.indexOf(assetId);

		if (index === -1) {
			this.assets.push(assetId);
		} else {
			this.assets.splice(index, 1);
		}
	};

	boomAssetSelection.prototype.addTag = function(tag) {
		$.post(this.baseUrl + 'tags/add', {
			assets : this.assets,
			tag : tag
		});
	};

	boomAssetSelection.prototype.clear = function() {
		this.assets = [];
	};

	boomAssetSelection.prototype.delete = function() {
		var selection = this,
			deleted = new $.Deferred(),
			confirmation,
			message = this.hasMultipleIds()? 'Are you sure you wanted to delete these assets?' : 'Are you sure you want to delete this asset?'

		confirmation = new boomConfirmation('Please confirm', message);
		confirmation.done(function() {
			$.post(selection.baseUrl + 'delete', {
				assets : selection.assets
			})
			.done(function() {
				deleted.resolve();
			});
		});

		return deleted;
	};
	
	boomAssetSelection.prototype.download = function() {
		var url = this.baseUrl + 'download?',
			assets = [];

		for (var i = 0; i < this.assets.length; i++) {
			assets[i] = 'asset[]=' + this.assets[i];
		}

		url = url + assets.join('&');

		if (this.assets.length > 1) {
			var dialog = new boomDialog({
				msg: '<label><p>Enter the name of the download</p><input type="text" name="filename" value="BoomCMS Asset Download" /></label>',
				width: 400,
				id: 'b-assets-download-filename'
			})
			.done(function() {
				url = url + '&filename=' + dialog.contents.find('input[name=filename]').val();

				window.location = url;
			});
		} else {
			window.location = url;
		}
	};

	boomAssetSelection.prototype.get = function() {
		return $.get(this.baseUrl + 'view/' + this.assets);
	};

	boomAssetSelection.prototype.hasMultipleIds = function() {
		return this.length() > 1;
	};

	boomAssetSelection.prototype.index = function(i) {
		return this.assets[i];
	};

	boomAssetSelection.prototype.length = function() {
		return this.assets.length;
	};

	boomAssetSelection.prototype.removeTag = function(tag) {
		$.post(this.baseUrl + 'tags/remove', {
			assets : this.assets,
			tag : tag
		});
	};

	boomAssetSelection.prototype.revertToVersion = function(versionId) {
		return $.post(this.baseUrl + 'revert/' + this.assets[0], {
			version_id: versionId
		});
	};

	boomAssetSelection.prototype.save = function(data) {
		return $.post(this.baseUrl + 'save/' + this.assets[0], data);
	};

	boomAssetSelection.prototype.tag = function() {
		var assetSelection = this,
			url = this.baseUrl + 'tags/list/' + this.assets.join('-'),
			dialog;
				
		dialog = new boomDialog({
			url: url,
			title: 'Asset tags',
			width: 440,
			cancelButton : false,
			onLoad: function() {
				dialog.contents.find('#b-tags').assetTagSearch({
					addTag: function(e, tag) {
						assetSelection.addTag(tag);
					},
					removeTag: function(e, tag) {
						assetSelection.removeTag(tag);
					}
				});
			}
		});
	};
};
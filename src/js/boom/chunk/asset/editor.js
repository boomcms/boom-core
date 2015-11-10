function boomChunkAssetEditor(pageId, slotname, visibleElements) {
	this.pageId = pageId;
	this.slotname = slotname;
	this.visibleElements = visibleElements;

	boomChunkAssetEditor.prototype.bind = function() {
		var chunkAssetEditor = this;

		this.assetElement.on('click', function() {
			new boomAssetPicker(chunkAssetEditor.asset)
				.done(function(asset) {
					chunkAssetEditor.setAsset(asset);
				});
		});

		this.link.on('click', 'button', function() {
			var $this = $(this);

			new boomLinkPicker(new boomLink($this.parent().find('input').val()))
				.done(function(link) {
					chunkAssetEditor.setLink(link.getUrl());
				});
		});
	};

	boomChunkAssetEditor.prototype.dialogOpened = function() {
		this.title = this.dialog.contents.find('.b-title');
		this.caption = this.dialog.contents.find('.b-caption');
		this.link = this.dialog.contents.find('.b-link');
		this.assetElement = this.dialog.contents.find('a');
		this.asset = new boomAsset(this.assetElement.attr("data-asset-id"));

		this.bind();
		this.toggleElements();
	};

	boomChunkAssetEditor.prototype.getData = function() {
		return {
			asset_id : this.asset.getId(),
			caption : this.caption.find('textarea').val(),
			url : this.link.find('input').val(),
			title : this.title.find('textarea').val()
		};
	};

	boomChunkAssetEditor.prototype.open = function() {
		var chunkAssetEditor = this;
		this.deferred = new $.Deferred();

		this.dialog = new boomDialog({
			url : '/cms/chunk/' + this.pageId + '/edit?slotname=' + this.slotname + '&type=asset',
			id : 'b-assets-chunk-editor',
			width: 900,
			closeButton: false,
			saveButton: true,
			open : function() {
				chunkAssetEditor.dialogOpened();
			}
		})
		.done(function() {
			chunkAssetEditor.deferred.resolve(chunkAssetEditor.getData());
		 })
		.fail(function() {
			chunkAssetEditor.deferred.reject();
		 });

		return this.deferred;
	};

	boomChunkAssetEditor.prototype.setAsset = function(asset) {
		this.asset = asset;
		this.assetElement.attr('data-asset-id', asset.getId());

		var $img = this.assetElement.find('img');

		if ( ! $img.length) {
			$img = $('<img />');
			this.assetElement.find('p').replaceWith($img);
		}

		$img.attr('src', asset.getUrl('view', 400));
	};

	boomChunkAssetEditor.prototype.setLink = function(link) {
		this.link
			.find('input')
			.val(link)
			.blur();
	};

	boomChunkAssetEditor.prototype.toggleElements = function() {
		var elements = ['title', 'caption', 'link'],
			i,
			element;

		for (i = 0; i < elements.length; i++) {
			element = elements[i];

			if ( ! this.visibleElements[element]) {
				this[element].hide();
			}
		}
	};

	return this.open();
};
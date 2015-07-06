function boomChunkAssetEditor(pageId, slotname, visibleElements) {
	this.pageId = pageId;
	this.slotname = slotname;
	this.visibleElements = visibleElements;

	boomChunkAssetEditor.prototype.bind = function() {
		var chunkAssetEditor = this;

		this.asset.on('click', function() {
			new boomAssetPicker(chunkAssetEditor.asset.attr("data-asset-id"))
				.done(function(assetId) {
					chunkAssetEditor.setAsset(assetId);
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
		this.asset = this.dialog.contents.find('a');

		this.bind();
		this.toggleElements();
	};

	boomChunkAssetEditor.prototype.getData = function() {
		return {
			asset_id : this.asset.attr('data-asset-id'),
			caption : this.caption.find('textarea').val(),
			url : this.link.find('input').val(),
			title : this.title.find('textarea').val()
		};
	};

	boomChunkAssetEditor.prototype.open = function() {
		var chunkAssetEditor = this;
		this.deferred = new $.Deferred();

		this.dialog = new boomDialog({
			url : '/cms/chunk/asset/edit/' + this.pageId + '?slotname=' + this.slotname,
			id : 'b-assets-chunk-editor',
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

	boomChunkAssetEditor.prototype.setAsset = function(assetId) {
		this.asset.attr('data-asset-id', assetId);

		var $img = this.asset.find('img');

		if ( ! $img.length) {
			$img = $('<img />');
			this.asset.find('p').replaceWith($img);
		}

		$img.attr('src', '/asset/view/' + assetId + '/400');
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
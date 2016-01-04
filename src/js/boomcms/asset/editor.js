function boomAssetEditor(asset, uploader) {
    this.asset = asset;
    this.uploader = uploader;
	this.selection = new boomAssetSelection([this.asset.id]);

    boomAssetEditor.prototype.bind = function(dialog) {
        var asset = this.asset,
			selection = this.selection,
            assetEditor = this;

        dialog.contents
			.on('click', '.b-assets-delete', function() {
				selection
					.delete()
					.done(function() {
						dialog.cancel();
					});
			})
			.on('click', '.b-assets-download', function(e) {
				e.preventDefault();
				selection.download();
			})
            .on('click', '.b-assets-replace', function(e) {
                var uploadFinished = assetEditor.uploader.assetUploader('option', 'uploadFinished');

                e.preventDefault();

                assetEditor.uploader.assetUploader('replacesAsset', asset);
                assetEditor.uploader.assetUploader('option', 'uploadFinished', function(e, data) {
                    assetEditor.reloadPreviewImage();
                    uploadFinished(e, data);

                    // Restore the previous event handler.
                    assetEditor.uploader.assetUploader('option', 'uploadFinished', uploadFinished);
                });

                assetEditor.uploader.show();
            })
            .on('click', '.b-assets-revert', function(e) {
                e.preventDefault();

                assetEditor.revertTo($(this).attr('data-version-id'));
            })
			.on('click', '.b-assets-openeditor', function(e) {
				e.preventDefault();
		
				new boomImageEditor(asset.getUrl() + '?' + new Date().getTime())
					.done(function(blob) {
						assetEditor.replaceWithBlob(blob);
					});
			})
			.on('click', '.b-assets-save', function() {
				assetEditor.selection
					.save(assetEditor.dialog.contents.find('form').serialize())
					.done(function() {
						new boomNotification("Asset details saved");
					});
			})
			.on('focus', '#thumbnail', function() {
				var $this = $(this);

				new boomAssetPicker(new boomAsset($this.val()))
					.done(function(asset) {
						$this.val(asset.getId());
					});
			});
    };

    boomAssetEditor.prototype.open = function() {
        var assetEditor = this;

        this.dialog = new boomDialog({
			title : 'Edit Asset',
			url : '/boomcms/assets/view/' + assetEditor.asset.id,
			width: document.documentElement.clientWidth >= 1000? '1000px' : '100%',
			closeButton: false,
			onLoad : function() {
                assetEditor.bind(assetEditor.dialog);

				assetEditor.dialog.contents
					.find('#b-tags')
					.assetTagSearch({
						addTag: function(e, tag) {
							assetEditor.selection.addTag(tag);
						},
						removeTag: function(e, tag) {
							assetEditor.selection.removeTag(tag);
						}
					});
			}
		});

        return this.dialog;
    };

    boomAssetEditor.prototype.reloadPreviewImage = function() {
        var $img = this.dialog.contents.find('.b-assets-preview img');

        $img.attr("src", $img.attr('src') + '?' + new Date().getTime());
    };
	
	boomAssetEditor.prototype.replaceWithBlob = function(blob) {
		var assetEditor = this,
			data = new FormData();
		
		data.append('files[]', blob);

		$.ajax({
			data: data,
			url: '/boomcms/assets/replace/' + asset.getId(),
			processData: false,
			contentType: false,
			type: 'POST'
		})
		.done(function() {
			assetEditor.reloadPreviewImage();
		});
	};

    boomAssetEditor.prototype.revertTo = function(versionId) {
        var assetEditor = this;

        this.selection.revertToVersion(versionId)
            .done(function() {
                new boomNotification("This asset has been reverted to the previous version");
                assetEditor.reloadPreviewImage();
            });
    };

    return this.open();
};

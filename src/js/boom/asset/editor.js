function boomAssetEditor(asset, uploader) {
    this.asset = asset;
    this.uploader = uploader;

    boomAssetEditor.prototype.bind = function() {
        var asset = this.asset,
            dialgo = this.dialog,
            assetEditor = this;

        this.dialog.contents
			.on('click', '.b-assets-delete', function() {
				asset
					.delete()
					.done(function() {
						dialog.close();
						assetManager.getAssets();
					});
			})
			.on('click', '.b-assets-download', function(e) {
				e.preventDefault();
				asset.download();
			})
            .on('click', '.b-assets-replace', function(e) {
                e.preventDefault();

                assetEditor.uploader.assetUploader('replacesAsset', asset);
                assetEditor.uploader.show();
            })
			.on('focus', '#thumbnail', function() {
				var $this = $(this);

				new boomAssetPicker($this.val())
					.done(function(assetId) {
						$this.val(assetId);
					});
			});
    };

    boomAssetEditor.prototype.open = function() {
        var assetEditor = this;

        this.dialog = new boomDialog({
			title : 'Edit Asset',
			url : '/cms/assets/view/' + assetEditor.asset.id,
			width: document.documentElement.clientWidth >= 1000? '1000px' : '100%',
			closeButton: false,
			saveButton: true,
			onLoad : function() {
				assetEditor.dialog.contents
					.find('#b-tags')
					.assetTagSearch({
						addTag: function(e, tag) {
							assetEditor.asset.addTag(tag);
						},
						removeTag: function(e, tag) {
							assetEditor.asset.removeTag(tag);
						}
					});
			}
		}).done(function() {
            assetEditor.asset
                .save(assetEditor.dialog.contents.find('form').serialize())
                .done(function() {
                    new boomNotification("Asset details saved");
                });
        });

        this.bind();

        return this.dialog;
    };

    return this.open();
};

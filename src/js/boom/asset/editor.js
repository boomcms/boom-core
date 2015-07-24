function boomAssetEditor(asset) {
    this.asset = asset;

    boomAssetEditor.prototype.bind = function() {
        var asset = this.asset,
            dialgo = this.dialog;

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

        return this.dialog;
    };

    return this.open();
};

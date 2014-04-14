$.widget('boom.asset', {
	bind : function() {
		var asset = this.asset;

		this.element
			.on('click', '.b-assets-save', function(event) {
				var data = $(this).closest('form').serialize();

				asset
					.save(data)
					.done(function() {
						new boomNotification("Asset saved.");
					});

			})
			.on('click', '.b-assets-download', function(event) {
				asset.download();
			})
			.on('click', '.b-assets-delete', function(event) {
				event.preventDefault();
				event.stopPropagation();

				asset
					.delete()
					.done(function() {
						$.boom.history.load('tag/0');
					});

				return false;
			})
			.find('#b-tags')
			.tagger({
				type: 'asset',
				id: asset.id
			});
	},

	_create : function() {
		this.asset = new boomAsset(this.options.asset_id);

		this.bind();
	}
});
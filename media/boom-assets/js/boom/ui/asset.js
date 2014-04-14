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
			.on('click', '.b-assets-back', function(event) {
				event.preventDefault();
				$.boom.history.load('');
			})
			.on('click', '.b-assets-delete', function(event) {
				event.preventDefault();
				event.stopPropagation();

				asset
					.delete()
					.done(function() {
						$.boom.history.load('');
					});

				return false;
			})
			.on('.b-assets-replace', 'click', function(event) {
				self.
					upload({
						url: '/cms/assets/upload',
						formData : [{
							csrf: $.boom.options.csrf,
							name: 'asset_id',
							value: asset.id
						}]
					})
					.done(function(data) {
						$.boom.history.refresh();
						new boomNotification('Asset updated');
					});
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
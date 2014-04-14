function boomAssetPicker(currentAssetId) {
	this.currentAssetId = currentAssetId;
	this.deferred = new $.Deferred();

	boomAssetPicker.prototype.url = '/cms/assets/manager';

	boomAssetPicker.prototype.bind = function() {
		var assetPicker = this;

		this.dialog.contents
			.on('click', '.thumb a', function(e) {
				e.preventDefault();

				var asset_id = $(this).attr('href').replace('#asset/', '');

				assetPicker.pick(asset_id);

				return false;
			});
	};

	boomAssetPicker.prototype.open = function() {
		var assetPicker = this;

		this.dialog = new boomDialog({
			url : this.url,
			width: document.documentElement.clientWidth > 1000? '60%' : '900',
			height: '700',
			dialogClass : 'b-dialog b-assets-dialog',
			title: 'Select an asset',
			open: function() {
				var dialog = $(this);

//				var upload = $('<button />')
//					.addClass('ui-helper-left b-button ui-button')
//					.text( 'Upload' )
//					.button({
//						text: false,
//						icons: { primary : 'b-button-icon b-button-icon-upload' }
//					})
//					.click( function() {
//						browser.browser_asset( 'upload' )
//							.done( function(){
//								$.boom.history.load( 'tag/0' );
//							});
//					});

				$(this).dialog('widget')
					.find('.ui-dialog-buttonpane')
//					.prepend(upload)
					.append($('<div class="center"><div id="b-assets-pagination"></div><div id="b-assets-stats"></div></div>'));
			},
			onLoad: function() {
				$(this).assetManager({});
//				browser = $('#b-assets-manager').browser_asset();
//
//				$.when(browser.browser_asset('browse'))
//					.progress(function(asset_id) {
//						cleanup();
//						complete.resolve(asset_id);
//						dialog.close();
//						browser.remove();
//					});
//
//				// browser widget pushes a default URL to the history stack.
//				// need to override that if an asset is already selected
//				// by setting a fragment identifier on the parent window.
//				if ( opts.asset_rid && opts.asset_rid > 0 ) {
//					$.boom.log( 'getting asset ' + opts.asset_rid );
//					browser.browser_asset( 'edit', opts.asset_rid );
//				}
			}
		});

		this.bind();

		return this.deferred;
	};

	boomAssetPicker.prototype.pick = function(asset_id) {
		this.deferred.resolve(asset_id);
		this.dialog.close();
	};

	return this.open();
};
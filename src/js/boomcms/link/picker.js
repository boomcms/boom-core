function boomLinkPicker(link, options) {
	this.deferred = new $.Deferred();
	this.link = link? link : new boomLink();

	this.defaultOptions = {
		text: false,
		remove: false,
		external: true,
		asset: true
	};

	this.options = $.extend(this.defaultOptions, options);

	boomLinkPicker.prototype.bind = function() {
		var linkPicker = this;

		this.externalTypeSelector
			.on('change', function() {
				var type = linkPicker.externalTypeSelector.val(),
					val = linkPicker.externalUrl.val();

				if (type === 'http' || type === 'https') {
					linkPicker.externalUrl.autocomplete('enable');
				} else {
					linkPicker.externalUrl.autocomplete('disable');
				}

				if (val === 'http://') {
					linkPicker.externalUrl.val('');
				}

				linkPicker.externalUrl.focus();
				linkPicker.externalUrl[0].setSelectionRange(0, val.length);
			});

		this.externalUrl.autocomplete({
			appendTo: linkPicker.dialog.contents.find('#b-linkpicker-add-external form'),
			source: function(request, response) {
				if (linkPicker.externalTypeSelector.val('http') || linkPicker.externalTypeSelector.val('https')) {
					if (linkPicker.externalUrl.val()) {
						$.ajax({
							url: '/boomcms/autocomplete/page-titles',
							dataType: 'json',
							data: {
								text : linkPicker.externalUrl.val()
							}
						})
						.done(function(data) {
							response(data);
						});
					}
				}
			},
			select: function(event, ui) {
				event.preventDefault();

				linkPicker.externalUrl.val(ui.item.value);
			}
		});

		this.dialog.contents.find('.boom-tree').pageTree({
			onPageSelect: function(link) {
				linkPicker.pick(link);
				linkPicker.dialog.cancel();
			}
		});

		this.removeButton.on('click', function(e) {
			e.preventDefault();

			linkPicker.deferred.resolve(new boomLink());
			linkPicker.dialog.cancel();
		});

		this.dialog.contents
			.on('click', '#b-linkpicker-asset-select', function() {
				new boomAssetPicker(linkPicker.link.getAsset())
					.done(function(asset) {
						var action = linkPicker.asset.find('option:selected').val();

						linkPicker.externalUrl.val(asset.getUrl(action));
						linkPicker.setAssetPreview(asset);
					});
			})
			.on('focus', '#b-linkpicker-add-asset select', function() {
				var $this = $(this);

				$this.data('previous', $this.find('option:selected').val());
			})
			.on('change', '#b-linkpicker-add-asset select', function() {
				if (linkPicker.link.isAsset()) {
					var $this = $(this),
						action = $this.find('option:selected').val(),
						url = linkPicker.externalUrl.val().replace($this.data('previous'), action);

						linkPicker.externalUrl.val(url);
				}
			});
	};

	boomLinkPicker.prototype.getExternalLink = function() {
		var url = this.externalUrl.val(),
			linkText;
	
		linkText = (this.options.text && this.textInput.val()) ?
			this.textInput.val() :
			url.replace('mailto:', '').replace('tel:', '');

		if (url.indexOf(window.location.hostname) === -1) {
			switch(this.externalTypeSelector.val()) {
				case 'http':
					if (url.substring(0, 7) !== 'http://'
							&& url.substring(0, 8) !== 'https://'
							&& url.substring(0, 1) !== '/'
							&& url.substring(0, 1) !== '#')
					{
						url = 'http://' + url;
					}

					break;
				case 'mailto':
					if (url.substring(0, 7) !== 'mailto:') {
						url = 'mailto:' + url;
					}

					break;
				case 'tel':
					if (url.substring(0, 4) !== 'tel:') {
						url = 'tel:' + url.replace(' ', '');
					}

					break;
			}
		}

		return new boomLink(url, 0, linkText);
	};

	boomLinkPicker.prototype.onLoad = function(dialog) {
		this.dialog = dialog;
		this.internal = dialog.contents.find('#b-linkpicker-add-internal');
		this.external = dialog.contents.find('#b-linkpicker-add-external');
		this.asset = dialog.contents.find('#b-linkpicker-add-asset');
		this.externalTypeSelector = this.external.find('select');
		this.externalUrl = this.external.find('input');
		this.textInput = dialog.contents.find('#b-linkpicker-text input[type=text]');
		this.removeButton = dialog.contents.find('#b-linkpicker-remove');

		if (!this.options.remove) {
			this.removeButton.hide();
		}

		if (!this.options.external) {
			this.external.hide();
			dialog.contents.find('.ui-tabs-nav li:nth-of-type(2)').hide();
		}

		if (!this.options.asset) {
			this.asset.hide();
			dialog.contents.find('.ui-tabs-nav li:nth-of-type(3)').hide();
		}

		this.setupInternal();
		this.setupExternalUrl();
		this.setupText();
		this.setupAssetLink();
		this.bind();
	};

	boomLinkPicker.prototype.open = function() {
		var linkPicker = this;

		new boomDialog({
			msg : $('#b-linkpicker-container').html(),
			id : 'b-linkpicker',
			width : 600,
			closeButton: this.options.external || this.options.text || this.options.asset,
			onLoad: function(dialog) {
				linkPicker.onLoad(dialog);
			}
		})
		.done(function() {
			linkPicker.pick(linkPicker.getExternalLink());
		})
		.fail(function() {
			linkPicker.deferred.reject();
		});

		return this.deferred;
	};

	boomLinkPicker.prototype.pick = function(link) {
		this.deferred.resolve(link);
	};

	boomLinkPicker.prototype.setAssetPreview = function(asset) {
		this.asset.find('img').attr('src', asset.getUrl('thumb'));
	};

	boomLinkPicker.prototype.setupAssetLink = function() {
		if (this.link.isAsset()) {
			this.setAssetPreview(this.link.getAsset());

			this.asset
				.find('select')
				.find('option')
				.removeAttr('selected')
				.end()
				.find('option[value="' + this.link.getAssetAction() + '"]')
				.attr('selected', 'selected');

			$('a[href=#b-linkpicker-add-asset]').click();
		}
	};

	boomLinkPicker.prototype.setupExternalUrl = function() {
		var url = this.link.url;

		if (this.link.isMailto()) {
			url = url.replace('mailto:', '');
			this.externalTypeSelector.val('mailto');
		} else if (this.link.isTel()) {
			url = url.replace('tel:', '');
			this.externalTypeSelector.val('tel');
		} else {
			url = this.link.getUrl();
			this.externalTypeSelector.val('http');
		}

		this.externalUrl.val(url);

		if (url !== "" && !this.link.isAsset()) {
			$('a[href=#b-linkpicker-add-external]').click();
		}
	};

	boomLinkPicker.prototype.setupInternal = function() {
		var pageId = this.link.getPageId();

		if (pageId) {
			this.internal.find('input').val(pageId);
		}
	};

	boomLinkPicker.prototype.setupText = function() {
		if (!this.options.text) {
			this.dialog.contents.find('#b-linkpicker-text').hide();
			this.dialog.contents.find('a[href=#b-linkpicker-text]').hide();
		} else {
			this.dialog.contents
				.find('#b-linkpicker-text input[type=text]')
				.val(link.getTitle());
		}
	};

	return this.open();
}

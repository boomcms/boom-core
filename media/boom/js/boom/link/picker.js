function boomLinkPicker(link) {
	this.deferred = new $.Deferred();
	this.link = link? link : new boomLink();

	boomLinkPicker.prototype.bind = function() {
		var linkPicker = this;

		this.externalTypeSelector
			.on('change', function() {
				var val = linkPicker.externalUrl.val();

				if (val === 'http://') {
					linkPicker.externalUrl.val('');
				}

				linkPicker.externalUrl.focus();
				linkPicker.externalUrl[0].setSelectionRange(0, val.length);
			});

		this.dialog.contents.find('.boom-tree').pageTree({
			onPageSelect : function(link) {
				linkPicker.pick(link);
				linkPicker.dialog.cancel();
			}
		});
	};

	boomLinkPicker.prototype.getExternalLink = function() {
		var url,
			linkText = url = this.externalUrl.val();

		if (url.indexOf(window.location.hostname) == -1) {
			switch(this.externalTypeSelector.val()) {
				case 'http':
					if (url.substring(0,7) !='http://' && url.substring(0,8) !='https://' && url.substring(0,1) != '/') {
						url = 'http://' + url;
					}
					break;
				case 'mailto':
					if (url.substring(0,6) != 'mailto:') {
						url = 'mailto:' + url;
					}
					break;
				case 'tel':
					if (url.substring(0,3)) {
						url = 'tel:' + url;
					}
					break;
			}
		} else {
			url.replace(window.location.hostname, '');
		}

		return new boomLink(url, 0, linkText);
	};

	boomLinkPicker.prototype.onLoad = function() {
		this.internal = this.dialog.contents.find('#b-linkpicker-add-internal');
		this.external = this.dialog.contents.find('#b-linkpicker-add-external'),
		this.externalTypeSelector = this.external.find('select'),
		this.externalUrl = this.external.find('input');

		this.setupInternal();
		this.setupExternalUrl();
		this.bind();
	};

	boomLinkPicker.prototype.open = function() {
		var linkPicker = this;

		this.dialog = new boomDialog({
			title : 'Edit link',
			url : '/cms/chunk/insert_url',
			id : 'b-linkpicker',
			width : 600,
			onLoad : function() {
				linkPicker.onLoad();
			}
		})
		.done(function() {
			linkPicker.pick(linkPicker.getExternalLink());
		});

		return this.deferred;
	};

	boomLinkPicker.prototype.pick = function(link) {
		this.deferred.resolve(link);
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

		if (url !== "") {
			$('a[href=#b-linkpicker-add-external]').click();
		}
	};

	boomLinkPicker.prototype.setupInternal = function() {
		var pageId = this.link.getPageId();

		if (pageId) {
			this.internal.find('input').val(pageId);
		}
	};

	return this.open();
};
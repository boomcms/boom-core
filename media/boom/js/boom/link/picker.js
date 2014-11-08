function boomLinkPicker(link) {
	this.deferred = new $.Deferred();

	this.link = link? link : new boomLink();

	boomLinkPicker.prototype.bind = function() {
		var linkPicker = this;

		this.externalTypeSelector
			.on('change', function() {
				this.externalUrl.focus();

				if (this.externalUrl.val() == 'http://') {
					this.externalUrl.val('');
				}
			});

		if (this.link.isExternal()) {
			var url = this.link.url;

			if (url.substring(0,7) =='http://' || url.substring(0,8) =='https://' || url.substring(0,1) == '/') {
				url = url.replace('https://', '').replace('http://', '');
				type_selector.val('http');
			}
			else if (url.substring(0,7) =='mailto:') {
				url = url.replace('mailto:', '');
				type_selector.val('mailto');
			}
			else if (url.substring(0,4) =='tel:') {
				url = url.replace('tel:', '');
				type_selector.val('tel');
			}

			if (url != "") {
				external_url.val(url);
				$('a[href=#boom-chunk-linkset-addlink-external]').trigger('click');
			}
		}

		this.dialog.contents.find('.boom-tree').pageTree({
			onPageSelect : function(pageId) {
				linkPicker.pick(new boomLink("", pageId));
			}
		});
	};

	boomLinkPicker.prototype.getExternalLink = function() {
		var url,
			link_text = url = this.dialog.contents.find('#boom-chunk-linkset-addlink-external-url').val();

		if (url.indexOf(window.location.hostname) == -1) {
			switch(this.dialog.contents.find('#b-chunk-linkset-addlink-external-type').val()) {
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

		return {
			url : url,
			text : link_text
		};
	};

	boomLinkPicker.prototype.onLoad = function() {
		this.internal = this.dialog.contents.find('#b-linkpicker-add-internal');
		this.external = this.dialog.contents.find('#b-linkpicker-add-external'),
		this.externalTypeSelector = this.external.find('select'),
		this.externalUrl = this.external.find('input');

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
			linkPicker.pick(this.getExternalLink());
		});

		return this.deferred;
	};

	boomLinkPicker.prototype.pick = function(link) {
		this.deferred.resolve(link);
	};

	return this.open();
};
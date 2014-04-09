function boomLinkPicker(title, link) {
	this.deferred = new $.Deferred();
	this.options = {
		title : title,
		url : '/cms/chunk/insert_url',
		id : 'b-linkpicker',
		width : 600
	};

	this.link = link? link : {};

	boomLinkPicker.prototype._bind = function() {
		var linkPicker = this,
			type_selector = this.dialog.contents.find('#b-chunk-linkset-addlink-external-type'),
			external_url = this.dialog.contents.find('#boom-chunk-linkset-addlink-external-url');

		if (this.link.rid == -1 || this.link.rid == "") {
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

		type_selector.on('change', function() {
			if (external_url.val() == 'http://') {
				external_url.val('');
			}
		});

		this.dialog.contents.find('.boom-tree').pageTree({
			onPageSelect : function(page) {
				linkPicker.link = page;
				linkPicker.pick();
				linkPicker.dialog.close();
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

	boomLinkPicker.prototype.open = function() {
		var linkPicker = this;

		this.options.onLoad = function() {
			linkPicker._bind();
		};
		this.dialog = new boomDialog(this.options)
			.done(function() {
				linkPicker.pick();
			});

		return this.deferred;
	};

	boomLinkPicker.prototype.pick = function() {
		if (this.link.page_id == -1 || this.link.page_id == undefined) {
			this.link = this.getExternalLink();
		}

		this.deferred.resolve(this.link);
	};

	return this.open();
};
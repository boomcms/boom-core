function boomLinkPicker(title, link) {
	this.deferred = new $.Deferred();
	this.options = {
		title : title,
		url : '/cms/chunk/insert_url',
		id : 'b-linkpicker',
		width : 600,
		link : link? link : {}
	};

	boomLinkPicker.prototype._bind = function() {
		var linkPicker = this,
			type_selector = this.dialog.contents.find('#b-chunk-linkset-addlink-external-type'),
			external_url = this.dialog.contents.find('#boom-chunk-linkset-addlink-external-url');

		if (this.options.link.rid == -1 || this.options.link.rid == "") {
			var url = this.options.link.url;

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
				external_url.val( url );
				$( 'a[href=#boom-chunk-linkset-addlink-external]' ).trigger('click');
			}

		}

		type_selector.on('change', function() {
			if (external_url.val() == 'http://') {
				external_url.val('');
			}
		});

		this.dialog.contents.find('.boom-tree').pageTree({
			onPageSelect : function(page) {
				linkPicker.pick({
					page_id : page.page_rid
				});
			}
		});
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
		if (link.rid == -1 || link.rid == "") {
			var url = link_text = $( '#boom-chunk-linkset-addlink-external-url' ).val();

			switch($('#b-chunk-linkset-addlink-external-type').val()) {
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

			link.url = url;
			link.title = link_text;
		}

		this.deferred.resolve(link);
		this.dialog.close();
	};

	return this.open();
};
(function(wysihtml5) {
	wysihtml5.commands.createBoomLink = {
		edit : function(composer) {
			this._select_link(composer);
		},

		exec: function(composer, command, value) {
			var anchors = this.state(composer, this);

			if (anchors) {
				$(composer.selection.getSelectedNode()).unwrap();
			} else {
				this._select_link(composer);
			}
		},

		state: function(composer) {
			return wysihtml5.commands.formatInline.state(composer, this, "A");
		},

		_select_link : function(composer) {
			var self = this,
				 existing_link = this.state(composer),
				opts = {};
			var bm = composer.selection.getBookmark();

			if (existing_link) {
				var link = new boomLink(existing_link.href, 0, (existing_link.textContent || existing_link.innerText));
				opts.link = link;
			}

			$(composer).trigger('before:boomdialog');

			 new boomLinkPicker(new boomLink(link))
				.done(function(link) {
					var url = link.getUrl(),
						page_id = link.getPageId();

					if (existing_link) {
						$(existing_link)
							.attr('href', link.getUrl())
							.attr('title', '')
							.text($(existing_link).text().replace(existing_link.href, url));
					} else {
						composer.selection.setBookmark(bm);

						if (page_id) {
							composer.commands.exec("createLink", { href: url, rel: page_id, title: '', text: link.title});
						} else {
							var text = url.replace('mailto:', '').replace('tel:', '');

							composer.commands.exec("createLink", { href: url, title: '', text: text});
						}
					}

				})
				.always(function() {
					$(composer).trigger('after:boomdialog');
				});
		}
	};
})(wysihtml5);
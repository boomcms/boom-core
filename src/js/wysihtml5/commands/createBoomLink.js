(function(wysihtml5) {
	wysihtml5.commands.createBoomLink = {
		exec: function(composer, command, value) {
			this._select_link(composer);
		},

		removeLink: function(composer) {
			var anchors = this.state(composer, this);

			if (anchors) {
				$(composer.selection.getSelectedNode()).unwrap();
			}
		},

		state: function(composer) {
			return wysihtml5.commands.formatInline.state(composer, this, "A");
		},

		_select_link: function(composer) {
			var self = this,
				 existing_link = this.state(composer)[0],
				opts = {},
				link,
				bm = composer.selection.getBookmark();

			if (existing_link) {
				var link = new BoomCMS.Link(existing_link.href, 0, (existing_link.textContent || existing_link.innerText));
				opts.link = link;
			} else {
				link = new BoomCMS.Link();
			}

			$(composer).trigger('before:boomdialog');

			new BoomCMS.LinkPicker(link, {remove: link.getUrl() != ''})
				.done(function(link) {
					var url = link.getUrl();

					if (!url) {
						return self.removeLink(composer);
					}

					if (existing_link) {
						$(existing_link)
							.attr('href', link.getUrl())
							.text($(existing_link).text().replace(existing_link.href, url));
					} else {
						composer.selection.setBookmark(bm);
						composer.commands.exec("createLink", {href: url});
					}
				})
				.always(function() {
					$(composer).trigger('after:boomdialog');
				});
		}
	};
})(wysihtml5);
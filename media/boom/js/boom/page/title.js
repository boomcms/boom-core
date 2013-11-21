/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {
	edit : function() {
		var self = this;
		var old_html = this.element.html();

		self._bring_forward();

		if (this.isUntitled()) {
			this.element.text('');
		}

		$('body').editor('edit', self.element)
			.fail(function() {
				self.element.html(old_html).show();
				self.destroy();

				$.boom.page.editor.bind();
			})
			.done(function() {
				var title = self.element.text();

				if (title != '' && title != old_html) {
					self.insert(title);
				}
			})
			.always(function() {
				if (self.element.text() == '' ) {
					self.element.html(old_html);
				}

				self._send_back();
				self.bind();
			});
	},

	insert : function(html) {
		this.element.html(html);
		this._save();
	},

	isUntitled : function() {
		return this.element.text() == 'Untitled';
	},

	_save : function() {
		$.boom.loader.show();

		$.post('/cms/page/version/title/' + $.boom.page.id, {
			csrf : $.boom.options.csrf,
			title : this.element.html()
		})
		.always(function() {
			$.boom.loader.hide();
		})
		.done(function(response) {
			try {
				var data = $.parseJSON(response);
			} catch (e) {};

			if (typeof data =='object' && data.location) {
				$.boom.growl.show('Page URL changed, redirecting to new URL.');

				setTimeout(function() {
					top.location = data.location;
				}, 1000);
			} else {
				$.boom.page.toolbar.status.set(response);
			}
		})
	}
});
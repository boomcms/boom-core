/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {

	_bind : function() {
		var self = this;

		this.element
			.on('click', function(event) {
				self.edit();
			})
			.addClass('b-editable');
	},

	_create : function() {
		this._bind();
	},

	edit : function() {
		var self = this;
		var old_html = this.element.html();

		self._bring_forward();
		self._unbind();

		if (this.isUntitled()) {
			this.element.text('');
		}

		$('body').editor('edit', self.element)
			.fail(function() {
				self.element.html(old_html).show();
				self.destroy();
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
				self._bind();
			});
	},

	_init : function() {

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

		$.post('/cms/page/version/title/' + $.boom.page.options.id, {
			csrf : $.boom.options.csrf,
			title : this.element.html()
		})
		.always(function() {
			$.boom.loader.hide();
		})
		.done(function(response) {
			var data = $.parseJSON(response);

			if (data.location) {
				$.boom.growl.show('Page URL changed, redirecting to new URL.');

				setTimeout(function() {
					top.location = data.location;
				}, 1000);
			} else {
				$.boom.page.setStatus(data.status);
			}
		})
	},

	_unbind : function() {
		this.element
			.unbind('click')
			.removeClass('b-editable');
	}
});
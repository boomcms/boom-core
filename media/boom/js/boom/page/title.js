/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {
	max_length : 70,

	bind : function() {
		$.ui.chunk.prototype.bind.call(this);

		var self = this,
			element = this.element;

		this.element
			.on('keydown change paste', function() {
				setTimeout(function() {
					self._update_length_counter(element.text().length)
				}, 0);
			})
			.on('click', function() {
				element.focus();
			})
			.on('blur', function() {
				$('body').editor('apply', element);
			});
	},

	_create_length_counter : function() {
		top.$('body').append('<div id="b-title-length"><span></span></div>');

		var offset = this.element.offset();

		top.$('#b-title-length')
			.css({
				top : offset.top + 'px',
				left : (offset.left - 60) + 'px',
			});

		$('<p><a href="#" id="b-title-help">What is this?</a></p>')
			.appendTo(top.$('#b-title-length'))
			.on('click', 'a', function(e) {
				e.preventDefault();

				$.boom.dialog.open({
					url : '/media/boom/html/help/title_length.html',
					width : '70%'
				});
			});

		this._update_length_counter(this.element.text().length);
	},

	edit : function() {
		var self = this;
		var old_html = this.element.html();
					
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

				top.$('#b-title-length').remove();

				self.bind();
			});

		this._create_length_counter();
	},

	_get_counter_color_for_length : function(length) {
		if (length >= this.max_length) {
			return 'red';
		} else if (length >= this.max_length * 0.9) {
			return 'orange';
		} else if (length >= this.max_length * 0.8) {
			return 'yellow';
		}

		return 'green';
	},

	insert : function(html) {
		this.element.html(html);
		this._save();
	},

	isUntitled : function() {
		return this.element.text() == 'Untitled';
	},

	_save : function() {
		this.options.currentPage.setTitle(this.element.html())
			.done(function(response) {
				try {
					var data = $.parseJSON(response);
				} catch (e) {};

				if (typeof data =='object' && data.location) {
					$.boom.dialog.confirm('Page URL changed', "Because you've set a page title for the first time the URL of this page has been updated to reflect the new title.<br /><br />Would you like to reload the page using the new URL?<br /><br />You can continue editing the page without reloading.")
						.done(function() {
							top.location = data.location;
						});
				} else {
					$.boom.growl.show('Page title saved.');
					$.boom.page.toolbar.status.set(response);
				}
			});
	},

	_update_length_counter : function(length) {
		top.$('#b-title-length')
			.find('span')
			.text(length)
			.end()
			.css('background-color', this._get_counter_color_for_length(length));

		var disable_accept_button = (length >= this.max_length || length === 0)? true : false;
		var opacity = disable_accept_button? '.35' : 1;
		top.$('#b-editor-accept')
			.prop('disabled', disable_accept_button)
			.css('opacity', opacity);
	}
});
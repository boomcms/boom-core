/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {
	max_length : 70,

	saveOnBlur : false,

	addBlurEvent : function() {
		var element = this.element,
			title = this;

		this.saveOnBlur = true;

		element.on('blur', function() {
			if (title.saveOnBlur) {
				$('body').editor('apply', element);
			}
		});
	},

	bind : function() {
		$.ui.chunk.prototype.bind.call(this);

		var self = this,
			element = this.element;

		this.element
			.on('keydown change paste', function() {
				setTimeout(function() {
					self._update_length_counter(element.text().length);
				}, 0);
			})
			.on('click', function() {
				element.focus();
			});

		this.addBlurEvent();
	},

	_create_length_counter : function() {
		top.$('body').append('<div id="b-title-length"><span></span></div>');

		var offset = this.element.offset(),
			title = this;

		top.$('#b-title-length')
			.css({
				top : offset.top + 'px',
				left : (offset.left - 60) + 'px',
			});

		$('<p><a href="#" id="b-title-help">What is this?</a></p>')
			.appendTo(top.$('#b-title-length'))
			.on('mousedown', 'a', function(e) {
				e.preventDefault();

				title.toggleBlurEvent();
			})
			.on('keydown', function(e) {
				if (e.which == 13) {
					title.toggleBlurEvent();
					title.openHelp();
				}
			})
			.on('click', function(e) {
				e.preventDefault();

				title.openHelp();
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
					self._save(title, old_html);
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

	isUntitled : function() {
		return this.element.text() == 'Untitled';
	},

	openHelp : function() {
		var title = this;

		new boomDialog({
			url : '/media/boom/html/help/title_length.html',
			width : '600px',
			cancelButton: false
		}).always(function() {
			title.toggleBlurEvent();
			title.element.focus();
		});
	},

	_save : function(title, old_title) {
		this.options.currentPage.setTitle(title)
			.done(function(response) {
				try {
					var data = $.parseJSON(response);
				} catch (e) {};

				if (typeof data == 'object' && data.location) {
					var history = new boomHistory();

					if (history.isSupported()) {
						history.replaceState({}, title, data.location);
						new boomNotification('Page title saved.');
						$.boom.page.toolbar.status.set(response);
					} else {
						var confirmation = new boomConfirmation('Page URL changed', "Because you've set a page title for the first time the URL of this page has been updated to reflect the new title.<br /><br />Would you like to reload the page using the new URL?<br /><br />You can continue editing the page without reloading.");
						confirmation
							.done(function() {
								top.location = data.location;
							});
					}
				} else {
					new boomNotification('Page title saved.');
					$.boom.page.toolbar.status.set(response);
				}

				var page_title = top.$('title').text().replace(old_title, title);
				top.$('title').text(page_title);
			});
	},

	toggleBlurEvent : function() {
		if (this.saveOnBlur) {
			this.saveOnBlur = false;

			this.element.unbind('blur');
		} else {
			this.addBlurEvent();
		}
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
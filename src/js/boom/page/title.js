/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {
	lengthCounterCreated : false,

	max_length : 70,

	saveOnBlur : false,

	bind : function() {
		$.ui.chunk.prototype.bind.call(this);

		var self = this,
			element = this.element,
			old_text = element.text();

		this.element.textEditor({
			edit : function() {
				var title = self.element.text().trim();

				if (title != '' && title != old_text) {
					self.updatePageTitle(old_text, title);
					self._save(title, old_text);
				}

				old_text = title;

				self.lengthCounterCreated = false;
				$(top.document).find('#b-title-length').remove();
			}
		});

		this.element
			.on('keydown change paste', function() {
				var oldText = element.text();

				setTimeout(function() {
					self.updatePageTitle(oldText, element.text());
					self._update_length_counter(element.text().length);
				}, 0);
			})
			.on('focus', function() {
				if ( ! self.lengthCounterCreated) {
					self._create_length_counter();
					self.lengthCounterCreated = true;
				}

				if (self.isUntitled()) {
					self.element.text('');
				}
			});
	},

	_create_length_counter : function() {
		var $counter = $('<div id="b-title-length"><span></span></div>');
		
		$(top.document)
				.find('body')
				.first()
				.append($counter);

		var offset = this.element.offset(),
			title = this;

		$counter
			.css({
				top : offset.top + 'px',
				left : (offset.left - 110) + 'px'
			});

		$('<p><a href="#" id="b-title-help">What is this?</a></p>')
			.appendTo($counter)
			.on('mousedown', 'a', function() {
				title.element.textEditor('disableAutoSave');
			})
			.on('keydown', function(e) {
				if (e.which == 13) {
					title.openHelp();
				}
			})
			.on('click', function(e) {
				e.preventDefault();

				title.openHelp();
			});

		this._update_length_counter(this.element.text().length);
	},

	edit : function() {},

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
			url : '/vendor/boomcms/boom-core/html/help/title_length.html',
			width : '600px',
			cancelButton: false
		}).always(function() {
			title.element.textEditor('enableAutoSave');
			title.element.focus();
		});
	},

	_save : function(title, old_title) {
		this.options.currentPage.setTitle(title)
			.done(function(data) {
				if (typeof data == 'object' && data.location) {
					var history = new boomHistory();

					if (history.isSupported()) {
						history.replaceState({}, title, data.location);
						new boomNotification('Page title saved.');
						$.boom.page.toolbar.status.set(data.status);
					} else {
						var confirmation = new boomConfirmation('Page URL changed', "Because you've set a page title for the first time the URL of this page has been updated to reflect the new title.<br /><br />Would you like to reload the page using the new URL?<br /><br />You can continue editing the page without reloading.");
						confirmation
							.done(function() {
								top.location = data.location;
							});
					}
				} else {
					new boomNotification('Page title saved.');
					$.boom.page.toolbar.status.set(data.status);
				}

				var page_title = top.$('title').text().replace(old_title, title);
				top.$('title').text(page_title);
			});
	},
	
	updatePageTitle : function(oldTitle, newTitle) {
		top.document.title = top.document.title.replace(oldTitle, newTitle);
	},

	_update_length_counter : function(length) {
		$(top.document).find('#b-title-length')
			.find('span')
			.text(length)
			.end()
			.css('background-color', this._get_counter_color_for_length(length));

		var disable_accept_button = (length >= this.max_length || length === 0)? true : false;
		var opacity = disable_accept_button? '.35' : 1;
		$('.b-editor-accept')
			.prop('disabled', disable_accept_button)
			.css('opacity', opacity);
	},

	unbind : function() {}
});
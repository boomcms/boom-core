/**
 * TODO: Tell someone off for trying to blank a page title or writing an essay in the title.
 */
$.widget('boom.pageTitle', $.ui.chunk, {
	max_length : 70,

	bind : function() {
		$.ui.chunk.prototype.bind.call(this);

		var self = this;
		this.element.on('keydown change paste', function() {
			var $el = $(this);

			setTimeout(function() {
				self._update_length_counter($el.text().length)
			}, 0);
		});
	},

	_create_length_counter : function() {
		top.$('body').append('<div id="b-title-length"><span></span></div>');

		var offset = this.element.offset();

		top.$('#b-title-length')
			.css({
				top : offset.top + 'px',
				left : (offset.left - 60) + 'px',
				position : 'absolute',
				'z-index' : 1001,
				background : '#ffffff',
				width : '50px',
				height : '80px',
				'font-size' : '40px',
				'text-align' : 'center',
				overflow : 'hidden'
			})
			.find('span')
			.css('line-height', '40px');

		$('<p><a href="#" id="b-title-help">What is this?</a></p>')
			.appendTo(top.$('#b-title-length'))
			.css({
				'line-height' : '12px',
				'margin-top' : '5px',
				'font-size' : '12px'
			})
			.find('a')
			.css('color', '#000')
			.on('click', function(e) {
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

		self._bring_forward();

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

				self._send_back();
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
				$.boom.growl.show('Page title saved.');
				$.boom.page.toolbar.status.set(response);
			}
		})
	},

	_update_length_counter : function(length) {
		top.$('#b-title-length')
			.find('span')
			.text(length)
			.end()
			.css('background-color', this._get_counter_color_for_length(length));

		var disable_accept_button = (length >= this.max_length)? true : false;
		var opacity = disable_accept_button? '.35' : 1;
		top.$('#b-editor-accept')
			.prop('disabled', disable_accept_button)
			.css('opacity', opacity);
	}
});
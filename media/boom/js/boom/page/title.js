$.widget('boom.pageTitle', $.ui.chunk, {

	$el : null,

	_bind : function() {
		var self = this;

		this.$el
			.on('click', function(event) {
				self.edit();
			});
	},

	_create : function() {
		this.$el = $(this.element);

		this._bind();
	},

	edit : function() {
		var self = this;
		var old_html = this.$el.html();

		self._bring_forward();
		self._unbind();

		$('body').editor('edit', self.$el)
			.fail(function() {
				self.element.html(old_html).show();
				self.destroy();
			})
			.done(function(html) {
				if (html != old_html) {
					self.insert(html);
				}
			})
			.always(function() {
				if (self.$el.text() == '' ) {
					self.$el.html(old_html);
				}

				self._send_back();
				self._bind();
			});
	},

	insert : function(html) {
		this.$el.html(html);
		this._save();
	},

	_save : function() {
		$.boom.loader.show();

		$.post('/cms/page/version/title/' + $.boom.page.options.id, {
			csrf : $.boom.options.csrf,
			title : this.$el.html()
		})
		.always(function() {
			$.boom.loader.hide();
		});
	},

	_unbind : function() {
		this.$el.unbind('click');
	}
});
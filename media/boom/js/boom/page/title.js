$.widget('boom.pageTitle', $.ui.chunkText, {

	$el : null,

	_bind : function() {
		this.$el
			.on('click', function(event) {
				$.boom.page.slots.edit(event, this, {type : 'text'}, {});
				return false;
			});
	},

	_create : function() {
		this.$el = $(this.element);

		this._bind();
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
	}
});
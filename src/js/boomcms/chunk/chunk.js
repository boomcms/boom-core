/**
@fileOverview jQuery UI widgets for editable slots.
*/
/**
@namespace
@name $.ui
*/

/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('ui.chunk',

	/**
	@lends $.ui.chunk
	*/
	{

	edited : false,

	bind: function() {
		var self = this;

		this.element
			.addClass('b-editable')
			.unbind('click')
			.on('click', function(e) {
				self.triggerEdit(e);
			})
			.on('keydown', function(e) {
				if (e.which === 13) {
					self.triggerEdit(e);
				}
			})
			.attr('tabindex', 0);
	},

	_create: function() {
		this.bind();
	},

	destroy: function() {
		this.bind();
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html: function(html) {
		var $html = $(html);

		this.element.replaceWith($html);
		this.element = $html;

		this.bind();

		top.$.event.trigger({
			type: "boomcms:chunkload",
			chunk: {
				type: this.options.type,
				name: this.options.name,
				html: $html[0]
			},
			target: this.element[0]
		});
	},

	remove: function() {
		var self = this,
			chunk = new boomChunk(this.options.currentPage.id, this.options.type, this.options.name);

		return chunk.delete(this.options.template)
			.done(function(data) {
				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				new boomNotification("Page content saved");
			});
	},

	_save: function(data) {
		var self = this,
			chunk = new boomChunk(this.options.currentPage.id, this.options.type, this.options.name),
			data = data? data : this.getData();

		data.template = this.options.template;

		return chunk.save(data)
			.done(function(data) {
				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				new boomNotification("Page content saved");
			});
	},

	triggerEdit: function(e) {
		e.preventDefault();
		e.stopPropagation();

		this.edit();
		this.unbind();
	},

	unbind: function() {
		this.element
			.unbind('click')
			.unbind('keydown');
	}
});
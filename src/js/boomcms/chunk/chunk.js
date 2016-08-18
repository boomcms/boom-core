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
			.addClass(BoomCMS.editableClass)
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
				window.BoomCMS.page.toolbar.status.set(data.status);
				new boomNotification("Page content saved").show();
			});
	},

	_save: function(data) {
		var self = this,
			chunk = new boomChunk(this.options.currentPage.id, this.options.type, this.options.name),
			data = data? data : this.getData();

		data.template = this.options.template;
		data.chunkId = this.options.chunkId;

		return chunk.save(data)
			.done(function(data) {
				self.options.chunkId = data.chunkId;

				self._update_html(data.html);
				window.BoomCMS.page.toolbar.status.set(data.status);

				new boomNotification("Page content saved").show();
			})
			.fail(function(response) {
				if (response.responseJSON.error === 'conflict') {
					self.resolveConflict(response.responseJSON, data);
				}
			});
	},

	/**
	 * Replace the HTML of the chunk during conflict resolution.
	 * 
	 * Most chunks will just call _update_html()
	 * However, text chunks define an empty _update_html() method
	 * So this method needs to do something useful there.
	 *
	 * @param {string} html
	 * @returns {undefined}
	 */
	_replace_html: function(html) {
		this._update_html(html);
	},

	resolveConflict: function(data, saveData) {
		var chunk = this,
			dialog = new boomDialog({
				msg: data.html,
				closeButton: false,
				width: 500,
				title: 'Save conflict',
				onLoad: function() {
					dialog.contents
						.on('click', '#b-conflict-reload', function() {
							chunk.options.chunkId = data.chunkId;
							chunk._replace_html(data.chunk);

							window.BoomCMS.page.toolbar.status.set(data.status);

							dialog.cancel();
						})
						.on('click', '#b-conflict-overwrite', function() {
							saveData.force = 1;

							chunk._save(saveData);

							dialog.cancel();
						})
						.on('click', '#b-conflict-inspect', function() {
							window.open(top.location);
						});
				}
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
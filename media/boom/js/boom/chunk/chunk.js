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

	bind : function() {
		var self = this;

		this.element
			.addClass('b-editable')
			.unbind('click')
			.on('click', function(event) {
				event.preventDefault();
				event.stopPropagation();

				self.unbind();

				self.edit();

				return false;
			})
			.on('keydown', function(event) {
				switch(event.which) {
					case 13:
						self.edit()
					break;
				}
			});
	},

	_create : function(){
		$.boom.log( 'CHUNK CREATE' );

		this.bind();
	},

	destroy : function() {
		this.bind();
	},

	/**
	Insert edited chunk content back into the page.
	@function
	*/
	_update_html : function(html) {
		var $html = $(html);
		this.element.replaceWith($html);
		this.element = $($html[0]);

		this.bind();
	},

	remove : function() {
		var self = this,
			chunk = new boomChunk(this.options.currentPage.id, this.options.type, this.options.name);

		return chunk.delete()
			.done(function(response) {
				var data = $.parseJSON(response);

				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				$.boom.growl.show("Page content saved");
			});
	},

	_save : function() {
		var self = this,
			chunk = new boomChunk(this.options.currentPage.id, this.options.type, this.options.name),
			data = this.getData();

		data.template = this.options.template;

		return chunk.save(data)
			.done(function(response) {
				var data = $.parseJSON(response);

				self._update_html(data.html);
				$.boom.page.toolbar.status.set(data.status);
				$.boom.growl.show("Page content saved");
			});
	},

	/**
	Bring the slot UI forward, above all other page elements.
	@function
	*/
	_bring_forward : function() {

		this.element.css( {
			'z-index' : 1000,
			'position' : 'relative'
		});
		top.$( 'body' ).prepend( '<div class="overlay"></div>' );

	},
	/**
	Drop the slot UI back into its natural place in the page z-index stack.
	@function
	*/
	_send_back : function() {
		this.element.removeAttr( 'style' );
		top.$( 'div.overlay' ).remove();
	},

	unbind : function() {
		this.element
			.unbind('click')
			.removeClass('b-editable');
	}
});
/**
Editable text slots
@class
@name chunkText
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkText', $.ui.chunk,

	/**
	@lends $.ui.chunkText
	*/
	{

	content : '',

	_create : function() {
		var element = this.element.find('.chunk-text');
		this.element = (element.length)? $(element[0]) : this.element;

		$.ui.chunk.prototype._create.call(this);
	},

	bind : function() {
		var element = this.element,
			self = this,
			old_html = element.html();

		$.ui.chunk.prototype.bind.call(this);

		element.textEditor({
			edit : function() {
				var edited = old_html != self.element.html();

				if ( ! self.hasContent()) {
					self.remove();
					self.element.text('Default text.');
				} else if (edited == true) {
					self._save();
				}
			}
		});
	},

	edit : function() {},

	/**
	Get the chunk HTML, escaped and cleaned.
	*/
	getData : function(){
		var $content = this.element.find('.slot-content');

		this.content = ($content.length)? $content.html() : this.element.html();

		return {
			text : this.content
		};
	},

	hasContent : function() {
		return this.element.text() !== '' || this.element.find('img').length > 0;
	},

	unbind : function() {
		this.element.unbind('click');
	},

	_update_html : function() {}
});
/**
@class
@name chunkTag
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkTag', $.ui.chunk,
	/**
	@lends $.ui.chunkTag
	*/
	{

	_create : function() {
		this.tag = this.element.attr('data-boom-tag')? this.element.attr('data-boom-tag') : 0;

		$.ui.chunk.prototype._create.call(this);
	},

	edit : function(){
		$.boom.log('Tag chunk edit');

		var self = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/' + this.options.currentPage.id + '/edit?type=tag&slotname=' + this.options.name,
			width: 400,
			title: 'Select tag',
			onLoad : function() {
				self.dialog.contents.find('#b-tags-add-name').assetTagAutocomplete({
					complete : function(e, data) {
						self.tag = data.tag;
						self.dialog.contents.find('#b-selected p').text(data.tag);
					}
				});
			}
		})
		.always(function() {
			self.bind();
		});
	},

	getData: function() {
		return {tag : this.tag};
	},

	insert : function(tag) {
		this.tag = tag;

		return this._save();
	}
});
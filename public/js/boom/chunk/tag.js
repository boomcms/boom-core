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


	/**
	Open a dialog with a tree control to pick a page for the current feature
	and a button to remove any existing page without replacing it.
	*/
	edit : function(){
		$.boom.log('Tag chunk slot edit');

		var self = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/tag/edit/' + this.options.currentPage.id + '?tag=' + this.tag,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			title: 'Select tag',
			onLoad : function() {
				self.dialog.contents.find('#b-tags-add-name').assetTagAutocomplete({
					type : 1,
					complete : function(e, data) {
						self.tag = data.tag;
						self.dialog.contents.find('#b-selected p').text(data.tag);
					}
				});
			}
		}).done(function() {
			self.insert(tag);
		})
		.always(function() {
			self.bind();
		});
	},

	getData: function() {
		return {tag : this.tag};
	},

	/**
	@param {Int} id Tag ID
	*/
	insert : function(tag) {
		this.tag = tag;

		return this._save();
	}
});
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
		this.tag_id = this.element.attr('data-boom-tag_id')? this.element.attr('data-boom-tag_id') : 0;

		$.ui.chunk.prototype._create.call(this);
	},


	/**
	Open a dialog with a tree control to pick a page for the current feature
	and a button to remove any existing page without replacing it.
	*/
	edit : function(){
		$.boom.log('Tag chunk slot edit');

		var self = this,
			tag_id;

		this.dialog = new boomDialog({
			url: '/cms/chunk/tag/edit/' + this.options.currentPage.id + '?tag_id=' + this.tag_id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			title: 'Select tag',
			onLoad : function() {
				var $dialog = $(this);
				$('#b-tags-add-name').tagAutocompleter({
					type : 1,
					complete : function(event, tag) {
						tag_id = tag.id;

						$dialog.find('#b-selected p').text(tag.name);
					}
				});
			},
			callback : function() {
				self.insert(tag_id);
			},
			destroy: function() {
				self.destroy();
			},
			open: function() {
				
			}
		});
	},

	getData: function() {
		return {tag_id : this.tag_id};
	},

	/**
	@param {Int} id Tag ID
	*/
	insert : function(id) {
		this.tag_id = id;

		return this._save();
	}
});
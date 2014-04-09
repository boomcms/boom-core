/**
@class
@name chunkFeature
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkFeature', $.ui.chunk,
	/**
	@lends $.ui.chunkFeature
	*/
	{

	/**
	Open a dialog with a tree control to pick a page for the current feature
	and a button to remove any existing page without replacing it.
	*/
	edit : function(){

		$.boom.log('Feature chunk slot edit');

		var self = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/feature/edit/' + this.options.currentPage.id,
			width: 400,
			id: self.element[0].id + '-boom-dialog',
			title: 'Page feature',
			onLoad : function() {

				self.dialog.find( '.boom-tree' ).pageTree({
					onPageSelect : function(page) {
						self.insert( page.page_id );

						$.boom.dialog.destroy(self.dialog);
					}
				});

			},
			destroy: function(){
				self.destroy();
			},
			open: function(){

				if ( self.options.id > 0 ) {

					$( 'input[name=parent_id]' ).val( self.options.id );

					var button = $('<button />')
					.addClass('b-button ui-helper-left')
					.text('Remove')
					.button({
						text: false,
						icons: { primary : 'b-button-icon b-button-icon-delete' }
					})
					.click(function(){

						$.boom.dialog.destroy(self.dialog);

						self.remove();
					});

					$(this).dialog('widget')
						.find('.ui-dialog-buttonpane')
						.prepend( button );
				}
			}
		});
	},

	/**
	Get the RID for this feature box.
	@returns {Int} Page RID
	*/
	getData: function(){
		return {target_page_id : this.options.id};
	},

	/**
	Insert the selected page into the DOM as a feature box.
	@param {Int} rid Page RID
	*/
	insert : function(rid){
		this.options.id = rid;

		return this._save();
	},
});
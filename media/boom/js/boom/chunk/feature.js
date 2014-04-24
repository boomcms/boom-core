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

	_bind : function() {
		if (this.options.id > 0) {
			this.dialog.contents.find('input[name=parent_id]').val(this.options.id);

			var button = $('<button />')
				.addClass('b-button ui-helper-left')
				.text('Remove')
				.button({
					icons: {primary : 'b-button-icon b-button-icon-delete'}
				})
				.click(function() {
					this.remove();
					this.dialog.close();
				});

			this.dialog.contents.dialog('widget')
				.find('.ui-dialog-buttonpane')
				.prepend(button);
		}
	},

	edit : function() {
		var featureEditor = this;

		this.confirmation = new boomDialog({
			title : 'Edit feature?',
			msg : '<p>You clicked on a feature box.</p><p>Do you want to visit the featured page or edit the feature?</p>',
			closeButton : false,
			buttons : [
				{
					text : 'Visit page',
					class : 'b-button b-button-textonly',
					click : function() {
						featureEditor.viewTarget();
					}
				},
				{
					text : 'Edit feature',
					class : 'b-button b-button-textonly',
					click : function() {
						featureEditor.editTarget();
					}
				}
			]
		});
	},

	editTarget : function() {
		$.boom.log('Feature chunk slot edit');

		var self = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/feature/edit/' + this.options.currentPage.id,
			width: 400,
			closeButton : false,
			position : {my: "bottom", at: "center", of: window},
			title: 'Page feature',
			onLoad : function() {
				self.confirmation && self.confirmation.close();

				self.dialog.contents.find('.boom-tree').pageTree({
					onPageSelect : function(page) {
						self.insert(page.page_id);
						self.dialog.close();
					}
				});
			},
			open: function() {
				self._bind();
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

	getTargetUrl : function() {
		return this.element.is('a')? this.element.attr('href') : this.element.find('a').attr('href');
	},

	/**
	Insert the selected page into the DOM as a feature box.
	@param {Int} rid Page RID
	*/
	insert : function(rid){
		this.options.id = rid;

		return this._save();
	},

	viewTarget : function() {
		top.window.location = this.getTargetUrl();
	}
});
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
		var featureChunk = this;

		if (this.options.id > 0) {
			this.dialog.contents.find('input[name=parent_id]').val(this.options.id);

			var button = $('<button />')
				.addClass('b-button ui-helper-left b-button-withtext')
				.text('Remove featured page')
				.button({
					icons: {primary : 'b-button-icon b-button-icon-delete'}
				})
				.click(function() {
					featureChunk.remove();
					featureChunk.dialog.close();
				});

			this.dialog.contents.dialog('widget')
				.find('.ui-dialog-buttonpane')
				.prepend(button);
		} else {
			this.dialog.contents.find('input[name=parent_id]').val('');
		}
	},

	edit : function() {
		var featureEditor = this;

		if (this.options.id > 0 && this.getTargetUrl()) {
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
						text: 'Remove the featured page',
						class: 'b-button b-button-textonly',
						click: function() {
							featureEditor.remove();
							featureEditor.confirmation.close();
						}
					},
					{
						text : 'Change the featured page',
						class : 'b-button b-button-textonly',
						click : function() {
							featureEditor.editTarget();
						}
					}
				]
			})
			.fail(function() {
				featureEditor.bind();
			});
		} else {
			this.editTarget();
		}
	},

	editTarget : function() {
		var featureEditor = this;

		new boomLinkPicker(new boomLink(null, this.options.currentPage.id), {
				external: false,
				asset: false
			})
			.done(function(link) {
				if (typeof(featureEditor.confirmation) !== 'undefined') {
					featureEditor.confirmation.close();
				}

				featureEditor.insert(link.getPageId());
			})
			.fail(function() {
				featureEditor.bind();
			});
	},

	getData: function() {
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
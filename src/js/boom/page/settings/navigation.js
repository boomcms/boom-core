$.widget('boom.pageSettingsNavigation', {
	_create: function() {
		this.element.find('.boom-tree').pageTree({
			active: this.element.find('input[name=parent_id]').val(),
			onPageSelect : function(page) {
				$('input[name=parent_id]').val(page.pageId);
			}
		});
	}
});
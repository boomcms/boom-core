$.widget('boom.pageSettingsHistory', $.boom.pageSettingsDefault, {
	bind: function() {
		this.element.on('click', 'a[data-timestamp]', function() {
			BoomCMS.Editor.setTime($(this).attr('data-timestamp')).done(function(response) {
				top.location.reload();
			});
		});
	}
});
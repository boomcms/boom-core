$.widget('boom.pageSettingsAcl', $.boom.pageSettingsDefault, {
	bind: function() {
		var settings = this,
			page = this.options.page;

		this.element
			.find('select[multiple]')
			.chosen()
			.change(function(event, data) {
				if (typeof(data.selected) !== 'undefined') {
					return page.addAclGroup(data.selected);
				}

				return page.removeAclGroup(data.selected);
			});

		this.element.on('change', 'select[name=b-page-acl-toggle]', function() {
			settings.toggleGroups();

			page.setEnableAcl($(this).val() === '1');
		});

		this.toggleGroups();
	},

	toggleGroups: function() {
		var val = this.element.find('select[name=b-page-acl-toggle]').val(),
			$groups = this.element.find('#b-page-acl-groups');

		(val === '1') ? $groups.show() : $groups.hide();
	}
});
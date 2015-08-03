$.widget('boom.pageSettingsTemplate', {
	_create: function() {
		var templateEditor = this;

		this.showDetails();

		this.element.on('change', 'select', function() {
			templateEditor.template.showDetails();
		});
	},

	showDetails: function() {
		var $template = this.element.find('#template'),
			$description = this.element.find('#description'),
			$count = this.element.find('#count'),
			$selected = $template.find('option:selected'),
			description_text = $selected.data('description');

		if (description_text) {
			$description.show().find('p').html($selected.data('description'));
		} else {
			$description.hide();
		}

		$count.find('p').html($selected.data('count'));
	}
});
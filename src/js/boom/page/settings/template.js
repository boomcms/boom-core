$.widget('boom.pageSettingsTemplate', {
	_create: function() {
		var templateEditor = this,
			initial = this.element.find('select option:selected').val();

		this.showDetails();

		this.element
			.on('change', 'select', function() {
				templateEditor.showDetails();
			})
			.on('click', '.b-template-save', function(e) {
				e.preventDefault();

				templateEditor.options.page.setTemplate(templateEditor.element.find('select option:selected').val())
					.done(function() {
						new boomNotification('Page template updated');
						
						templateEditor._trigger('done');
					});
			})
			.on('click', '.b-template-cancel', function(e) {
				e.preventDefault();

				templateEditor.element.find('select').val(initial);
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
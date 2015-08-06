$.widget('boom.pageSettingsTemplate', {
	_create: function() {
		var templateEditor = this,
			$cancelButton = this.element.find('.b-template-cancel'),
			$saveButton = this.element.find('.b-template-save'),
			initial = this.element.find('select option:selected').val();

		this.showDetails();

		this.element
			.on('change', 'select', function() {
				templateEditor.showDetails();
				$cancelButton.removeAttr('disabled');
				$saveButton.removeAttr('disabled');
			});
			
		$saveButton
			.on('click', function(e) {
				e.preventDefault();

				templateEditor.options.page.setTemplate(templateEditor.element.find('select option:selected').val())
					.done(function() {
						new boomNotification('Page template updated');
				
						$cancelButton.attr('disabled', 'disabled');
						$saveButton.attr('disabled', 'disabled');
						
						templateEditor._trigger('done');
					});
			});
			
		$cancelButton
			.on('click', function(e) {
				e.preventDefault();

				templateEditor.element.find('select').val(initial);
		
				$cancelButton.attr('disabled', 'disabled');
				$saveButton.attr('disabled', 'disabled');
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
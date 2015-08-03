$.widget('boom.pageSettingsVisibility', {
	changed: false,
	deferred: new $.Deferred(),
	baseUrl: '/cms/page/settings/visibility/',

	bind: function() {
		var pageVisibilityEditor = this;

		this.element
			.on('change', 'input, select', function() {
				pageVisibilityEditor.changed = true;
			})
			.on('change', '#toggle-visible', function() {
				pageVisibilityEditor.toggleVisibleTo(this.checked);
			})
			.on('change', '#b-page-visible', function() {
				pageVisibilityEditor.toggleVisible($(this).find('option:selected').val() === '1');
			});

			this.toggleVisible(this.elements.visible.find('option:selected').val() === '1');
			this.toggleVisibleTo(this.elements.visibleToToggle.is(':checked'));
	},

	disableElements: function() {
		var elementsToDisable = ['visibleFrom', 'visibleTo', 'visibleToToggle'];

		for (var el in elementsToDisable) {
			this.elements[elementsToDisable[el]].attr('disabled', 'disabled');
		}
	},

	findElements: function() {
		var $el = this.element;

		this.elements = {
			visible: $el.find('#b-page-visible'),
			visibleFrom: $el.find('#visible-from'),
			visibleTo: $el.find('#visible-to'),
			visibleToToggle: $el.find('#toggle-visible')
		};
	},

	save: function() {
		var visibilityEditor = this;

		if (this.changed) {
			$.post(this.url, this.dialog.contents.find('form').serialize())
				.done(function(response) {
					new boomNotification('Page visibility saved');
					visibilityEditor.deferred.resolve(response);
				});
		}
	},

	toggleVisible: function(visible) {
		if (visible) {
			this.elements.visibleFrom.removeAttr('disabled');
			this.elements.visibleToToggle.removeAttr('disabled');
		} else {
			this.disableElements();
		}
	},

	toggleVisibleTo: function(disable) {
		var visibleTo = this.elements.visibleTo;

		if (disable) {
			visibleTo.removeAttr('disabled');

			if (visibleTo.val().toLowerCase().trim() == 'forever') {
				visibleTo.val('');
			}

			visibleTo.focus();
		} else {
			visibleTo.attr('disabled', 'disabled');

			if (! visibleTo.val().trim().length) {
				visibleTo.val('forever');
			}

			visibleTo.blur();
		}
	}
});
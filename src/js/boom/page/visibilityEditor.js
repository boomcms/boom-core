boomPageVisibilityEditor = function(page) {
	this.changed = false;
	this.deferred = new $.Deferred();
	this.page = page;
	this.url = '/cms/page/settings/visibility/' + this.page.id;

	boomPageVisibilityEditor.prototype.bind = function() {
		var pageVisibilityEditor = this;

		this.dialog.contents
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
	};

	boomPageVisibilityEditor.prototype.disableElements = function() {
		var elementsToDisable = ['visibleFrom', 'visibleTo', 'visibleToToggle'];

		for (var el in elementsToDisable) {
			this.elements[elementsToDisable[el]].attr('disabled', 'disabled');
		}
	};

	boomPageVisibilityEditor.prototype.findElements = function() {
		this.elements = {
			visible : this.dialog.contents.find('#b-page-visible'),
			visibleFrom : this.dialog.contents.find('#visible-from'),
			visibleTo : this.dialog.contents.find('#visible-to'),
			visibleToToggle : this.dialog.contents.find('#toggle-visible')
		};
	};

	boomPageVisibilityEditor.prototype.open = function() {
		var pageVisibilityEditor = this;

		this.dialog = new boomDialog({
			url : pageVisibilityEditor.url,
			title : 'Page visibility',
			closeButton: false,
			saveButton: true,
			open : function() {
				pageVisibilityEditor.findElements();
				pageVisibilityEditor.bind();
			}
		}).done(function() {
			pageVisibilityEditor.save();
		});

		return this.deferred;
	};

	boomPageVisibilityEditor.prototype.save = function() {
		var visibilityEditor = this;

		if (this.changed) {
			$.post(this.url, this.dialog.contents.find('form').serialize())
				.done(function(response) {
					new boomNotification('Page visibility saved');
					visibilityEditor.deferred.resolve(response);
				});
		}
	};

	boomPageVisibilityEditor.prototype.toggleVisible = function(visible) {
		if (visible) {
			this.elements.visibleFrom.removeAttr('disabled');
			this.elements.visibleToToggle.removeAttr('disabled');
		} else {
			this.disableElements();
		}
	};

	boomPageVisibilityEditor.prototype.toggleVisibleTo = function(disable) {
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
	};

	return this.open();
};
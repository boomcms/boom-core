function boomDialog(options) {
	var dialog = this;

	this.deferred = $.Deferred().always(function() {
		$(top.window).trigger('boom:dialog:close');

		dialog.cleanup();
	});

	this.buttons = {
		close: 'Okay',
		cancel: 'Cancel'
	};

	this.options = $.extend({
		width: 'auto',
		cancelButton : true,
		closeButton : true,
		autoOpen: true,
		modal: true,
		resizable: false,
		draggable: false,
		closeOnEscape: false,
		buttons : [],
		dialogClass : 'b-dialog',
		boomDialog: this
	}, options);

	boomDialog.prototype.always = function(callback) {
		dialog.deferred.always(callback);

		return this;
	};

	boomDialog.prototype.cancel = function() {
		dialog.deferred.rejectWith(this.dialog);
	};

	boomDialog.prototype.cleanup = function() {
		if (this.contents) {
			this.contents.remove();
			this.contents = null;
		}
	};

	boomDialog.prototype.close = function() {
		dialog.deferred.resolveWith(this.dialog);
	};

	boomDialog.prototype.configureButtons = function(options) {
		var dialog = this;

		for (var button in dialog.buttons) {
			if (options[button + 'Button']) {
				dialog.options.buttons.push({
					text: dialog.buttons[button],
					class: 'b-button-' + button,
					click: dialog[button]
				});
			}
		}
	};

	boomDialog.prototype.done = function(callback) {
		this.deferred.done(callback);

		return this;
	};

	boomDialog.prototype.fail = function(callback) {
		this.deferred.fail(callback);

		return this;
	};

	boomDialog.prototype.init = function() {
		var boomDialog = this;

		$(top.window).trigger('boom:dialog:open');

		this
			.contents
			.dialog(this.options)
			.ui();

		$(document).on('keydown', function(e) {
			if (e.which === $.ui.keyCode.ESCAPE) {
				boomDialog.cancel();
				e.stopPropagation();
			}
		});
	};

	boomDialog.prototype.open = function() {
		var self = this,
			$div = $('<div></div>');

		if (this.options.id) {
			$div.attr('id', this.options.id);
		}

		this.contents = $div.appendTo($(document).contents().find('body'));

		this.configureButtons(this.options);

		if (this.options.url && this.options.url.length) {
			if (this.contents.hasClass('ui-dialog-content')) {
				this.contents.dialog('open');
			} else {
				setTimeout(function() {
					self.contents.load(self.options.url, function(response, status, xhr) {
						if (xhr.status === 200) {
							self.init();

							if ($.isFunction(self.options.onLoad)) {
								self.options.onLoad.apply(self.dialog);
							}
						} else {
							self.deferred.reject(response, xhr.status);
						} 
					});
				}, 100);
			}

		} else if (this.options.msg.length) {
			setTimeout(function() {
				self.contents.html(self.options.msg);
				self.init();

				if ($.isFunction(self.options.onLoad)) {
					self.options.onLoad(self);
				}
			}, 100);
		}
	};

	this.open();
}
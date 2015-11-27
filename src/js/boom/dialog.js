function boomDialog(options) {
	this.deferred = new $.Deferred().always(function() {
		$(top.window).trigger('boom:dialog:close');
	});

	this.options = $.extend({
		width: 'auto',
		cancelButton : true,
		closeButton : true,
		autoOpen: true,
		modal: true,
		resizable: false,
		draggable: true,
		closeOnEscape: true,
		buttons : [],
		dialogClass : 'b-dialog',
		boomDialog: this
	}, options);

	boomDialog.prototype.always = function(callback) {
		this.deferred.always(callback);

		return this;
	};

 	boomDialog.prototype.cancelButton = {
		text : 'Cancel',
		icons : { primary : 'b-button-icon-cancel b-button-icon' },
		class : 'b-button',
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.cancel();
		}
	};

	boomDialog.prototype.cancel = function() {
		this.deferred.rejectWith(this.dialog);

		this.contents.remove();
		this.contents = null;
	};

	boomDialog.prototype.closeButton = {
		text : 'Okay',
		class : 'b-button',
		icons : { primary : 'b-button-icon-accept b-button-icon' },
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.close();
		}
	};

	boomDialog.prototype.close = function() {
		this.deferred.resolveWith(this.dialog);
		
		this.contents.remove();
		this.contents = null;
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

		$(top.window)
			.trigger('boom:dialog:open');

		this
			.contents
			.dialog(this.options)
			.ui();
	};

	boomDialog.prototype.open = function() {
		var self = this,
			$div = $('<div></div>');

		if (this.options.id) {
			$div.attr('id', this.options.id);
		}

		this.contents = $div.appendTo($(document).contents().find('body'));

		this.options.cancelButton && this.options.buttons.push(this.cancelButton);
		this.options.closeButton && this.options.buttons.push(this.closeButton);
		this.options.saveButton && this.options.buttons.push(this.saveButton);

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
					})
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

	boomDialog.prototype.saveButton = {
		text : 'Save',
		class : 'b-button',
		icons : { primary : 'b-button-icon-save b-button-icon' },
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.close();
		}
	};

	this.open();
};
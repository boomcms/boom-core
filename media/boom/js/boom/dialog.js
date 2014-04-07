boomDialog.prototype = {
 	cancelButton : {
		text : 'Cancel',
		icons : { primary : 'b-button-icon-cancel b-button-icon' },
		class : 'b-button',
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.cancel();
		}
	},

	closeButton : {
		text : 'Okay',
		class : 'b-button',
		icons : { primary : 'b-button-icon-accept b-button-icon' },
		click : function() {
			var boomDialog = $(this).dialog('option', 'boomDialog');
			boomDialog.close();
		}
	}
};

function boomDialog(options) {
	var self = this;

	this.deferred = new $.Deferred().always(function() {
		$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.minimise();
	});

	this.options = $.extend({
		width: 'auto',
		autoOpen: true,
		modal: true,
		resizable: false,
		draggable: true,
		closeOnEscape: true,
		position: ['center', 'center'],
		dialogClass : 'b-dialog',
		buttons: [this.cancelButton, this.closeButton],
		boomDialog: this
	}, options);

	boomDialog.prototype.always = function(callback) {
		this.deferred.always(callback);

		return this;
	};

	boomDialog.prototype.cancel = function() {
		this.deferred.rejectWith(this.dialog);
		this.contents.dialog('destroy');
	};

	boomDialog.prototype.close = function() {
		this.deferred.resolveWith(this.dialog);
		this.contents.dialog('destroy');
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

		$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.maximise();

		this
			.contents
			.dialog(this.options)
			.ui();

		this.contents.dialog('option', 'position', {my: "center center", at: "center center", of: top});

		this.contents.find('img').load(function() {
			boomDialog.contents.dialog('option', 'position', {my: "center center", at: "center center", of: top});
		});
	};

	boomDialog.prototype.open = function() {
		var id = this.options.id? this.options.id : $.boom.util.dom.uniqueId('boom-dialog-'),
			self = this;

		this.contents = $('#' + id).length? $('#' + id) : $('<div />').attr('id', id).hide().appendTo($(document).contents().find('body'));

		if (this.options.url && this.options.url.length) {
			if (this.contents.hasClass('ui-dialog-content')) {
				this.contents.dialog('open');
			} else {
				setTimeout(function() {
					self.contents.load(self.options.url, function(response, status){

						if (status == 'error') {
							if ($.boom.page && $( '.ui-dialog:visible' ).length == 0) {
								$.boom.page.toolbar && $.boom.page.toolbar.minimise();
							}

							return;
						}

						self.init();

						if ($.isFunction(self.options.onLoad)) {
							self.options.onLoad.apply(self.dialog);
						}
					});
				}, 100);
			}

		} else if (this.options.msg.length) {
			this.contents.html(this.options.msg);

			this.init();
		}
	};

	this.open();
};
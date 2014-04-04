function boomDialog(options) {
	this.deferred = new $.Deferred();

	var self = this;
	this.options = {
		width: 'auto',
		autoOpen: true,
		modal: true,
		resizable: false,
		id : 'b-dialog',
		draggable: true,
		closeOnEscape: true,
		position: ['center', 'center'],
		dialogClass : 'b-dialog',
		buttons: [
			{
				text : 'Cancel',
				icons : { primary : 'b-button-icon-cancel b-button-icon' },
				class : 'b-button',
				click : function() {
					self.cancel();
				}
			},
			{
				text : 'Okay',
				class : 'b-button',
				icons : { primary : 'b-button-icon-accept b-button-icon' },
				click : function() {
					self.close();
				}
			}
		]
	};

	this.options = $.extend(options, this.options);

	boomDialog.prototype.cancel = function() {
		this.deferred.rejectWith(this.dialog);
		this.dialog.dialog('destroy');
	};

	boomDialog.prototype.close = function() {
		this.deferred.resolveWith(this.dialog);
		this.dialog.dialog('destroy');
	};

	boomDialog.prototype.init = function() {
		$.boom.page && $.boom.page.toolbar && $.boom.page.toolbar.maximise();

		this
			.dialog
			.dialog(this.options)
			.ui();
	};

	boomDialog.prototype.open = function() {
		var id = this.options.id? this.options.id : $.boom.util.dom.uniqueId('boom-dialog-'),
			self = this;

		this.dialog = $('#' + id).length? $('#' + id) : $('<div />').attr('id', id).hide().appendTo($(document).contents().find('body'));

		if (this.options.url.length) {
			if (this.dialog.hasClass('ui-dialog-content')) {
				this.dialog.dialog('open');
			} else {
				setTimeout(function() {
					self.dialog.load(self.options.url, function(response, status){

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
			this.dialog.html(this.options.msg);

			this.init();
		}

		return this.deferred;
	};


	return this.open();
};
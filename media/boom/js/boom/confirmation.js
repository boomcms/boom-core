function boomConfirmation(title, message) {
	this.deferred = new $.Deferred();

	this.title = title;
	this.message = message;

	boomConfirmation.prototype.open = function() {
		var confirmation = this;

		$.boom.dialog.open({
			title : confirmation.title,
			msg : confirmation.message,
			width : 300,
			callback : function() {
				confirmation.deferred.resolve();
			}
		});

		return this.deferred;
	}

	return this.open();
}
function boomAlert(message) {
	this.deferred = new $.Deferred();
	this.message = message;

	boomAlert.prototype.open = function() {
		var alert = this;

		$.boom.dialog.open({
			msg : alert.message,
			buttons: [
				{
					text : 'Okay',
					icons : { primary : 'b-button-icon-accept b-button-icon' },
					class : 'b-button',
					click : function() {
						$.boom.dialog.destroy($(this));
						alert.deferred.resolve();
					}
				}
			]
		});

		return this.deferred;
	};

	return this.open();
}
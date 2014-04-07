function boomAlert(message) {
	this.deferred = new $.Deferred();
	this.message = message;

	boomAlert.prototype.open = function() {
		var alert = this,
			dialog;

		dialog = new boomDialog({
			msg : alert.message,
			buttons: [
				{
					text : 'Okay',
					icons : { primary : 'b-button-icon-accept b-button-icon' },
					class : 'b-button',
					click : function() {
						dialog.close();
						alert.deferred.resolve();
					}
				}
			]
		});

		return this.deferred;
	};

	return this.open();
}
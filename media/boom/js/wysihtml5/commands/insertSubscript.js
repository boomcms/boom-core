(function(wysihtml5) {
	wysihtml5.commands.insertSubscript = {
		exec: function(composer, command) {
			if (wysihtml5.commands.insertSubscript.state(composer, command)) {
				return wysihtml5.commands.formatInline.exec(composer, "formatInline", 'sub');
			} else {
				return wysihtml5.commands.formatInline.exec(composer, "formatInline", 'sub');
			}
		},

		state: function(composer, command) {
			return wysihtml5.commands.formatInline.state(composer, "formatInline", 'sub');
		}
	};
})(wysihtml5);
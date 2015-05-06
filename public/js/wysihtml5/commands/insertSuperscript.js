(function(wysihtml5) {
	wysihtml5.commands.insertSuperscript = {
		exec: function(composer, command) {
			if (wysihtml5.commands.insertSuperscript.state(composer, command)) {
				return wysihtml5.commands.formatInline.exec(composer, "formatInline", 'sup');
			} else {
				return wysihtml5.commands.formatInline.exec(composer, "formatInline", 'sup');
			}
		},

		state: function(composer, command) {
			return wysihtml5.commands.formatInline.state(composer, "formatInline", 'sup');
		}
	};
})(wysihtml5);
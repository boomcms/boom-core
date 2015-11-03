/*
 * Wysihtml5 command for applying cta class
 *
 */
(function(wysihtml5) {
	var nodeOptions = {
		className: 'cta',
		toggle: true
	};

	wysihtml5.commands.cta = {
		exec: function(composer, command) {
			return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", nodeOptions);
		},

		state: function(composer, command) {
			return wysihtml5.commands.formatBlock.state(composer, "formatBlock", nodeOptions);
		}
	};
})(wysihtml5);
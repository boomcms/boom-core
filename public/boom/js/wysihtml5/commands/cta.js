/*
 * Wysihtml5 command for applying cta class
 *
 */
(function(wysihtml5) {
  var CLASS_NAME  = "cta",
      REG_EXP     = /cta/g;

	wysihtml5.commands.cta = {
		exec: function(composer, command) {
			if (wysihtml5.commands.cta.state(composer, command)) {
				wysihtml5.commands.formatBlock.exec(composer, "formatBlock", 'p', CLASS_NAME, REG_EXP);
				return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", 'p');
			} else {
				return wysihtml5.commands.formatBlock.exec(composer, "formatBlock", 'p', CLASS_NAME, REG_EXP);
			}
		},

		state: function(composer, command) {
			return wysihtml5.commands.formatBlock.state(composer, "formatBlock", 'p', CLASS_NAME, REG_EXP);
		}
	};
})(wysihtml5);
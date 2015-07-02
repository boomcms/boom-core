/*
 * Wysihtml5 command for applying cta class
 *
 */
(function(wysihtml5) {
	var nodeOptions = {
		nodeName: 'SPAN',
		className: "cta",
		classRegExp: /cta/g,
		toggle: true
	};

	wysihtml5.commands.cta = {
		exec: function(composer, command) {
			return composer.commands.exec("formatInline", 'P', 'cta', /cta/g);
		},

		state: function(composer, command) {
			return composer.commands.state("formatInline", 'P', 'cta', /cta/g);
		}
	};
})(wysihtml5);
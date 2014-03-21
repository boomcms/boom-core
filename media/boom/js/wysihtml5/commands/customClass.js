/*
 * Command for adding a custom class to some text
 *
 * See http://stackoverflow.com/questions/13380948/wysihtml5-editor-how-to-simply-add-a-class-to-an-element
 */
wysihtml5.commands.customClass = {
	exec: function(composer, command, className) {
		return wysihtml5.commands.formatBlock.exec(composer, command, "", className, new RegExp(className, "g"));
	},
	state: function(composer, command, className) {
		return wysihtml5.commands.formatBlock.state(composer, command, "", className, new RegExp(className, "g"));
	}
};
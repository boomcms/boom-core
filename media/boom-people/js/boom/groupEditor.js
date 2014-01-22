$.widget('boom.groupEditor', {
	group : null,

	bind : function() {
		var groupEditor = this;
	},

	_create : function() {
		this.group = this.options.group;
		this.element.groupPermissionsEditor({group : this.group});

		this.bind();
	}
});
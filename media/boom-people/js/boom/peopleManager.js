$.widget('boom.peopleManager', {
	bind : function() {
		var peopleManager = this;

		this.element
			.on('click', '.b-people-group-add', function(e) {
				e.preventDefault();
				peopleManager.addGroup();
			})
			.on('click', '#b-people-create', function(e) {
				e.preventDefault();
				peopleManager.addPerson();
			});

	},

	addGroup : function() {
		var group = new boomGroup();

		group.add()
			.done(function() {
				$.boom.growl.show('Group successfully saved, reloading.');

				window.setTimeout(function() {
					top.location.reload();
				}, 300);
			});
	},

	addPerson : function() {
		var person = new boomPerson();

		person.add()
			.done(function() {
				$.boom.growl.show('Success');

				setTimeout(function() {
					top.location.reload();
				}, 300);
			})
			.fail(function() {
				$.boom.growl.show('Failure');
			});
	},

	_create : function() {
		this.bind();
	}
});
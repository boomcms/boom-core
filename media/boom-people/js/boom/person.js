function boomPerson(person_id) {
	this.id = person_id;

	boomPerson.prototype.base_url = '/cms/person/';

	boomPerson.prototype.add = function() {
		var deferred = new $.Deferred(),
			person = this;

		$.boom.dialog.open({
			url : this.base_url + 'add',
			width: 'auto',
			title : 'Create new person',
			callback: function() {
				var data = $(this).find('form').serialize();

				person.addWithData(data)
					.done(function() {
						deferred.resolve();
					})
					.fail(function() {
						deferred.reject();
					});
			}
		});

		return deferred;
	};

	boomPerson.prototype.addWithData = function(data) {
		return $.post(this.base_url + 'add', data);
	};

	boomPerson.prototype.deleteMultiple = function(people_ids) {
		return $.post(this.base_url + 'delete', {'people[]' : people_ids});
	};
};
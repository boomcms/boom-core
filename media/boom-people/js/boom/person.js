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

	boomPerson.prototype.addGroups = function() {
		var url = this.base_url + 'add_group/' + this.id,
			deferred = new $.Deferred();

		$.boom.dialog.open({
			url: url,
			title: 'Add group',
			callback: function() {
				var groups = {};
				
				$(this).find('form select option:selected').each(function(i, el) {
					var $el = $(el);
					groups[$el.val()] = $el.text();
				});

				var group_ids = Object.keys(groups);
				if (group_ids.length) {
					$.boom.post(url, {'groups[]' : group_ids})
						.done(function() {
							deferred.resolve(groups);
						});
				} else {
					deferred.resolve([]);
				}
			}
		});

		return deferred;
	};

	boomPerson.prototype.addWithData = function(data) {
		return $.boom.post(this.base_url + 'add', data);
	};

	boomPerson.prototype.delete = function() {
		var deferred = new $.Deferred(),
			person = this,
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to delete this person?');

			confirmation
				.done(function() {
					$.boom.post(person.base_url + 'delete/' + person.id)
						.done(function() {
							deferred.resolve();
						});
				});

		return deferred;
	};

	boomPerson.prototype.deleteMultiple = function(people_ids) {
		return $.boom.post(this.base_url + 'delete', {'people[]' : people_ids});
	};

	boomPerson.prototype.removeGroup = function(group_id) {
		return $.boom.post(this.base_url + 'remove_group/' + this.id, {group_id: group_id});
	};

	boomPerson.prototype.save = function(data) {
		return $.boom.post(this.base_url + 'save/' + this.id, data);
	};
};
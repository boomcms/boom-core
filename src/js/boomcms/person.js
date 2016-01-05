function boomPerson(person_id) {
	this.id = person_id;

	boomPerson.prototype.base_url = '/boomcms/person/';

	boomPerson.prototype.add = function() {
		var deferred = new $.Deferred(),
			person = this,
			dialog;

		dialog = new boomDialog({
			url : this.base_url + 'add',
			width: '600px',
			title : 'Create new person',
			closeButton: false,
			saveButton: true
		})
		.done(function() {
			var data = dialog.contents.find('form').serialize();

			person.addWithData(data)
				.done(function(response) {
					deferred.resolve();
				})
				.fail(function() {
					deferred.reject();
				});
		});

		return deferred;
	};

	boomPerson.prototype.addGroups = function() {
		var url = this.base_url + 'add_group/' + this.id,
			deferred = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: url,
			title: 'Add group',
			closeButton: false,
			saveButton: true
		}).done(function() {
			var groups = {};

			dialog.contents.find('form select option:selected').each(function(i, el) {
				var $el = $(el);
				groups[$el.val()] = $el.text();
			});

			var groupIds = Object.keys(groups);
			console.log(groups);
			console.log(groupIds);
			if (groupIds.length) {
				$.post(url, {'groups[]' : groupIds})
					.done(function() {
						deferred.resolve(groups);
					});
			} else {
				deferred.resolve([]);
			}
		});

		return deferred;
	};

	boomPerson.prototype.addWithData = function(data) {
		return $.post(this.base_url + 'add', data);
	};

	boomPerson.prototype.delete = function() {
		var deferred = new $.Deferred(),
			person = this,
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to delete this person?');

			confirmation
				.done(function() {
					$.post(person.base_url + 'delete', {
						people : [person.id]
					})
					.done(function() {
						deferred.resolve();
					});
				});

		return deferred;
	};

	boomPerson.prototype.deleteMultiple = function(people_ids) {
		return $.post(this.base_url + 'delete', {'people[]' : people_ids});
	};

	boomPerson.prototype.removeGroup = function(group_id) {
		return $.post(this.base_url + 'remove_group/' + this.id, {group_id: group_id});
	};

	boomPerson.prototype.save = function(data) {
		return $.post(this.base_url + 'save/' + this.id, data);
	};
};
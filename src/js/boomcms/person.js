function boomPerson(person_id) {
	this.id = person_id;

	boomPerson.prototype.baseUrl = '/boomcms/person';

	boomPerson.prototype.add = function() {
		var deferred = new $.Deferred(),
			person = this,
			dialog;

		dialog = new boomDialog({
			url : this.baseUrl + '/create',
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

	boomPerson.prototype.addGroups = function(groupIds) {
		return $.post(this.baseUrl + '/' + this.id + '/groups', {'groups[]': groupIds});
	}

	boomPerson.prototype.getAddableGroups = function() {
		return $.get(this.baseUrl + '/' + this.id + '/groups');

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
		return $.post(this.baseUrl, data);
	};

	boomPerson.prototype.delete = function() {
		var deferred = new $.Deferred(),
			person = this,
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to delete this person?');

			confirmation
				.done(function() {
					person.deleteMultiple([person.id])
					.done(function() {
						deferred.resolve();
					});
				});

		return deferred;
	};

	boomPerson.prototype.deleteMultiple = function(peopleIds) {
		return 	$.ajax({
			type: 'delete',
			url: this.baseUrl,
			data: {
				'people[]': peopleIds
			}
		});
	};

	boomPerson.prototype.removeGroup = function(groupId) {
		return $.ajax({
			type: 'delete',
			url: this.baseUrl + '/' + this.id + '/groups/' + groupId
		});
	};

	boomPerson.prototype.save = function(data) {
		return $.ajax({
			type: 'put',
			url: this.baseUrl + '/' + this.id,
			data: data
		});
	};
};
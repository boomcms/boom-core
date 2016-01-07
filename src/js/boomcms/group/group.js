function boomGroup(group_id) {
	this.id = group_id;

	boomGroup.prototype.base_url = '/boomcms/group';

	boomGroup.prototype.add = function() {
		var group = this,
			deferred = new $.Deferred(),
			dialog;

		dialog = new boomDialog({
			url: this.base_url + '/create',
			title: 'Add group',
			closeButton: false,
			saveButton: true
		})
		.done(function() {
			group.addWithName(dialog.contents.find('input[type=text]').val())
				.done(function(response) {
					deferred.resolve(response);
				});
		});

		return deferred;
	};

	boomGroup.prototype.addRole = function(role_id, allowed, page_id) {
		var deferred = new $.Deferred(),
			group = this;

		group.removeRole(role_id, page_id)
			.done(function() {
				$.ajax({
					type: 'put',
					url: group.base_url + '/' + group.id + '/roles',
					data: {
						role_id : role_id,
						allowed : allowed,
						page_id: page_id
					}
				})
				.done(function(response) {
					deferred.resolve(response);
				});
			});

		return deferred;
	};

	boomGroup.prototype.addWithName = function(name) {
		return $.post(this.base_url, {name: name});
	};

	boomGroup.prototype.getRoles = function(page_id) {
		return $.getJSON(this.base_url + '/' + this.id + '/roles?page_id=' + page_id);
	};

	boomGroup.prototype.remove = function() {
		var group = this,
			deferred = new $.Deferred(),
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

		confirmation
			.done(function() {
				$.ajax({
					url: group.base_url + '/' + group.id,
					type: 'delete'
				})
				.done(function(response) {
					deferred.resolve(response);
				});
			});

		return deferred;
	};

	boomGroup.prototype.removeRole = function(role_id, page_id) {
		return $.ajax({
			type: 'delete',
			url: this.base_url + '/' + this.id + '/roles',
			data: {
				role_id : role_id,
				page_id : page_id
			}
		});
	},

	boomGroup.prototype.save = function(data) {
		return $.ajax({
			type: 'put',
			url: this.base_url + '/' + this.id,
			data: data
		});
	};
};
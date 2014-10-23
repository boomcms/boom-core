function boomGroup(group_id) {
	this.id = group_id;

	boomGroup.prototype.base_url = '/cms/group/';

	boomGroup.prototype.add = function() {
		var group = this,
			deferred = new $.Deferred();

		new boomDialog({
			url: this.base_url + 'add',
			title: 'Add group'
		})
		.done(function() {
			group.addWithName($(this).find('input[type=text]').val())
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
				$.boom.post(group.base_url + 'add_role/' + group.id, {
					role_id : role_id,
					allowed : allowed,
					page_id: page_id
				})
				.done(function(response) {
					deferred.resolve(response);
				});
			});

		return deferred;
	};

	boomGroup.prototype.addWithName = function(name) {
		return $.boom.post(this.base_url + 'add', {name: name});
	};

	boomGroup.prototype.getRoles = function(page_id) {
		return $.getJSON(this.base_url + 'list_roles/' + this.id + '?page_id=' + page_id);
	};

	boomGroup.prototype.remove = function() {
		var group = this,
			deferred = new $.Deferred(),
			confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

		confirmation
			.done(function() {
				$.boom.post(group.base_url + 'delete/' + group.id)
					.done(function(response) {
						deferred.resolve(response);
					});
			});

		return deferred;
	};

	boomGroup.prototype.removeRole = function(role_id, page_id) {
		return $.boom.post(this.base_url + 'remove_role/' + this.id, {
			role_id : role_id,
			page_id : page_id
		});
	},

	boomGroup.prototype.save = function(data) {
		return $.boom.post(this.base_url + 'save/' + this.id, data);
	};
};
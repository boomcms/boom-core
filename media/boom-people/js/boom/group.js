function boomGroup(group_id) {
	this.id = group_id;

	boomGroup.prototype.base_url = '/cms/groups/';

	boomGroup.prototype.add = function() {
		var group = this,
			deferred = new $.Deferred();

		$.boom.dialog.open({
			url: this.base_url + 'add',
			title: 'Add group',
			callback: function() {
				group.addWithName($(this).find('input[type=text]').val())
					.done(function(response) {
						deferred.resolve(response);
					});
			}
		});

		return deferred;
	};

	boomGroup.prototype.addRole = function(role_id, allowed, page_id) {
		var deferred = new $.Deferred(),
			group = this;

		group.removeRole(role_id, page_id)
			.done(function() {
				$.post(group.base_url + 'add_role/' + group.id, {
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
		return $.post(this.base_url + 'add', {name: name});
	};

	boomGroup.prototype.getRoles = function(page_id) {
		return $.getJSON(this.base_url + 'list_roles/' + this.id + '?page_id=' + page_id);
	};

	boomGroup.prototype.remove = function() {
		var group = this;

		$.boom.dialog.confirm(
			'Please confirm',
			'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!'
		)
		.done(function() {
			return $.post(group.base_url + 'delete/' + group.id)
				.done( function(){
					$.boom.growl.show('Group successfully removed.');
				});
		});
	};

	boomGroup.prototype.removeRole = function(role_id, page_id) {
		return $.post(this.base_url + 'remove_role/' + this.id, {
			role_id : role_id,
			page_id : page_id
		});
	},

	boomGroup.prototype.save = function(data) {
		return $.post(this.base_url + 'save/' + this.id, data);
	};
};
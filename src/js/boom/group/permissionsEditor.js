$.widget('boom.groupPermissionsEditor', {
	group : null,

	bind : function() {
		var self = this, selected_page;

		this.element
			.on('change', '#b-group-roles-general input[type=radio]', function() {
				var role_id = this.name,
					allowed = parseInt(this.value, 10);

				self._change(role_id, allowed, 0);

			})
			.on('change', '#b-group-roles-pages input[type=radio]', function() {
				var role_id = this.name,
					allowed = parseInt(this.value, 10),
					page_id = selected_page;

				self._change(role_id, allowed, page_id);

			});

		/**
		 * Clicking on a page in the tree.
		 * Should make a GET call to /cms/groups/list_roles/<group ID>?page_id=<page ID>
		 *
		 * This will return a json encoded array of role ID => <value>
		 * Possible values are 1 if the role is allowed and 0 if the role is disallowed.
		 * If nothing is set for a role then that role ID won't be in the returned array.
		 *
		 * The role checkboxes should then be updated if the correct values.
		 */
		var page_tree = this.element.find('#b-group-roles-pages .boom-tree');

		page_tree.pageTree({
			onPageSelect : function(link) {
				$('#b-group-roles-pages .b-group-roles').show();

				selected_page = link.getPageId();

				self._check_inputs($('#b-group-roles-pages input[type=radio]'), -1);

				page_tree
					.find('a[data-page-id=' + link.getPageId() + ']')
					.parents('.boom-tree')
					.find('a.ui-state-active')
					.removeClass('ui-state-active')
					.end()
					.end()
					.addClass('ui-state-active');

				self._show_permissions(selected_page);
			}
		});
	},

	_change: function(role_id, allowed, page_id) {
		this.group.addRole(role_id, allowed, page_id)
			.done(function() {
				new boomNotification('Permissions updated');
			});
	},

	_check_inputs: function(radio_buttons, value) {
		radio_buttons
			.filter(':checked')
			.prop('checked', false)
			.end()
			.filter('[value=' + value + ']')
			.prop('checked', true);
	},

	_create : function() {
		this.group = this.options.group;
		this.element.ui();
		this.bind();

		this._check_inputs($('#b-group-roles-general input[type=radio]'), -1);
		this._show_permissions(0);
	},

	_show_permissions : function(page_id) {
		var self = this,
			i;

		this.group.getRoles(page_id)
			.done(function(data) {
				console.log(data);
				if (data.length) {
					for (i in data) {
						console.log(data[i]);
						self._check_inputs($('input[name=' + data[i].role_id + ']'), data[i].allowed);
					}
				}
			});
	}
});
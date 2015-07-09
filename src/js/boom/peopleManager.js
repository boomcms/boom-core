$.widget('boom.peopleManager', {
	homeUrl : '/cms/people',
	selectedPeople : 0,

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
			})
			.on('click', '.b-group-delete', function(e) {
				e.preventDefault();
				peopleManager.removeGroup($(this).parents('li'));
			})
			.on('change', '#b-items-view-list input[type=checkbox]', function() {
				peopleManager.togglePersonCheckbox($(this));
				peopleManager.togglePersonDeleteButton();
			})
			.on('click', '#b-people-multi-delete', function() {
				peopleManager.deleteSelectedPeople();
			})
			.on('click', '#b-people-group-save', function(e) {
				e.preventDefault();
				peopleManager.saveOpenGroup();
			})
			.on('click', '#b-person-save', function(e) {
				e.preventDefault();
				peopleManager.currentPersonSave();
			})
			.on('click', '#b-person-delete', function(e) {
				e.preventDefault();
				peopleManager.currentPersonDelete();
			})
			.on('click', '.b-person-addgroups', function(e) {
				e.preventDefault();
				peopleManager.currentPersonAddGroups();
			})
			.on('click', '.b-person-group-delete', function(e) {
				e.preventDefault();
				peopleManager.currentPersonRemoveGroup($(this).parents('li'));
			})
			.on('click', '#b-people-all', function() {
				window.location = peopleManager.homeUrl;
			});
	},

	addGroup : function() {
		var group = new boomGroup();

		group.add()
			.done(function() {
				new boomNotification('Group successfully saved, reloading.');

				window.setTimeout(function() {
					$.boom.reload();
				}, 1500);
			});
	},

	addPerson : function() {
		var person = new boomPerson();

		person.add()
			.done(function() {
				new boomNotification('Success');

				setTimeout(function() {
					$.boom.reload();
				}, 300);
			})
			.fail(function() {
				new boomNotification('Failure');
			});
	},

	_create : function() {
		this.bind();

		this.document.find('body').height(this.window.height());
	},

	currentPersonAddGroups : function() {
		var person_id = this.getCurrentPersonId(),
			person = new boomPerson(person_id),
			peopleManager = this,
			$group_list = this.element.find('#b-person-groups-list');

		person.addGroups()
			.done(function(new_groups) {
				if (Object.keys(new_groups).length) {
					var id, name;

					for (id in new_groups) {
						name = new_groups[id];

						$group_list.append($("<li data-group-id=" + id +">" + name +"&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a></li>"));
					}

					new boomNotification('This person has been added to the groups');
				}
			});
	},

	currentPersonDelete : function() {
		var person_id = this.getCurrentPersonId(),
			person = new boomPerson(person_id),
			peopleManager = this;

		person.delete()
			.done(function() {
				new boomNotification('This person has been deleted.');

				setTimeout(function() {
					top.location = peopleManager.homeUrl;
				}, 300);
			});
	},

	currentPersonRemoveGroup : function($group) {
		var person_id = this.getCurrentPersonId(),
			person = new boomPerson(person_id),
			peopleManager = this,
			group_id = $group.data('group-id');

		person.removeGroup(group_id)
			.done(function() {
				new boomNotification('This person has been removed from the group');
				$group.remove();
			});
	},

	currentPersonSave : function() {
		var person_id = this.getCurrentPersonId(),
			person = new boomPerson(person_id),
			peopleManager = this;

		person.save(this.element.find('.b-person-view form').serialize())
			.done(function() {
				new boomNotification('The new details for this person have been saved.');
			});
	},

	deleteSelectedPeople : function() {
		var selected = this.getSelectedPeople(),
			person = new boomPerson(selected.join('-')),
			peopleManager = this,
			confirmation = new boomConfirmation('Confirm deletion', 'Are you sure you want to remove the selected people?');

			confirmation
				.done(function() {
					person.deleteMultiple(selected)
						.done(function() {
							peopleManager.removePeopleFromList(selected);

							new boomNotification('The selected people have been deleted.');
						});
				});
	},

	getCurrentPersonId : function() {
		return this.element.find('.b-person-view').data('person-id');
	},

	getSelectedPeople : function() {
		return $('#b-items-view-list input[type=checkbox]:checked')
			.map(function() {
				return $(this).parents('tr').data('person-id');
			})
			.get();
	},

	removeGroup : function($el) {
		var group = new boomGroup($el.data('group-id'));

		group.remove()
			.done(function() {
				$el.remove();
				new boomNotification('Group successfully removed.');
			});
	},

	removePeopleFromList : function(person_ids) {
		$('#b-items-view-list tr').each(function(index, el) {
			var $el = $(el),
				i = person_ids.indexOf($el.data('person-id'));

			if (i >= 0) {
				$el.remove();
				person_ids.splice(i, 1);

				if (person_ids.length === 0) {
					return false;
				}
			}
		});
	},

	saveOpenGroup : function() {
		var $form = this.element.find('#b-group-edit form'),
			group_id = $form.data('group-id'),
			group = new boomGroup(group_id),
			new_name = $form.find('input[name=name]').val();

		group.save($form.serialize())
			.done(function() {
				new boomNotification('Group name updated');
				$('#b-groups-list li[data-group-id='+ group_id + '] .b-groups-item').html(new_name);
			});
	},

	togglePersonCheckbox : function($el) {
		if ($el.is(":checked")) {
			this.selectedPeople++;
		} else {
			this.selectedPeople--;
		}
	},

	togglePersonDeleteButton : function() {
		var button = this.element.find('#b-people-multi-delete');

		(this.selectedPeople > 0)? button.prop('disabled', false) : button.prop('disabled', true);
	}
});
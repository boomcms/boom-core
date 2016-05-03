$.widget('boom.peopleManager', {
	homeUrl : '/boomcms/people',
	selectedPeople : 0,

	bind: function() {
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
			.on('click', '#b-people-all', function() {
				window.location = peopleManager.homeUrl;
			});
	},

	addGroup: function() {
		var group = new boomGroup();

		group.add()
			.done(function() {
				new boomNotification('Group successfully saved, reloading').show();

				window.setTimeout(function() {
					top.location.reload();
				}, 1500);
			});
	},

	addPerson: function() {
		var person = new boomPerson();

		person.add()
			.done(function() {
				new boomNotification('Success').show();

				setTimeout(function() {
					top.location.reload();
				}, 300);
			})
			.fail(function() {
				new boomNotification('Failure').show();
			});
	},

	_create: function() {
		var peopleManager = this;

		this.bind();

		this.element.find('.b-person-groups')
			.chosen()
			.change(function(event, data) {
				if (typeof(data.selected) !== 'undefined') {
					peopleManager.currentPersonAddGroup(data.selected);
				}

				if (typeof(data.deselected) !== 'undefined') {
					peopleManager.currentPersonRemoveGroup(data.deselected);
				}
			});
	},

	currentPersonAddGroup: function(groupId) {
		this.getCurrentPerson().addGroup(groupId)
			.done(function() {
				new boomNotification('This person has been added to the group').show();
			});
	},

	currentPersonDelete: function() {
		var url = this.homeUrl;

		this.getCurrentPerson().delete()
			.done(function() {
				new boomNotification('This person has been deleted').show();

				setTimeout(function() {
					top.location = url;
				}, 300);
			});
	},

	currentPersonRemoveGroup: function(groupId) {
		this.getCurrentPerson().removeGroup(groupId)
			.done(function() {
				new boomNotification('This person has been removed from the group').show();
			});
	},

	currentPersonSave: function() {
		this.getCurrentPerson().save(this.element.find('.b-person-view form').serialize())
			.done(function() {
				new boomNotification('The new details for this person have been saved').show();
			});
	},

	deleteSelectedPeople: function() {
		var selected = this.getSelectedPeople(),
			person = new boomPerson(selected.join('-')),
			peopleManager = this,
			confirmation = new boomConfirmation('Confirm deletion', 'Are you sure you want to remove the selected people?');

			confirmation
				.done(function() {
					person.deleteMultiple(selected)
						.done(function() {
							peopleManager.removePeopleFromList(selected);

							new boomNotification('The selected people have been deleted').show();
						});
				});
	},

	getCurrentPerson: function() {
		var personId = this.element.find('.b-person-view').data('person-id');

		return new boomPerson(personId);
	},

	getSelectedPeople: function() {
		return $('#b-items-view-list input[type=checkbox]:checked')
			.map(function() {
				return $(this).parents('tr').data('person-id');
			})
			.get();
	},

	removeGroup: function($el) {
		var group = new boomGroup($el.data('group-id'));

		group.remove()
			.done(function() {
				$el.remove();
				new boomNotification('Group successfully removed').show();
			});
	},

	removePeopleFromList: function(person_ids) {
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

	saveOpenGroup: function() {
		var $form = this.element.find('#b-group-edit form'),
			group_id = $form.data('group-id'),
			group = new boomGroup(group_id),
			new_name = $form.find('input[name=name]').val();

		group.save($form.serialize())
			.done(function() {
				new boomNotification('Group name updated').show();
				$('#b-groups-list li[data-group-id='+ group_id + '] .b-groups-item').html(new_name);
			});
	},

	togglePersonCheckbox: function($el) {
		if ($el.is(":checked")) {
			this.selectedPeople++;
		} else {
			this.selectedPeople--;
		}
	},

	togglePersonDeleteButton: function() {
		var button = this.element.find('#b-people-multi-delete');

		(this.selectedPeople > 0)? button.prop('disabled', false) : button.prop('disabled', true);
	}
});
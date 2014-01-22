$.widget('boom.peopleManager', {
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
			});
	},

	addGroup : function() {
		var group = new boomGroup();

		group.add()
			.done(function() {
				$.boom.growl.show('Group successfully saved, reloading.');

				window.setTimeout(function() {
					top.location.reload();
				}, 300);
			});
	},

	addPerson : function() {
		var person = new boomPerson();

		person.add()
			.done(function() {
				$.boom.growl.show('Success');

				setTimeout(function() {
					top.location.reload();
				}, 300);
			})
			.fail(function() {
				$.boom.growl.show('Failure');
			});
	},

	_create : function() {
		this.bind();
	},

	deleteSelectedPeople : function() {
		var selected = this.getSelectedPeople(),
			person = new boomPerson(selected.join('-')),
			peopleManager = this;

		$.boom.dialog.confirm('Confirm deletion', 'Are you sure you want to remove the selected people?')
			.done(function() {
				person.deleteMultiple(selected)
					.done(function() {
						peopleManager.removePeopleFromList(selected);

						$.boom.growl.show('The selected people have been deleted.');
					});
				});
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
				$.boom.growl.show('Group successfully removed.');
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
		var $form = this.element.find('#b-group-edit'),
			group_id = $form.data('group-id'),
			group = new boomGroup(group_id),
			new_name = $form.find('input[name=name]').val();

		group.save($form.serialize())
			.done(function() {
				$.boom.growl.show('Group name updated');
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

		(this.selectedPeople > 0)? button.button('enable') : button.button('disable');
	}
});
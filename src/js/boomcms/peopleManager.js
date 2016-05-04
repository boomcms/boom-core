(function($, BoomCMS) {
	'use strict';

	$.widget('boom.peopleManager', {
		homeUrl : '/boomcms/people',
		selectedPeople : 0,

		bind: function() {
			var peopleManager = this;

			this.element
				.on('click', '.b-button', function(e) {
					e.preventDefault();
				})
				.on('click', '#b-people-create', function() {
					peopleManager.addPerson();
				})
				.on('change', '#b-items-view-list input[type=checkbox]', function() {
					peopleManager.togglePersonCheckbox($(this));
					peopleManager.togglePersonDeleteButton();
				})
				.on('click', '#b-people-multi-delete', function() {
					peopleManager.deleteSelectedPeople();
				})
				.on('click', '#b-person-save', function() {
					peopleManager.currentPersonSave();
				})
				.on('click', '#b-person-delete', function() {
					peopleManager.currentPersonDelete();
				})
				.on('click', '#b-people-all', function() {
					window.location = peopleManager.homeUrl;
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
			var peopleManager = this,
				person = this.getCurrentPerson();

			this.bind();

			new BoomCMS.PeopleManager();

			this.element
				.find('.b-person-groups')
				.chosen()
				.change(function(event, data) {
					if (typeof(data.selected) !== 'undefined') {
						return peopleManager.addPersonToGroup(person, data.selected);
					}

					return peopleManager.removePersonFromGroup(person, data.deselected);
				})
				.end()
				.find('.b-person-sites')
				.chosen()
				.change(function(event, data) {
					if (typeof(data.selected) !== 'undefined') {
						return peopleManager.addPersonToSite(person, data.selected);
					}

					return peopleManager.removePersonFromSite(person, data.deselected);
				});
		},

		addPersonToGroup: function(person, groupId) {
			person.addGroup(groupId)
				.done(function() {
					new boomNotification('This person has been added to the group').show();
				});
		},

		addPersonToSite: function(person, siteId) {
			person.addSite(siteId)
				.done(function() {
					new boomNotification('This person has been added to the site').show();
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

		removePersonFromGroup: function(person, groupId) {
			person.removeGroup(groupId)
				.done(function() {
					new boomNotification('This person has been removed from the group').show();
				});
		},

		removePersonFromSite: function(person, siteId) {
			person.removeSite(siteId)
				.done(function() {
					new boomNotification('This person has been removed from the site').show();
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
})(jQuery, window.BoomCMS);
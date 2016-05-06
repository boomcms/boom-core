(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.PeopleManager = Backbone.View.extend({
		el: $('body'),

		events: {
			'submit #b-groups-new': 'createGroup',
			'click .b-groups-list .edit': 'editGroup',
			'click #b-people-create': 'createPerson'
		},

		initialize: function() {
			this.$groupList = this.$('#b-groups-list');
			this.$content = this.$('#b-people-content');

			this.groups = new BoomCMS.Collections.Groups();
			this.people = new BoomCMS.Collections.People();

			this.listenTo(this.groups, 'edit created', this.editGroup);
			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'all sort', this.render);	
			this.groups.fetch();
		},

		addGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupListItem({model: group});

			this.$groupList.append(view.render().el);
		},

		createGroup: function(e) {
			e.preventDefault();

			var $el = $(e.target).find('input[type=text]'),
				group = this.groups.create({
					id: null,
					name: $el.val()
				});

			this.groups.trigger('created', group);

			$el.val('');
		},

		createPerson: function(e) {
			e.preventDefault();

			var view = new BoomCMS.PeopleManager.CreatePerson({
				groups: this.groups.models,
				people: this.people,
			});

			this.$content.html(view.render().el);
		},

		editGroup: function(group) {
			var view = new BoomCMS.PeopleManager.GroupView({model: group}); 

			this.$content.html(view.render().el);
			view.$el.groupPermissionsEditor({group: group});
		},

		render: function() {
			this.$groupList.empty();
			this.groups.each(this.addGroup, this);

			return this;
		}
	});
}(jQuery, Backbone, window.BoomCMS));
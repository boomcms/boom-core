(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.View.PeopleManager = Backbone.View.extend({
		el: $('#b-people-manager'),

		events: {
			'submit #b-groups-new': 'createGroup'
		},

		initialize: function() {
			this.$groupList = this.$('#b-groups-list');

			var groups = Backbone.Collection.extend({
				model: BoomCMS.Group,
				url: '/boomcms/group'
			});

			this.groups = new groups();

			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'all', this.render);
			this.groups.fetch();
		},

		createGroup: function(e) {
			e.preventDefault();

			this.groups.add({
				id: null,
				name: $('#b-groups-new input[type=text]').val()
			});
		},

		addGroup: function(group) {
			var view = new BoomCMS.View.GroupListItem({model: group});

			this.$groupList.append(view.render().el);
		},

		render: function() {
		}
	});
})(jQuery, Backbone, window.BoomCMS);
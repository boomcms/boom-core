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
				url: '/boomcms/group',
				comparator: 'name'
			});

			this.groups = new groups();

			this.listenTo(this.groups, 'add', this.addGroup);
			this.listenTo(this.groups, 'all sort', this.render);	
			this.groups.fetch();
		},

		createGroup: function(e) {
			e.preventDefault();

			var $el = $(e.target).find('input[type=text]'),
				group = this.groups.create({
					id: null,
					name: $el.val()
				});

			$el.val('');
		},

		addGroup: function(group) {
			var view = new BoomCMS.View.GroupListItem({model: group});

			this.$groupList.append(view.render().el);
		},

		render: function() {
			this.$groupList.empty();
			this.groups.each(this.addGroup, this);

			return this;
		}
	});
})(jQuery, Backbone, window.BoomCMS);
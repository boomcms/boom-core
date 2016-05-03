(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.View.GroupListItem = Backbone.View.extend({
		tagName: 'li',
		template: _.template($('#b-group-list-item').html()),

		events: {
			'click .b-group-delete': 'deleteGroup'
		},

		initialize: function() {
			this.listenTo(this.model, 'change', this.render);
			this.listenTo(this.model, 'destroy', this.remove);
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));

			return this;
		},

		deleteGroup: function() {
			var group = this.model,
				confirmation = new boomConfirmation('Please confirm', 'Are you sure you want to remove this group? <br /><br /> This will delete the group from the database and cannot be undone!');

			confirmation.done(function() {
				group.destroy();
			});
		}
	});
})(jQuery, Backbone, window.BoomCMS);
(function(BoomCMS) {
	'use strict';

	BoomCMS.AssetVersion = BoomCMS.Model.extend({
		getEditedAt: function() {
			return this.get('edited_at');
		},

		getEditedBy: function() {
			if (this.editedBy === undefined) {
				this.editedBy = new BoomCMS.Person(this.get('edited_by'));
			}

			return this.editedBy;
		},

		getThumbnail: function() {
			return '/asset/version/' + this.getId() + '/200/0';
		}
	});
}(BoomCMS));

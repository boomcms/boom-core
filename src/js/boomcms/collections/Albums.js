(function(Backbone, BoomCMS) {
	'use strict';

	BoomCMS.Collections.Albums = Backbone.Collection.extend({
		model: BoomCMS.Album,
		url: BoomCMS.urlRoot + 'album',
		comparator: function(album) {
            return album.getName().toLowerCase();
        },

        findBySlug: function(slug) {
            return this.findWhere({slug: slug});
        }
	});
}(Backbone, BoomCMS));

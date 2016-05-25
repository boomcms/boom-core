(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Filters = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-assets-filters').html())
	});
}(jQuery, Backbone, BoomCMS));
(function($, Backbone, BoomCMS) {
	'use strict';

	BoomCMS.AssetManager.Uploader = Backbone.View.extend({
		tagName: 'div',
		template: _.template($('#b-assets-upload').html())
	});
}(jQuery, Backbone, BoomCMS));
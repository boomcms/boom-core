(function(BoomCMS) {
	'use strict';

	BoomCMS.Template = BoomCMS.Model.extend({
		urlRoot: BoomCMS.urlRoot + 'template',
		needsSave: false,

		fileExists: function() {
			return this.get('file_exists');
		},

		initialize: function() {
			this.pages = new BoomCMS.Collections.Pages();

			this.listenTo(this, 'change', function() {
				this.needsSave = true;
			});

			this.listenTo(this, 'sync', function() {
				this.needsSave = false;
			});
		},

		getDescription: function() {
			return this.get('description');
		},

		getFilename: function() {
			return this.get('filename');
		},

		getName: function() {
			return this.get('name');
		},

		getPages: function() {
			this.pages.findByTemplate(this);
		},

		getTheme: function() {
			return this.get('theme');
		}
	});
}(BoomCMS));

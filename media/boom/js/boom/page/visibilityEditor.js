boomPageVisibilityEditor = function(page) {
	this.changed = false;
	this.page = page;
	this.url = '/cms/page/settings/visibilty/' + this.page.id;

	boomPageVisibilityEditor.prototype.bind = function() {
		var pageVisibilityEditor = this;

		this.dialog.contents
			.on('change', 'input, select', function() {
				pageVisibilityEditor.changed = true;
			});
	};

	boomPageVisibilityEditor.prototype.open = function() {
		var pageVisibilityEditor = this;

		this.dialog = new boomDialog({
			url : pageVisibilityEditor.url,
			title : 'Page visibility',
			open : function() {
				pageVisibilityEditor.bind();
			}
		}).done(function() {
			pageVisibilityEditor.save();
		});
	};

	boomPageVisibilityEditor.prototype.save = function() {
		console.log(this.changed);

		if (this.changed) {
			$.boom.post(this.url, {feature_image_id : this.currentImage})
				.done(function(response) {
					new boomNotification('Page feature image saved');
				});
		}
	};

	this.open();
};
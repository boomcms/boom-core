$.widget('ui.chunkLink', $.ui.chunk, {
	edit : function() {
		var chunkLink = this,
			link = new boomLink(this.getUrl(), this.getTargetPageId(), this.getText());
		
		new boomLinkPicker(link, {text: true})
			.done(function(link) {
				chunkLink.insert(link);
			})
			.fail(function() {
				chunkLink.destroy();	
			});
	},
	
	getTargetPageId : function() {
		return this.element.attr('data-boom-target_page_id');
	},
	
	getText : function() {
		return this.element.attr('data-boom-text');
	},
	
	getUrl : function() {
		return this.element.attr('data-boom-url');
	},

	insert : function(link) {
		if (typeof(link) === 'undefined' || link.getUrl() === '') {
			this.remove();
		} else {
			this._save({
				links: [{
					title: link.getTitle(),
					url: link.getUrl(),
					target_page_id: link.getPageId()
				}]
			});
		}
	}
});
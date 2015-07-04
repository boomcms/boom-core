function boomChunkLocationEditor(page_id, slotname) {
	this.page_id = page_id;
	this.slotname = slotname;
	this.deferred = new $.Deferred();

	boomChunkLocationEditor.prototype.bind = function() {
		var locationEditor = this;

		L.tileLayer( 'http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
			subdomains: ['otile1','otile2','otile3','otile4']
		}).addTo(this.map);
	};

	boomChunkLocationEditor.prototype.open = function() {
		var locationEditor = this;

		this.dialog = new boomDialog({
			url : '/cms/chunk/location/edit/' + this.page_id + '?slotname=' + this.slotname,
			id : 'b-location-editor',
			width: 920,
			open : function() {
				locationEditor.map = L.map(locationEditor.dialog.contents.find('.b-map')[0])
					.setView([51.528837, -0.165653], 13);

				locationEditor.bind();
			}
		})
		.done(function() {
			locationEditor.deferred.resolve();
		})
		.fail(function() {
			locationEditor.deferred.reject();
		});

		return this.deferred;
	};

	return this.open();
};
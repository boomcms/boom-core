function boomChunkLocationEditor(page_id, slotname) {
	this.page_id = page_id;
	this.slotname = slotname;
	this.deferred = new $.Deferred();

	boomChunkLocationEditor.prototype.bind = function() {
		var locationEditor = this;

		L.tileLayer('http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
			subdomains: ['otile1','otile2','otile3','otile4']
		}).addTo(this.map);

		if (this.mapElement.attr('data-lat') != 0 && this.mapElement.attr('data-lng') != 0) {
			this.setMapLocation(this.mapElement.attr('data-lat'), this.mapElement.attr('data-lng'));
		}

		this.element
			.on('click', '#b-location-set', function(e) {
				e.preventDefault();

				locationEditor.setMapLocationFromAddress();
			})
			.on('click', '#b-location-remove', function() {
				if (locationEditor.marker) {
					locationEditor.map
						.removeLayer(locationEditor.marker)
						.setView(null, 13);

					locationEditor.marker = null;
					locationEditor.element.find('#b-location-remove').hide();
				}
			});
	};

	boomChunkLocationEditor.prototype.getAddress = function() {
		return this.element.find('textarea').val();
	};

	boomChunkLocationEditor.prototype.getData = function() {
		var latLng = this.getLocation();

		return {
			title: this.getTitle(),
			address: this.getAddress(),
			postcode: this.getPostcode(),
			lat: latLng.lat,
			lng: latLng.lng
		};
	};

	boomChunkLocationEditor.prototype.getLocation = function() {
		return (this.marker)? this.marker.getLatLng() : {lat: 0, lng: 0};
	};

	boomChunkLocationEditor.prototype.getPostcode = function() {
		return this.element.find('input[name=postcode]').val();
	};

	boomChunkLocationEditor.prototype.getTitle = function() {
		return this.element.find('input[name=title]').val();
	};

	boomChunkLocationEditor.prototype.geocode = function(location) {
		return $.get('//nominatim.openstreetmap.org/search', {
			q: location,
			format: 'json'
		});
	};

	boomChunkLocationEditor.prototype.open = function() {
		var locationEditor = this;

		this.dialog = new boomDialog({
			url : '/cms/chunk/location/edit/' + this.page_id + '?slotname=' + this.slotname,
			id : 'b-location-editor',
			width: 920,
			closeButton: false,
			saveButton: true,
			title: 'Location Chunk Editor',
			open : function() {
				locationEditor.mapElement = locationEditor.dialog.contents.find('#b-location-map');

				locationEditor.map = L.map(locationEditor.mapElement[0])
					.setView([51.528837, -0.165653], 13);

				locationEditor.element = locationEditor.dialog.contents;
				locationEditor.bind();
			}
		})
		.done(function() {
			locationEditor.deferred.resolve(locationEditor.getData());
		})
		.fail(function() {
			locationEditor.deferred.reject();
		});

		return this.deferred;
	};

	boomChunkLocationEditor.prototype.setMapLocation = function(lat, lng) {
		L.Icon.Default.imagePath = '/vendor/boomcms/boom-core/images';

		if ( ! this.marker) {
			this.marker = L.marker([lat, lng], {
				draggable: true
			}).addTo(this.map);
		} else {
			this.marker.setLatLng([lat, lng]);
		}

		this.map.setView([lat, lng], 16);
		this.element.find('#b-location-remove').show();
	};

	boomChunkLocationEditor.prototype.setMapLocationFromAddress = function() {
		var locationEditor = this,
			address = this.getAddress(),
			postcode = this.getPostcode(),
			location = this.geocode(address + ', ' + postcode)
				.done(function(response) {
					if (response.length) {
						locationEditor.setMapLocation(response[0].lat, response[0].lon);
					} else {
						new boomAlert("No location was found matching the address supplied");
					}
				});
	};

	return this.open();
};

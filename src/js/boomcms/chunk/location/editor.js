(function(BoomCMS) {
    'use strict';

    BoomCMS.ChunkLocationEditor = function(pageId, slotname, options) {
        this.pageId = pageId;
        this.slotname = slotname;
        this.deferred = new $.Deferred();
        this.defaultLocation = [51.528837, -0.165653];
        this.options = options;
        this.title = 'Edit location';

        BoomCMS.ChunkLocationEditor.prototype.bind = function() {
            var locationEditor = this;

            L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png', {
                attribution: 'Wikimedia maps beta | Map data &copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
            }).addTo(this.map);

            if (this.mapElement.attr('data-lat') != 0 && this.mapElement.attr('data-lng') != 0) {
                this.setMapLocation(this.mapElement.attr('data-lat'), this.mapElement.attr('data-lng'));
            }

            this.map.on('click', function(e) {
                locationEditor.setMapLocation(e.latlng.lat, e.latlng.lng);
            });

            this.element
                .on('click', '#b-location-set', function(e) {
                    e.preventDefault();

                    locationEditor.setMapLocationFromAddress();
                })
                .on('click', '#b-location-latlng', function(e) {
                    e.preventDefault();

                    locationEditor.setMapLocationFromLatLng();
                })
                .on('click', '#b-location-remove', function() {
                    locationEditor.removeLocation();
                });
        };

        BoomCMS.ChunkLocationEditor.prototype.getAddress = function() {
            return this.element.find('textarea').val();
        };

        BoomCMS.ChunkLocationEditor.prototype.getData = function() {
            var latLng = this.getLocation();

            return {
                title: this.getTitle(),
                address: this.getAddress(),
                lat: latLng.lat,
                lng: latLng.lng
            };
        };

        BoomCMS.ChunkLocationEditor.prototype.getDMS = function() {
            return this.element.find('input[name=dms]').val();
        };

        BoomCMS.ChunkLocationEditor.prototype.getLocation = function() {
            return (this.marker)? this.marker.getLatLng() : {lat: 0, lng: 0};
        };

        BoomCMS.ChunkLocationEditor.prototype.getSearchAddress = function() {
            return this.element.find('input[name=search-address]').val();
        };

        BoomCMS.ChunkLocationEditor.prototype.getTitle = function() {
            return this.element.find('input[name=title]').val();
        };

        BoomCMS.ChunkLocationEditor.prototype.geocode = function(location) {
            return $.get('//nominatim.openstreetmap.org/search', {
                q: location,
                format: 'json',
                limit: 1
            });
        };

        BoomCMS.ChunkLocationEditor.prototype.open = function() {
            var locationEditor = this;

            this.dialog = new BoomCMS.Dialog({
                url : '/boomcms/page/' + this.pageId + '/chunk/edit?slotname=' + this.slotname + '&type=location',
                id : 'b-location-editor',
                width: 920,
                title: this.title,
                open: function() {
                    locationEditor.mapElement = locationEditor.dialog.contents.find('#b-location-map');

                    locationEditor.map = L.map(locationEditor.mapElement[0])
                        .setView(locationEditor.defaultLocation, 13);

                    locationEditor.element = locationEditor.dialog.contents;
                    locationEditor.toggleElements(locationEditor.options);
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

        BoomCMS.ChunkLocationEditor.prototype.removeLocation = function() {
            if (this.marker) {
                this.map
                    .removeLayer(this.marker)
                    .setView(this.defaultLocation, 13);

                this.marker = null;

                this.element.find('#b-location-remove').hide();
            }
        };

        BoomCMS.ChunkLocationEditor.prototype.setMapLocation = function(lat, lng) {
            var locationEditor = this,
                marker;

            L.Icon.Default.imagePath = '/vendor/boomcms/boom-core/images';

            if (!this.marker) {
                marker = this.marker = L.marker([lat, lng], {
                    draggable: true
                })
                .addTo(this.map)
                .on('dragend', function() {
                    var latlng = marker.getLatLng();

                    locationEditor.setMapLocation(latlng.lat, latlng.lng);
                });
            } else {
                this.marker.setLatLng([lat, lng]);
            }

            this.map.setView([lat, lng], 16);

            this.element
                .find('.b-lat input')
                .val(parseFloat(lat).toFixed(6))
                .end()
                .find('.b-lng input')
                .val(parseFloat(lng).toFixed(6))
                .end()
                .find('#b-location-remove')
                .show();
        };

        BoomCMS.ChunkLocationEditor.prototype.setMapLocationFromLatLng = function() {
            var lat = this.element.find('.b-lat input').val(),
                lng = this.element.find('.b-lng input').val();

            this.setMapLocation(Dms.parseDMS(lat), Dms.parseDMS(lng));
        };

        BoomCMS.ChunkLocationEditor.prototype.setMapLocationFromAddress = function() {
            var locationEditor = this,
                address = this.getSearchAddress();

            this.geocode(address)
                .done(function(response) {
                    if (response.length) {
                        locationEditor.setMapLocation(response[0].lat, response[0].lon);
                    } else {
                        BoomCMS.alert('No location was found matching the postcode supplied');
                    }
                });
        };

        BoomCMS.ChunkLocationEditor.prototype.toggleElements = function(options) {
            if (!options.title && !options.address) {
                this.element.find('#b-location-details').hide();
                return;
            }

            if (!options.title) {
                this.element.find('.b-title').hide();
            }

            if (!options.address) {
                this.element.find('.b-address').hide();
            }
        };

        return this.open();
    };
}(BoomCMS));
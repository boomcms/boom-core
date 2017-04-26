(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Router = Backbone.Router.extend({
        history: [],

        routes: {
            '': 'home',
            'upload': 'upload',
            'asset/:asset/:section': 'viewAsset',
            'selection/:selection/:section': 'viewSelection',
            'albums/:album': 'viewAlbum',
            'search?:query': 'searchResults',
            'search(?)': 'search'
        },

        /**
         * Go forward to the last route visited, or home if we're on the first page view
         */
        goToPreviousOrHome: function() {
            var route = (this.history.length > 1) ? this.history[this.history.length - 2] : '';

            this.goTo(route);
        },

        goTo: function(route) {
            this.navigate(route, {trigger: true});
        },

        /**
         * Accepts an array of search parameters, as created from jQuery.serliazeArray()
         *
         */
        goToSearchResults: function(search) {
            var active = {};

            for (var i = 0; i < search.length; i++) {
                if (search[i].value !== '0' && search[i].value !== '') {
                    active[search[i].name] = search[i].value;
                }
            }

            this.goTo('search?' + $.param(active));
        },

        initialize: function(options) {
            var router = this;

            this.assets = options.assets;

            this.listenTo(this, 'route', function () {
                // Limit the size of the history array
                if (router.history.length > 1) {
                    router.history = [router.history[router.history.length - 1]];
                }

                router.history.push(Backbone.history.fragment);
            });
        },

        updateSelection: function(assets, section, options) {
            this.navigate('selection/' + assets.getIdString() + '/' + section, options);
        },

        searchResults: function(queryString) {
            var params = {},
                queryArray = queryString.split('&');

            for (var i = 0; i < queryArray.length; i++) {
                var q = queryArray[i].split('=');

                params[q[0]] = q[1];
            }

            this.trigger('viewSearchResults', params);
        },

        viewAsset: function(id, section) {
            var asset = this.assets.get(id);

            if (asset === undefined) {
                asset = new BoomCMS.Asset({id: id});

                asset.fetch({
                    success: function() {
                        asset.trigger('view', asset, section);
                    }
                });

                this.assets.add(asset);
            } else {
                asset.trigger('view', asset, section);
            }
        },

        viewSelection: function(selection, section) {
            var assetIds = selection.split(',');

            this.trigger('selection', assetIds, section);
        }
    });
}(Backbone, BoomCMS));

(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.Router = Backbone.Router.extend({
        routes: {
            '': 'home',
            'upload': 'upload',
            'albums/create': 'createAlbum',
            'albums/:album': 'viewAlbum',
            'albums/:album/upload': 'upload',
            'albums/:album/asset/:asset/:section': 'viewAssetInAlbum',
            'albums/:album/selection/:selection/:section': 'viewSelectionInAlbum',
            'search/:query/asset/:asset/section': 'viewAssetInSearch',
            'search/:query/selection/:selection/section': 'viewSelectionInSearch',
            'search/:query': 'searchResults',
            'search': 'search',
            'asset/:asset/:section': 'viewAsset',
            'selection/:selection/:section': 'viewSelection'
        },

        createAlbum: function() {
            this.trigger('route:viewAlbum');
        },

        goTo: function(route) {
            this.navigate(route, {trigger: true});
        },

        goToAsset: function(asset) {
            var current = Backbone.history.getFragment(),
                prefix = '',
                albumSlug;

            if (albumSlug = this.getAlbumSlug(current)) {
                prefix = 'albums/' + albumSlug + '/';
            }
    
            this.navigate(prefix + 'asset/' + asset.getId() + '/info', {trigger: true});
        },

        goToPreviousOrHome: function() {
            var current = Backbone.history.getFragment(),
                albumSlug;

            if (albumSlug = this.getAlbumSlug(current)) {
                return this.goTo('albums/' + albumSlug);
            }

            return this.goTo('');
        },

        getAlbumSlug: function(path) {
            var matches = path.match(/^albums\/([-a-zA-Z0-9]+)/i);

            if (matches !== null && typeof matches[1] !== 'undefined') {
                return matches[1];
            }
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

            this.goTo('search/' + $.param(active));
        },

        initialize: function(options) {
            this.assets = options.assets;
        },

        updateSection: function(section) {
            var current = Backbone.history.getFragment(),
                newPath = current.replace(/(.*)\/[a-z]+$/, '$1/' + section);

            this.navigate(newPath);
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

        viewSelection: function(selection, section) {
            var assetIds = selection.split(',');

            this.trigger('selection', assetIds, section);
        }
    });
}(Backbone, BoomCMS));

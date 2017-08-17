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
            'search/:query/asset/:asset/:section': 'viewAssetInSearch',
            'search/:query/selection/:selection/:section': 'viewSelectionInSearch',
            'search/:query': 'searchResults',
            'search': 'search',
            'asset/:asset/:section': 'viewAsset'
        },

        createAlbum: function() {
            this.trigger('route:viewAlbum');
        },

        getContext: function(path) {
            var matches = path.match(/^(albums|search)\/([^\/]+)/i);

            if (matches !== null && typeof matches[2] !== 'undefined') {
                return matches[1] + '/' + matches[2];
            }

            return '';
        },

        goTo: function(route) {
            this.navigate(route, {trigger: true});
        },

        goToAsset: function(asset) {
            var current = Backbone.history.getFragment(),
                context = this.getContext(current),
                prefix = context === '' ? '' : context + '/';
    
            this.navigate(prefix + 'asset/' + asset.getId() + '/info', {trigger: true});
        },

        goToContext: function() {
            var current = Backbone.history.getFragment(),
                context = this.getContext(current);

            return this.goTo(context);
        },

        /**
         * Accepts an array of search parameters, as created from jQuery.serliazeArray()
         *
         */
        goToSearchResults: function(search) {
            this.goTo('search/' + $.param(search));
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
            var current = Backbone.history.getFragment(),
                context = this.getContext(current),
                prefix = context === '' ? '' : context + '/';

            this.navigate(prefix + 'selection/' + assets.getIdString() + '/' + section, options);
        },

        searchResults: function(queryString) {
            this.trigger('viewSearchResults', queryString.toQueryParams());
        }
    });
}(Backbone, BoomCMS));

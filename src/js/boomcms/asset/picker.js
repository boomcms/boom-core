(function(BoomCMS) {
    'use strict';

    BoomCMS.AssetPicker = function(currentAsset, filters) {
        this.currentAsset = typeof(currentAsset) === 'object' ? 
            currentAsset : new BoomCMS.Asset();

        this.deferred = new $.Deferred();
        this.document = $(document);

        this.filters = filters ? filters : {};

        this.url = BoomCMS.urlRoot + 'asset-picker';

        this.assetsUploaded = function(assetIds) {
            if (assetIds.length === 1) {
                this.pick(new BoomCMS.Asset({id: assetIds[0]}));
            } else {
                this.dialog.contents.find('.b-assets-upload-form').assetUploader('reset');
                this.dialog.contents.assetSearch('removeFilters');
                this.dialog.contents.assetSearch('getAssets');
            }
        };

        this.bind = function() {
            var assetPicker = this;

            this.assets
                .on('select', function(asset) {
                    assetPicker.pick(asset);
                })
                .on('view', function(asset) {
                    window.open('/boomcms/asset-manager#asset/' + asset.getId() + '/info');
                });

            this.picker
                .on('click', '#b-assets-picker-close', function() {
                    assetPicker.cancel();
                })
                .on('click', '#b-assets-picker-current-remove', function() {
                    assetPicker.pick(new BoomCMS.Asset());
                })
                .find('.b-assets-upload-form')
                .assetUploader({
                    uploadFinished: function(e, data) {
                        assetPicker.assetsUploaded(data.result);
                    }
                });
        };

        this.cancel = function() {
            this.deferred.reject();
            this.dialog.cancel();
        };

        this.close = function() {
            this.dialog.cancel();
        };

        this.hideCurrentAsset = function() {
            this.picker
                .find('#b-assets-picker-current')
                .hide();
        };

        this.loadPicker = function() {
            var assetPicker = this;

            this.assets = new BoomCMS.Collections.Assets();

            this.dialog = new BoomCMS.Dialog({
                url: this.url,
                onLoad: function() {
                    assetPicker.dialog.contents.parent().css({
                        position: 'fixed',
                        height: '100vh',
                        width: '100vw',
                        transform: 'none',
                        overflow: 'visible'
                    });

                    assetPicker.picker = assetPicker.dialog.contents.find('#b-assets-picker');

                    if (typeof(assetPicker.filters.type) !== 'undefined') {
                        assetPicker.showActiveTypeFilter(assetPicker.filters.type);
                    }

                    assetPicker.dialog.contents.assetSearch({
                        filters: assetPicker.filters,
                        assets: assetPicker.assets
                    });

                    assetPicker.dialog.contents.assetSearch('getAssets');
                    assetPicker.bind();

                    if (assetPicker.currentAsset && assetPicker.currentAsset.getId() > 0) {
                        assetPicker.picker
                            .find('#b-assets-picker-current img')
                            .attr('src', assetPicker.currentAsset.getUrl());
                    } else {
                        assetPicker.hideCurrentAsset();
                    }
                }
            });
        };

        this.open = function() {
            this.loadPicker();

            return this.deferred;
        };

        this.pick = function(asset) {
            this.deferred.resolve(asset);

            this.close();
        };

        /**
         * Selects an option in the type filter select box to show that the type filter is active.
         * Used when the asset picker is opened with an active type filter.
         *
         * @param {string} type
         * @returns {BoomCMS.AssetPicker.prototype}
         */
        this.showActiveTypeFilter = function(type) {
            var $types = this.dialog.contents.find('#b-assets-types');

            $types.find('option').each(function() {
                var $this = $(this);

                if ($this.val().toLowerCase() === type.toLowerCase()) {
                    $types.val($this.val());
                }
            });

            return this;
        };

        return this.open();
    };
}(BoomCMS));

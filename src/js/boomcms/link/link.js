(function(BoomCMS) {
    'use strict';

    BoomCMS.Link = function(url, pageId, title) {
        this.url = url? url : '';
        this.pageId = pageId? pageId : 0;
        this.title = title? title : '';

        BoomCMS.Link.prototype.isAsset = function() {
            return this.getUrl().indexOf('/asset/') === 0;
        };

        BoomCMS.Link.prototype.isExternal = function() {
            return this.getUrl() !== '' && this.getUrl().substring(0,1) !== '/';
        };

        BoomCMS.Link.prototype.isHttp = function() {
            return this.url.substring(0,7) === 'http://';
        };

        BoomCMS.Link.prototype.isHttps = function() {
            return this.url.substring(0,8) === 'https://';
        };

        BoomCMS.Link.prototype.isInternal = function() {
            return this.pageId > 0 || this.getUrl().substring(0,1) === '/';
        };

        BoomCMS.Link.prototype.isMailto = function() {
            return this.url.substring(0,7) === 'mailto:';
        };

        BoomCMS.Link.prototype.isTel = function() {
            return this.url.substring(0,4) === 'tel:';
        };

        BoomCMS.Link.prototype.getAsset = function() {
            var assetId = this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)(\/(view|download|thumb))?/i, '$1');

            return new BoomCMS.Asset({id: assetId});
        };

        BoomCMS.Link.prototype.getAssetAction = function() {
            if (this.isAsset()) {
                return this.getUrl().replace(/\/asset\/(\d+)([\/\d]*?)\/(view|download)/i, '$3');
            }
        };

        BoomCMS.Link.prototype.getUrl = function() {
            if (this.isTel()) {
                return this.getTelUrl();
            }

            return (this.url === 'http://') ? '' : this.makeUrlRelative();
        };

        BoomCMS.Link.prototype.getPageId = function() {
            return parseFloat(this.pageId, 10);
        };

        BoomCMS.Link.prototype.getTelUrl = function() {
            return 'tel:' + this.url.replace(/[^+\d]+/g, '');
        };

        BoomCMS.Link.prototype.getTitle = function() {
            return this.title;
        };

        BoomCMS.Link.prototype.makeUrlRelative = function() {
            return (this.url.indexOf(window.location.hostname) > -1) ?
                this.url.replace(/^https?:\/\//, '').replace(window.location.hostname, '') :
                this.url;
        };
    };
}(BoomCMS));

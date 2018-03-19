(function(BoomCMS) {
    'use strict';

    BoomCMS.ChunkAssetEditor = function(pageId, slotname, visibleElements) {
        this.pageId = pageId;
        this.slotname = slotname;
        this.visibleElements = visibleElements;

        BoomCMS.ChunkAssetEditor.prototype.bind = function() {
            var chunkAssetEditor = this;

            this.assetElement.on('click', function() {
                BoomCMS.AssetPicker(chunkAssetEditor.asset)
                    .done(function(asset) {
                        chunkAssetEditor.setAsset(asset);
                    });
            });

            this.link.on('click', 'button', function() {
                var $this = $(this);

                new BoomCMS.LinkPicker(new BoomCMS.Link($this.parent().find('input').val()))
                    .done(function(link) {
                        chunkAssetEditor.setLink(link.getUrl());
                    });
            });
        };

        BoomCMS.ChunkAssetEditor.prototype.dialogOpened = function() {
            this.title = this.dialog.contents.find('.b-title');
            this.caption = this.dialog.contents.find('.b-caption');
            this.link = this.dialog.contents.find('.b-link');
            this.assetElement = this.dialog.contents.find('a');
            this.asset = new BoomCMS.Asset({id: this.assetElement.attr('data-asset-id')});

            this.bind();
            this.toggleElements();
        };

        BoomCMS.ChunkAssetEditor.prototype.getData = function() {
            return {
                asset_id : this.asset.getId(),
                caption : this.caption.find('textarea').val(),
                url : this.link.find('input').val(),
                title : this.title.find('textarea').val()
            };
        };

        BoomCMS.ChunkAssetEditor.prototype.open = function() {
            var chunkAssetEditor = this;
            this.deferred = new $.Deferred();

            this.dialog = new BoomCMS.Dialog({
                url : '/boomcms/page/' + this.pageId + '/chunk/edit?slotname=' + this.slotname + '&type=asset',
                id : 'b-assets-chunk-editor',
                width: 900,
                open: function() {
                    chunkAssetEditor.dialogOpened();
                }
            })
            .done(function() {
                chunkAssetEditor.deferred.resolve(chunkAssetEditor.getData());
            })
            .fail(function() {
                chunkAssetEditor.deferred.reject();
            });

            return this.deferred;
        };

        BoomCMS.ChunkAssetEditor.prototype.setAsset = function(asset) {
            this.asset = asset;
            this.assetElement.attr('data-asset-id', asset.getId());

            if(asset.getId()) {
                var $img = this.assetElement.find('img');

                if ( ! $img.length ) {
                    $img = $('<img />');
                    this.assetElement.find('p').replaceWith($img);
                }
    
                $img.attr('src', asset.getUrl('view', 400));
            } else {
                this.assetElement.find('img').replaceWith('<p>None set</p>');
            }
        };

        BoomCMS.ChunkAssetEditor.prototype.setLink = function(link) {
            this.link
                .find('input')
                .val(link)
                .blur();
        };

        BoomCMS.ChunkAssetEditor.prototype.toggleElements = function() {
            var elements = ['title', 'caption', 'link'],
                i,
                element;

            for (i = 0; i < elements.length; i++) {
                element = elements[i];

                if ( ! this.visibleElements[element]) {
                    this[element].hide();
                }
            }
        };

        return this.open();
    };
}(BoomCMS));
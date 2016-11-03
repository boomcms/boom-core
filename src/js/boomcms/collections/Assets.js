(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.Collections.Assets = Backbone.Collection.extend({
        model: BoomCMS.Asset,
        url: BoomCMS.urlRoot + 'asset',
        comparator: 'name',

        addTag: function(tag) {
            return $.post(this.url + '/tags', {
                assets: this.getAssetIds(),
                tag: tag
            });
        },

        destroy: function() {
            var assets = this;

            return $.ajax({
                url: this.url,
                type: 'delete',
                data: {
                    'assets[]': this.getAssetIds()
                }
            })
            .done(function() {
                assets.each(function(model) {
                    model.trigger('destroy');
                });
            });
        },
    
        download: function(filename) {
            var url = this.url + '/download?',
                assets = [];

            for (var i = 0; i < this.models.length; i++) {
                assets[i] = 'assets[]=' + this.models[i].getId();
            }

            url = url + assets.join('&');
            url = url + '&filename=' + filename;

            window.location = url;
        },

        getAssetIds: function() {
            return this.pluck('id');
        },

        getIdString: function() {
            return this.getAssetIds().join(',');
        },

        getTags: function() {
            if (this.allTags === undefined) {
                this.allTags = $.get(this.url + '/tags', {
                    assets: this.getAssetIds()
                });
            }

            return this.allTags;
        },

        parse: function(data) {
            return data.assets;
        },

        removeTag: function(tag) {
            return $.ajax(this.url + '/tags', {
                type: 'delete',
                data: {
                    assets: this.getAssetIds(),
                    tag: tag
                }
            });
        },

        tag: function() {
            var assetSelection = this,
                url = this.url + 'tags/list/' + this.assets.join('-'),
                dialog;

            dialog = new BoomCMS.Dialog({
                url: url,
                title: 'Asset tags',
                width: 440,
                cancelButton : false,
                onLoad: function() {
                    dialog.contents.find('#b-tags').assetTagSearch({
                        addTag: function(e, tag) {
                            assetSelection.addTag(tag);
                        },
                        removeTag: function(e, tag) {
                            assetSelection.removeTag(tag);
                        }
                    });
                }
            });
        }
    });
}(Backbone, BoomCMS));

(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAsset = BoomCMS.AssetManager.ViewSelection.extend({
        templateSelector: '#b-assets-view-template',

        bind: function() {
            var view = this,
                asset = this.model;

            BoomCMS.AssetManager.ViewSelection.prototype.bind.call(this);

            this.$el
                .on('click', '.b-assets-revert', function(e) {
                    e.preventDefault();

                    asset.revertToVersion($(this).parents('li').attr('data-version-id'));
                })
                .on('click', '.b-assets-save', function() {
                    asset
                        .set(view.$('form').serializeJSON())
                        .save();

                    BoomCMS.Notification('Asset details saved');
                })
                .on('click', '#b-assets-thumbnail-change', function(e) {
                    e.preventDefault();

                    BoomCMS.AssetPicker(asset.getThumbnailAssetId())
                        .done(function(thumbnail) {
                            asset
                                .set('thumbnail_asset_id', thumbnail.getId())
                                .save();
                        });
                });

            this.$('#b-asset-replace form')
                .assetUploader({
                    dropArea: this.$('#b-asset-replace'),
                    asset: asset
                });
        },

        initialize: function(options) {
            var view = this;

            this.selection = new BoomCMS.Collections.Assets([this.model]);

            this.listenTo(this.model, 'sync', function() {
                view.render(view.getSection());
            });

            this.listenTo(this.model, 'revert', function() {
                BoomCMS.Notification('This asset has been reverted to the previous version');
            });

            this.listenTo(this.model, 'change:image revert', function() {
                this.router.goToAsset(this.model);
            });

            this.init(options);
        },

        initImageEditor: function() {
            var asset = this.model;

            this.$('.b-asset-imageeditor').imageEditor({
                save: function(e, blob) {
                    asset.replaceWith(blob);
                }
            });
        },

        initUploader: function() {
            var view = this,
                asset = this.model;

            this.$('.b-assets-upload').assetUploader({
                asset: asset,
                uploadFinished: function(e, data) {
                    asset.set(data.result);
                    asset.trigger('change:image');

                    view.render('info');
                }
            });
        },

        render: function() {
            this.$el.html(this.template({
                asset: this.model,
                section: this.section
            }));

            this.initUploader();
            this.initImageEditor();
            this.viewAlbums();

            if (this.eventsBound === false) {
                this.bind();
            }
            
            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

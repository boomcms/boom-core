(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAsset = BoomCMS.AssetManager.ViewSelection.extend({
        routePrefix: 'asset',
        tagName: 'div',
        tagsDisplayed: false,
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

                    BoomCMS.notify("Asset details saved");
                })
                .on('remove', function() {
                    this.$('.b-assets-upload').assetUploader('reset');
                });

            this.$('.b-assets-upload').assetUploader({
                asset: asset,
                uploadFinished: function(e, data) {
                    asset.set(data.result);
                    asset.trigger('change:image');

                    view.render('info');
                }
            });
        },

        initialize: function(options) {
            var asset = this.model;

            this.selection = new BoomCMS.Collections.Assets([this.model]);

            this.listenTo(this.model, 'revert', function() {
                BoomCMS.notify('This asset has been reverted to the previous version');
            });

            this.listenTo(this.model, 'change:image revert', function() {
                this.render('info');
            });

            this.$el
                .on('click', '#b-assets-thumbnail-change', function(e) {
                    e.preventDefault();

                    new boomAssetPicker(asset.getThumbnailAssetId())
                        .done(function(thumbnail) {
                            asset
                                .set('thumbnail_asset_id', thumbnail.getId())
                                .save();
                        });
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

        render: function(section) {
            this.$el.html(this.template({
                asset: this.model,
                section: section
            }));

            if (section === 'tags') {
                this.showTags();
            }

            this.bind();
            this.initImageEditor();

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

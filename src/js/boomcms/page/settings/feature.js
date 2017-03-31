$.widget('boom.pageSettingsFeature', {
    changed: false,

    getImagesInPage: function() {
        return top.$('body:first-of-type')
            .find('img[src^="/asset/"]')
            .map(function() {
                var $this = $(this),
                    src = $this.attr('src').replace(/\/asset\/(\d+)(.*?)/, '$1');

                return src.indexOf('/') > -1 ? src.substring(0, src.indexOf('/')) : src;
            });
    },

    bind: function() {
        var pageFeatureEditor = this,
            $imagesInPageContainer = this.element.find('.images-in-page');

        this.element
            .on('click', '#b-page-feature-remove', function() {
                pageFeatureEditor.removeFeature();
            })
            .on('click', '#b-page-feature-edit', function() {
                BoomCMS.AssetPicker(pageFeatureEditor.currentImage)
                    .done(function(asset) {
                        pageFeatureEditor.setFeature(asset);
                    });
            })
            .on('click', '.b-button-cancel', function() {
                pageFeatureEditor.setFeature(pageFeatureEditor.intial);
                pageFeatureEditor.changed = false;
            }).on('click', '.b-button-save', function() {
                pageFeatureEditor.save();
            })
            .on('click', '.b-page-feature-set', function() {
                pageFeatureEditor.setFeature(new BoomCMS.Asset({id: $(this).attr('data-asset-id')}));
            });
console.log(this.imagesInPage);
        if (this.imagesInPage.length) {
            for (var i = 0; i < this.imagesInPage.length; i++) {
                var asset = new BoomCMS.Asset({id: this.imagesInPage[i]});
console.log(asset);
                $imagesInPageContainer.append('<li><a href=\'#\' class=\'b-page-feature-set\' data-asset-id=\'' + asset.getId() + '\'><img src=\'' + asset.getUrl() + '\' /></a></li>');
            }
        } else {
            $imagesInPageContainer.parent('section').hide();
        }

        if (!this.currentImage.getId()) {
            this.hasNoFeatureImage();
        }
    },

    _create: function() {
        this.currentImage = this.initial = new BoomCMS.Asset({id: this.element.find('#b-page-feature-current').attr('src').replace(/\/asset\/(\d+)(.*)/, '$1')});
        this.imagesInPage = this.getImagesInPage();
        this.bind();
    },

    hasNoFeatureImage: function() {
        this.element.find('#b-page-feature-current').hide();
        this.element.find('#b-page-feature-remove').prop('disabled', true);
        this.element.find('#b-page-feature-none').show();
    },

    removeFeature: function() {
        this.setFeature(new BoomCMS.Asset());
    },

    save: function() {
        var pageFeatureEditor = this;

        if (this.changed) {
            pageFeatureEditor.options.page.setFeatureImage(this.currentImage)
                .done(function() {
                    BoomCMS.Notification('Page feature image saved');
            
                    pageFeatureEditor._trigger('done', null, pageFeatureEditor.currentImage);
                });
        }
    },

    setFeature: function(asset) {
        if (asset && asset.getId() !== this.currentImage.getId()) {
            this.changed = true;
            this.currentImage = asset;

            if (asset.getId()) {
                this.element.find('#b-page-feature-current').attr('src', asset.getUrl('thumb', 500)).show();
                this.element.find('#b-page-feature-remove').prop('disabled', false);
                this.element.find('#b-page-feature-none').hide();
            }
        }

        if (!asset || !asset.getId()) {
            this.hasNoFeatureImage();
        }
    }
});
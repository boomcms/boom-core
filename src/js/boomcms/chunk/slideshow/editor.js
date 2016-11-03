(function(BoomCMS) {
    'use strict';

    BoomCMS.ChunkSlideshowEditor = function(page_id, slotname, options) {
        this.page_id = page_id;
        this.slotname = slotname;
        this.options = options;

        BoomCMS.ChunkSlideshowEditor.prototype.bind = function() {
            var slideshowEditor = this;

            this.toggleSections();

            this.dialog.contents
                .on('click', '#b-slideshow-editor-delete', function() {
                    slideshowEditor.deferred.resolveWith({});

                    slideshowEditor.dialog.close();
                })
                .on('change', '#b-slideshow-editor-slides input[type=radio]', function() {
                    var slide = slideshowEditor._getSlideDetails($(this));

                    slide.asset = new BoomCMS.Asset({id: slide.asset_id});
                    slideshowEditor.editSlide(slide);
                })
                .on('click', '#b-slideshow-editor-current-delete', function(e) {
                    e.preventDefault();

                    slideshowEditor.deleteCurrentSlide();
                })
                .on('click', '#b-slideshow-editor-current form a', function(e) {
                    e.preventDefault();

                    var currentAssetId = $(this).find('img').attr('src').replace(/\/asset\/(\d+)(.*?)/, '$1');
                    slideshowEditor.editCurrentSlideAsset(new BoomCMS.Asset({id: currentAssetId}));
                })
                .on('keydown, change', '#b-slideshow-editor-current form input[type=text]', function() {
                    var $this = $(this),
                        name = $this.attr('name'),
                        val = $this.val();

                    slideshowEditor.getCurrentSlide().data(name, val);
                })
                .on('click', '#b-slideshow-editor-current .b-slideshow-link button', function(e) {
                    e.preventDefault();

                    var $input = $(this).parent().find('input'),
                        currentSlide = slideshowEditor.getCurrentSlide();

                    new BoomCMS.LinkPicker(new BoomCMS.Link($input.val(), currentSlide.data('page')))
                        .done(function(link) {
                            currentSlide
                                .data('url', link.getUrl())
                                .data('page', link.getPageId());

                            $input
                                .val(link.getUrl())
                                .blur();
                        });
                })
                .on('click', '#b-slideshow-editor-add', function(e) {
                    e.preventDefault();

                    slideshowEditor.addSlide();
                })
                .find('#b-slideshow-editor-slides')
                .sortable({});
        };

        BoomCMS.ChunkSlideshowEditor.prototype.addSlide = function() {
            var slideshowEditor = this;

            BoomCMS.AssetPicker(null, {type: 'image'})
                .done(function(asset) {
                    var $new_slide = $('<li><label><input type="radio" value="" name="slide" data-asset="' + asset.getId() + '"  /><img src="' + asset.getUrl('view', 0, 100) + '" /></label></li>');

                    slideshowEditor.dialog.contents
                        .find('#b-slideshow-editor-slides')
                        .append($new_slide);

                    $new_slide.find('input').attr('checked', true);
                    slideshowEditor.editSlide({asset : asset});
                });
        };

        BoomCMS.ChunkSlideshowEditor.prototype.deleteCurrentSlide = function() {
            this.getCurrentSlide()
                .parents('li')
                .remove()
                .end()
                .end()
                .find('#b-slideshow-editor-current')
                .find('.default')
                .show()
                .end()
                .find('form')
                .hide();
        };

        BoomCMS.ChunkSlideshowEditor.prototype.editCurrentSlideAsset = function(currentAsset) {
            var slideshowEditor = this;

            BoomCMS.AssetPicker(currentAsset)
                .done(function(asset) {
                    slideshowEditor.dialog.contents
                        .find('#b-slideshow-editor-current img, #b-slideshow-editor-slides input[type=radio]:checked + img')
                        .attr('src', asset.getUrl('view', 300, 0))
                        .siblings('input')
                        .attr('data-asset', asset.getId());
                });
        };

        BoomCMS.ChunkSlideshowEditor.prototype.editSlide = function(slide) {
            this.dialog.contents.find('#b-slideshow-editor-current')
                .find('.default')
                .hide()
                .end()
                .find('form')
                .show()
                .find('img')
                .attr('src', slide.asset.getUrl('view', 300, 0))
                .end()
                .find('input[name=title]')
                .val(slide.title)
                .end()
                .find('input[name=caption]')
                .val(slide.caption)
                .end()
                .find('input[name=url]')
                .val(new BoomCMS.Link(slide.url).getUrl())
                .end()
                .find('input[name=linktext]')
                .val(slide.linktext);
        };

        BoomCMS.ChunkSlideshowEditor.prototype.getAllSlideDetails = function() {
            var slideshowEditor = this;

            return this.dialog.contents
                .find('#b-slideshow-editor-slides li')
                .map(function(index, el) {
                    var $el = $(el);

                    return slideshowEditor._getSlideDetails($el.find('input'));
                })
                .get();
        };

        BoomCMS.ChunkSlideshowEditor.prototype.getCurrentSlide = function() {
            return this.dialog.contents.find('#b-slideshow-editor-slides input[type=radio]:checked');
        };

        BoomCMS.ChunkSlideshowEditor.prototype._getSlideDetails = function($element) {
            return {
                asset_id: $element.attr('data-asset'),
                url: $element.data('url'),
                page: $element.data('page'),
                caption: $element.data('caption'),
                title: $element.data('title'),
                linktext: $element.data('linktext')
            };
        };

        BoomCMS.ChunkSlideshowEditor.prototype.open = function() {
            var slideshowEditor = this;
            this.deferred = new $.Deferred();

            this.dialog = new BoomCMS.Dialog({
                url : '/boomcms/page/' + this.page_id + '/chunk/edit?slotname=' + this.slotname + '&type=slideshow',
                id : 'b-slideshow-editor',
                width: 920,
                open: function() {
                    slideshowEditor.bind();
                }
            })
            .done(function() {
                slideshowEditor.deferred.resolve(slideshowEditor.getAllSlideDetails());
            })
            .fail(function() {
                slideshowEditor.deferred.reject();
            });

            return this.deferred;
        };

        BoomCMS.ChunkSlideshowEditor.prototype.toggleSections = function() {
            var sections = ['title', 'caption', 'link', 'linktext'];

            for (var i in sections) {
                var section = sections[i];

                if (this.options[section] === false) {
                    this.dialog.contents.find('.b-slideshow-' + section).hide();
                }
            }
        };

        return this.open();
    };
}(BoomCMS));
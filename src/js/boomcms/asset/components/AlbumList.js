(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.AlbumList = Backbone.View.extend({
        el: $('<ul class="b-assets-album-list"></ul>'),
        selectedClass: 'selected',

        initialize: function(options) {
            this.albums = options.albums;
            this.selected = options.selected;

            this.template = _.template($('#b-assets-album-list-template').html());

            this.listenTo(this.albums, 'change add', this.render);
            this.listenTo(this.albums, 'remove', this.removeAlbum);
            this.listenTo(this.selected, 'add', this.selectAlbum);
            this.listenTo(this.selected, 'remove', this.unselectAlbum);
        },

        removeAlbum: function(album) {
            this.$el
                .find('li[data-album=' + album.getId() + ']')
                .fadeOut(600, function() {
                    $(this).remove();
                });
        },

        render: function() {
            var view = this;

            this.$el.html($(this.template({
                albums: this.albums
            })));

            this.$('li').removeClass('selected');

            if (this.selected !== undefined) {
                this.selected.each(function(album) {
                    view.$('li[data-album=' + album.getId() + ']').addClass(view.selectedClass);
                });
            }

            return this;
        },

        selectAlbum: function(album) {
            this.$('li[data-album=' + album.getId() + ']').addClass(this.selectedClass);
        },

        unselectAlbum: function(album) {
            this.$('li[data-album=' + album.getId() + ']').removeClass(this.selectedClass);
        }
    });
}(jQuery, Backbone, BoomCMS));

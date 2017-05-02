(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.AlbumList = Backbone.View.extend({
        el: $('<ul class="b-assets-album-list"></ul>'),

        initialize: function(options) {
            this.albums = options.albums;

            this.template = _.template($('#b-assets-album-list-template').html());

            this.listenTo(this.albums, 'change add', this.render);
            this.listenTo(this.albums, 'remove', this.removeAlbum);
        },

        removeAlbum: function(album) {
            this.$el
                .find('li[data-album=' + album.getId() + ']')
                .fadeOut(600, function() {
                    $(this).remove();
                });
        },

        render: function() {
            this.$el.html($(this.template({
                albums: this.albums
            })));

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

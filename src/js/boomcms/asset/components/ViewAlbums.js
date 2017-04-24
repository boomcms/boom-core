(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewAlbum = Backbone.View.extend({
        tagName: 'div',

        initialize: function() {
            this.template = _.template($('#b-assets-view-album-template').html());
        },

        render: function() {
            this.$el.html($(this.template({
                album: this.model
            })));

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));

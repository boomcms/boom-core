(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.AssetManager.ViewSelection = Backbone.View.extend({
        routePrefix: 'selection',
        tagsDisplayed: false,
        tagName: 'div',
        templateSelector: '#b-assets-selection-template',

        bind: function() {
            var view = this,
                selection = this.selection;

            this.$el
                .on('click', '.b-settings-close', function(e) {
                    e.preventDefault();

                    view.router.navigate(view.returnTo, {trigger: true});
                })
                .on('click', '.b-assets-delete', function() {
                    selection.destroy();
                })
                .on('click', 'a[data-section]', function() {
                    var section = $(this).attr('data-section');

                    view.router.navigate(view.routePrefix + '/' + selection.getIdString() + '/' + section, {trigger: true});
                })
                .on('submit', '#b-assets-download-filename', function(e) {
                    e.preventDefault();

                    var filename = $(this).find('input[name=filename]').val();

                    selection.download(filename);
                });

            this.$el.ui();
        },

        getSection: function() {
            return this.$('a[data-section].selected').attr('data-section');
        },

        init: function(options) {
            var view = this;

            this.assets = options.assets;
            this.router = options.router;
            this.returnTo = options.returnTo;

            this.template = _.template($(this.templateSelector).html());

            this.listenTo(this.selection, 'add remove', function() {
                this.render(view.getSection());
            });

            this.listenTo(this.selection, 'destroy', function() {
                view.close();
            });
        },

        initialize: function(options) {
            this.selection = options.selection;
            this.albums = options.albums;

            this.init(options);
        },

        render: function(section) {
            this.$el.html($(this.template({
                selection: this.selection,
                section: section
            })));

            if (section === 'albums') {
                this.viewAlbums();
            }
            
            var $about = this.$('.about');
            $about.text($about.text().replace(':count', this.selection.length));

            this.bind();

            this.listenTo(this.assets, 'select', function() {
                this.router.updateSelection(this.selection, this.getSection());
            });

            return this;
        },

        viewAlbums: function() {
            var view = new BoomCMS.AssetManager.AlbumList({
                albums: this.albums
            });

            this.$('#b-asset-albums > div').html(view.render().el);
        }
    });
}(jQuery, Backbone, BoomCMS));

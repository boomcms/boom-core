$.widget('ui.chunkLinkset', $.ui.chunk, {
    _create: function() {
        this.editorOptions = this.getOptions();
        this.bind();
    },

    edit: function() {
        var chunkLinkset = this;
        return new BoomCMS.ChunkLinksetEditor(this.options.currentPage.id, this.options.name, this.editorOptions)
            .done(function(data) {
                chunkLinkset.insert(data);
            })
            .fail(function() {
                chunkLinkset.destroy();
            });
    },

    getOptions: function() {
        var $el = this.element,
            options = {
                title: 'linkset-title',
                linkAssets: 'link-asset',
                linkText: 'link-text',
                linkTitle: 'link-title'
            };

        for (var i in options) {
            options[i] = $el.attr('data-boom-' + options[i]) === '1'
                || $el.hasClass(options[i])
                || $el.find('.' + options[i]).length > 0;
        }

        options.limit = parseInt($el.attr('data-boom-limit'));
        return options;
    },

    insert: function(links) {
        if (typeof(links) === 'undefined' || links.length === 0) {
            this.remove();
        } else {
            this._save(links);
        }
    }
});

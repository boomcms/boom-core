$.widget('ui.chunkLocation', $.ui.chunk, {
    edit: function() {
        var chunk = this,
            options = this.getOptions(),
            editor = new BoomCMS.ChunkLocationEditor(this.options.page, this.options.name, options);

        editor
            .done(function(data) {
                if (data.lat != 0 && data.lng != 0) {
                    chunk._save(data);
                } else {
                    chunk.remove();
                }
            })
            .always(function() {
                chunk.bind();
            });
    },

    getOptions: function() {
        return {
            address: this.element.attr('data-boom-address') === '1',
            title: this.element.attr('data-boom-title') === '1'
        };
    }
});

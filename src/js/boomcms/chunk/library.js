$.widget('ui.chunkLibrary', $.ui.chunk, {
    clearFilters: function() {
        var $el = this.dialog.contents;

        $el.find('select').prop('selectedIndex', 0);
        $el.find('input[type=text]').val('');
    },

    edit: function() {
        var library = this;

        this.dialog = new BoomCMS.Dialog({
            url: this.options.currentPage.baseUrl + 'chunk/edit?type=library&slotname=' + this.options.name,
            width: 700,
            onLoad: function() {
                library.dialog.contents
                    .on('click', '.b-button.clear', function() {
                        library.clearFilters();
                    })
                    .find('select[name=album]')
                    .chosen();
            }
        })
        .always(function() {
            library.bind();
        })
        .done(function() {
            library._save(library.getData());
        });
    },

    getData: function() {
        var $el = this.dialog.contents;

        return {
            params: {
                type: $el.find('#b-assets-types :selected').val(),
                order: $el.find('#b-assets-sortby :selected').val(),
                limit: $el.find('input[name=limit]').val(),
                album: $el.find('select[name=album] :selected').map(function() {
                    return $(this).val();
                }).toArray()
            }
        };
    }
});
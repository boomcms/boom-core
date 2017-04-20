$.widget( 'boom.pageEditor', {
    elements : {},

    openDialogs : 0,

    options : {
        editable : false
    },

    _create: function() {
        this.page = this.options.page;
        window.BoomCMS.page = this.page;

        this.document = $(top.document);

        this.page.toolbar = this.toolbar = this.document
            .find('#b-editor-iframe')
            .pageToolbar({ // This should probably be called editorIframe as we're calling this on the iframe. Then we need another widget which is specifically for the toolbar.
                page : this.page,
                publishable : this.options.publishable
            })
            .data('boom-pageToolbar');

        this.watchForDialogs();

        this.showTextDiff();

        if (this.options.editable) {
            this.createChunks();
        }
    },

    createChunks: function() {
        var self = this;

        this.document.contents()
            .find('[data-boom-chunk]')
            .each(function() {
                var $this = $(this),
                    type = $this.data('boom-chunk'),
                    widgetName = 'chunk' + type.ucfirst();

                $this[widgetName]({
                    type : type,
                    name : $this.data('boom-slot-name'),
                    template : $this.data('boom-slot-template'),
                    id : $this.data('boom-target')? parseInt($this.data('boom-target'), 10) : 0,
                    page : $this.data('boom-page'),
                    currentPage : self.page,
                    chunkId : $this.data('boom-chunk-id')
                });
            })
            .end()
            .find('#b-page-title')
            .pageTitle({
                currentPage : self.page
            })
            .end()
            .find('.b-page-tags')
            .each(function() {
                var $this = $(this);

                $this
                    .addClass(BoomCMS.editableClass)
                    .on('click', function(e) {
                        e.preventDefault();

                        window.BoomCMS.page.toolbar.showSettings('tags');
                    });
            })
            .end()
            .find('.b-page-visibility')
            .each(function() {
                var $this = $(this);

                $this
                    .addClass(BoomCMS.editableClass)
                    .on('click', function(e) {
                        e.preventDefault();

                        window.BoomCMS.page.toolbar.showSettings('visibility');
                    });
            })
            .end()
            .find('.b-page-featureimage')
            .each(function() {
                var $this = $(this);

                $this
                    .addClass(BoomCMS.editableClass)
                    .on('click', function(e) {
                        e.preventDefault();

                        window.BoomCMS.page.toolbar.showSettings('feature');
                    });
            });
    },

    showTextDiff: function() {
        var $diff = this.toolbar.element.contents().find('#b-history-diff');

        if ($diff.length) {
            var type = $diff.attr('data-type'),
                slotname = $diff.attr('data-slotname');

            this.document.contents()
            .find('[data-boom-chunk="' + type + '"][data-boom-slot-name="' + slotname + '"]')
            .html($diff.html());
        }
    },

    watchForDialogs: function() {
        var editor = this,
            toolbar = this.page.toolbar;

        $(top.window)
            .on('boom:dialog:open', function() {
                editor.openDialogs++;

                if (editor.openDialogs === 1) {
                    toolbar.maximise();
                    toolbar.hide();
                    editor.document.find('body').css('overflow', 'hidden');
                }
            })
            .on('boom:dialog:close', function() {
                editor.openDialogs--;

                if (editor.openDialogs === 0) {
                    toolbar.minimise();
                    toolbar.show();
                    editor.document.find('body').css('overflow', 'auto');
                }
            });
    }
});
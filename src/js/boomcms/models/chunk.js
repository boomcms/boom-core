(function($, BoomCMS) {
    'use strict';

    BoomCMS.Chunk = function(page_id, type, slotname) {
        this.page_id = page_id;
        this.slotname = slotname;
        this.type = type;
        this.urlPrefix = '/boomcms/page/' + this.page_id + '/chunk/';

        /**
         * To remove a chunk save it with no data.
         *
         * @param string template
         * @returns {jqXHR}
         */
        BoomCMS.Chunk.prototype.delete = function(template) {
            return this.save({
                template: template,
                force: true
            });
        };

        BoomCMS.Chunk.prototype.save = function(data) {
            data.slotname = this.slotname;
            data.type = this.type;

            return $.post(this.urlPrefix + 'save', data);
        };
    };
}($, BoomCMS));
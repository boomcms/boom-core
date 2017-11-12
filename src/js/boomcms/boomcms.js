(function($, Backbone) {
    'use strict';

    function BoomCMS() {
        this.editableClass = 'b-editable';
        this.urlRoot = '/boomcms/';
        this.Collections = {};

        this.Model = Backbone.Model.extend({
            addRelationship: function(type, id) {
                return $.ajax({
                    url: this.urlRoot + '/' + this.id + '/' + type + '/' + id,
                    type: 'put'
                });
            },

            getId: function() {
                return this.id;
            },

            removeRelationship: function(type, id) {
                return $.ajax({
                    type: 'delete',
                    url: this.urlRoot + '/' + this.id + '/' + type + '/' + id
                });
            }
        }),

        BoomCMS.prototype.init = function(options) {
            if (typeof(top.$) === 'undefined') {
                top.$ = $;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            this.assetTypes = options.assetTypes || {};
            this.user = options.user;

            $('#b-topbar, body').ui();
            $('#b-menu-button').boomcmsMenuButton();
        };

        BoomCMS.prototype.getCsrfToken = function() {
            return $('meta[name="csrf-token"]').attr('content');
        },

        BoomCMS.prototype.getTimezone = function() {
            var key = 'boomcms.timezone';

            if (!this.Storage.get(key)) {
                var tz = jstz.determine() || 'UTC';

                this.Storage.save(key, tz.name());
            }

            return this.Storage.get(key);
        };
    }

    window.BoomCMS = top.BoomCMS = new BoomCMS();
}(jQuery, Backbone));

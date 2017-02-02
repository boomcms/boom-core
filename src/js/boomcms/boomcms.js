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
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            this.assetTypes = options.assetTypes || {};
            this.user = options.user;

            $('#b-topbar, body').ui();
            $('#b-menu-button').boomcmsMenuButton();
        };

        BoomCMS.prototype.getTimezone = function() {
            var key = 'boomcms.timezone';

            if (!sessionStorage.getItem(key)) {
                var tz = jstz.determine() || 'UTC';

                sessionStorage.setItem(key, tz.name());
            }

            return sessionStorage.getItem(key);
        };
    }

    window.BoomCMS = top.BoomCMS = new BoomCMS();
}(jQuery, Backbone));

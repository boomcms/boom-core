(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager = Backbone.View.extend({
        el: 'body',
        
        initialize: function(options) {
            this.templates = new BoomCMS.Collections.Templates(options.templates);
            this.$container = this.$('#b-container');

            this.router = new BoomCMS.TemplateManager.Router({
                templates: this.templates
            });

            this.listenTo(this.router, 'home', this.home);
            this.listenTo(this.templates, 'view', this.viewTemplate);

            Backbone.history.start();
        },

        home: function() {
            this.$container.html(new BoomCMS.TemplateManager.TemplateTable({
                templates: this.templates
            }).render().el);
        },

        viewTemplate: function(template) {
            var view = new BoomCMS.TemplateManager.TemplateView({
                model: template
            });

            this.$container.html(view.render().el);
        }
    });
}(jQuery, Backbone, BoomCMS));
;(function(Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.Router = Backbone.Router.extend({
        routes: {
            '': 'home',
            'template/:template/pages': 'viewTemplatePages'
        },

        initialize: function(options) {
            this.templates = options.templates;

            this.listenTo(this.templates, 'change:id', this.editGroupOnCreate);
            this.listenTo(this.people, 'destroy', this.goBackOnPersonDelete);
        },

        home: function() {
            this.trigger('home');
        },

        viewTemplatePages: function(id) {
            var template = this.templates.get(id);

            template.trigger('view', template);
        }
    });
}(Backbone, BoomCMS));
;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplatePageItem = Backbone.View.extend({
        tagName: 'tr',
        template: _.template($('#b-template-page').html()),

        render: function() {
            this.$el
                .html(this.template({
                    page: this.model
                }));

            this.$('time').text(moment.unix(this.$('time').attr('datetime')).fromNow());

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplateTable = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-template-list').html()),

        initialize: function(options) {
            this.templates = options.templates;
        },

        addTemplate: function(template) {
            var view = new BoomCMS.TemplateManager.TemplateTableItem({model: template}),
                $el = view.render().$el;

            this.$('tbody').append($el);
        },

        render: function() {
            var table = this;

            this.$el
                .html(this.template({
                    templates: this.templates
                }));

            this.templates.each(function(template) {
                table.addTemplate(template);
            });

            this.$('table')
                .tablesorter({
                    /**
                    Return the value of any form input in a table cell, or the text content of the cell.
                    */
                    textExtraction: function(node) {
                        var text = $(node)
                            .find('input')
                            .val();

                        return (typeof text === 'undefined') ? $(node).text() : text;
                    }
                });

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplateTableItem = Backbone.View.extend({
        tagName: 'tr',
        template: _.template($('#b-template-row').html()),

        events: {
            'click .delete': 'deleteTemplate',
            'change input': 'setAttribute',
            'keyup input': 'setAttribute',
            'paste input': 'setAttribute',
            'blur input': 'saveTemplate'
        },

        initialize: function() {
            this.listenTo(this.model, 'destroy', this.remove);
        },

        deleteTemplate: function(e) {
            e.preventDefault();

            var template = this.model;

            BoomCMS.Confirmation('Please confirm', 'Are you sure you want to delete the template \'' + template.getName() + '\'?')
                .done(function() {
                    template.destroy();    
                });
        },

        render: function() {
            this.$el
                .html(this.template({
                    template: this.model
                }));

            if (!this.model.fileExists()) {
                this.$el.addClass('b-templates-nofile');
            }

            return this;
        },

        saveTemplate: function() {
            if (this.model.needsSave === true) {
                this.model.save();
    
                BoomCMS.notify('Template "' + this.model.getName() + '" has been saved');
            }
        },

        setAttribute: function(e) {
            var $el = $(e.target);

            this.model.set($el.attr('name'), $el.val());
        }
    });
}(jQuery, Backbone, BoomCMS));;(function($, Backbone, BoomCMS) {
    'use strict';

    BoomCMS.TemplateManager.TemplateView = Backbone.View.extend({
        tagName: 'div',
        template: _.template($('#b-template-pages').html()),

        initialize: function() {
            this.listenTo(this.model.pages, 'sync', this.addPages);
        },

        addPages: function(pages) {
            var table = this;

            pages.each(function(page) {
                var view = new BoomCMS.TemplateManager.TemplatePageItem({
                    model: page
                });

                table.$('tbody').append(view.render().el);
            });

            this.$('table').tablesorter();
        },

        render: function() {
            this.$el
                .html(this.template({
                    template: this.model
                }));
            
            this.model.getPages();

            return this;
        }
    });
}(jQuery, Backbone, BoomCMS));
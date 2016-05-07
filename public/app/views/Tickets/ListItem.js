define(['jquery', 'hbs!templates/Tickets/ListItem', 'backbone', 'marionette'],
    function ($, template, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            template: template
        });
    }
);
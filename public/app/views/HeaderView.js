define(['jquery', 'hbs!templates/Header', 'backbone', 'models/User', 'marionette'],
    function ($, template, Backbone, User) {
        //ItemView provides some default rendering logic
        return Backbone.Marionette.ItemView.extend({
            template: template,
            model: new User(window.CFUser)
        });
    }
);
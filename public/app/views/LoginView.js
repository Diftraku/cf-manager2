define(['App', 'backbone', 'marionette', 'jquery', 'models/User', 'hbs!templates/Login', 'jquery.growl'],
    function (App, Backbone, Marionette, $, User, template) {
        return Backbone.Marionette.ItemView.extend({
            template: template,
            model: new User(),
            events: {
                'click .login': 'processLogin',
                'change': 'updateModel'
            },
            updateModel: function (event) {
                var target = event.target;
                var change = {};
                change[target.name] = target.value;
                this.model.set(change);
            },
            processLogin: function () {
                this.model.save(null, {
                    success: function (model) {
                        $.growl.notice({ message: "Login successful, reloading...", timeout: function () {
                            window.location.hash = '#';
                            window.location.reload(true);
                        }});
                    },
                    error: function (model, response, options) {
                        $.growl.error({ message: "Login failed!" });
                    }
                });
            }
        });
    });
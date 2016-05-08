define(["jquery", "backbone"],
    function ($, Backbone) {
        return Backbone.Model.extend({
            urlRoot: '/api/user',
            defaults: {
                username: ''
            }
        });
    }
);
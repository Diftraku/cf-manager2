define(["jquery", "backbone"],
    function ($, Backbone) {
        return Backbone.Model.extend({
            urlRoot: "/ticket",
            defaults: {
                first_name : "",
                last_name  : "",
                type       : 1,
                email      : "",
                created_on : "",
                created_by : "",
                modified_on: "",
                modified_by: "",
                hash       : "",
                metadata   : ""
            }
        });
    }
);
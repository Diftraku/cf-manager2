define(["jquery", "backbone"],
    function ($, Backbone) {
        return Backbone.Model.extend({
            urlRoot: "/api/ticket",
            defaults: {
                first_name : "",
                last_name  : "",
                type       : 0,
                email      : "",
                address    : "",
                postal_code: "",
                city       : "Helsinki",
                country    : "Finland",
                date       : "",
                hash       : "",
                check      : "",
                reference  : "",
                status     : 0,
                claimed    : 0,
                quantity   : 1,
                order_id   : "",
                receipt_id : "",
                shirt1     : "",
                shirt2     : "",
                swag       : "",
                notes      : ""
            }
        });
    }
);
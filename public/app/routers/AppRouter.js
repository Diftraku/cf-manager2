define(['marionette', 'controllers/AppController'], function (Marionette, Controller) {
    return Marionette.AppRouter.extend({
        appRoutes: {
            "": "index",
            "user/logout": "logout",
	        "user/login": "login",
            "tickets/list/:page": "ticketsList",
            "tickets/list": "ticketsList",
	        "tickets/add": "ticketsAdd",
	        "tickets/import": "ticketsImport",
            "tickets/claim": "ticketsClaim",
            "tickets/view/:id": "ticketsView",
            "tickets/edit/:id": "ticketsEdit"
        },
	    onRoute: function (name, path, arguments) {
		    if (name !== "login" && window.CFUser.logged_in !== true) {
				// Stop processing the route somehow
			    window.location.replace('/#user/login');
		    }
	    }
    });
});
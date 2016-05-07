define(['App', 'backbone', 'marionette', 'views/MainView', 'views/HeaderView', 'views/LoginView'],
    function (App, Backbone, Marionette, MainView, HeaderView, LoginView) {
        return Marionette.Controller.extend({
            initialize: function (options) {
	            if (window.CFUser.logged_in == true) {
                    App.menuRegion.show(new HeaderView());
	            }
            },
            index: function () {
                App.mainRegion.show(new MainView());
            },
            logout: function () {
                window.User.destroy({
                    success: function () {
                        $.growl.notice({ message: "Logout successful, reloading...", timeout: function () {
                            window.location.hash = '#';
                            window.location.reload(true);
                        }});
                    }
                });
            },
	        login: function () {
			    App.mainRegion.show(new LoginView());
		    },
            ticketsList: function (page) {
                page = _.isEmpty(page) ? 1 : page;
                requirejs(['models/Ticket', 'models/TicketCollection', 'views/Tickets/List'], function (Ticket, TicketCollection, ListView) {
                    var collection = new TicketCollection();
                    collection.goTo(page, {
                        success: function (collection) {
                            App.mainRegion.show(new ListView({
                                collection: collection
                            }));
                        }
                    });
                });
            },
	        ticketsAdd: function () {
		        requirejs(['models/Ticket', 'views/Tickets/Add'], function (Ticket, TicketView) {
			        App.mainRegion.show(new TicketView({
				        model: new Ticket()
			        }));
		        });
	        },
            ticketsImport: function () {
                requirejs(['views/Tickets/Import'], function (ImportView) {
                    App.mainRegion.show(new ImportView());
                });
            },
            ticketsClaim: function () {
                requirejs(['views/Tickets/Lookup'], function (LookupView) {
                    App.mainRegion.show(new LookupView());
                });
            },
            ticketsView: function (id) {
                id = _.isEmpty(id) ? 1 : id;
                requirejs(['models/Ticket', 'views/Tickets/View'], function (Ticket, TicketView) {
                    var model = new Ticket({id: id});
                    model.fetch({
                        success: function (model) {
                            App.mainRegion.show(new TicketView({
                                model: model
                            }));
                        }
                    });
                });
            },
            ticketsEdit: function (id) {
                id = _.isEmpty(id) ? 1 : id;
                requirejs(['models/Ticket', 'views/Tickets/Edit'], function (Ticket, TicketView) {
                    var model = new Ticket({id: id});
                    model.fetch({
                        success: function (model) {
                            App.mainRegion.show(new TicketView({
                                model: model
                            }));
                        }
                    });
                });
            }
        });
    });
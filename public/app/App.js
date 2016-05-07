define(['jquery', 'backbone', 'marionette', 'bootstrap'],
	function ($, Backbone, Marionette) {
    var App = new Marionette.Application();

    App.addRegions({
        menuRegion: "#menu",
        mainRegion: "#content",
	    modalTitle: "#modal .modal-title",
	    modalBody: "#modal .modal-body"
    });

    App.addInitializer(function () {
        Backbone.history.start();
    });

    return App;
});
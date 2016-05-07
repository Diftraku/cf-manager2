require.config({
	baseUrl: 'app/',
	paths : {
		backbone : 'lib/backbone',
		underscore : 'lib/underscore',
		jquery : 'lib/jquery-2.1.1',
		marionette : 'lib/backbone.marionette',
		hbs: 'lib/hbs',
		'jquery.growl': 'lib/jquery.growl',
		paginator: 'lib/backbone.paginator.min',
		bootstrap : 'lib/bootstrap'
	},
	shim : {
		jquery : {
			exports : 'jQuery'
		},
		'jquery.growl': {
			deps: ['jquery'],
			exports: 'jQuery.growl'
		},
		paginator: {
			deps: ['backbone'],
			exports: 'Backbone.Paginator'
		},
		bootstrap: {
			deps: ['jquery']
		},
		underscore : {
			exports : '_'
		},
		backbone : {
			deps : ['jquery', 'underscore'],
			exports : 'Backbone'
		},
		marionette : {
			deps : ['jquery', 'underscore', 'backbone'],
			exports : 'Marionette'
		}
	},
    hbs: {
	    helpers: true,
	    i18n: false,
	    templateExtension: 'hbs',
	    partialsUrl: ''
    },
	waitSeconds: 0
});

require(["App", "routers/AppRouter", "controllers/AppController"],
	function (App, AppRouter, Controller) {
		App.appRouter = new AppRouter({
			controller:new Controller()
		});
		console.log('Starting CF Admin');
		App.start();
	}
);
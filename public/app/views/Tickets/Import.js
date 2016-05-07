define(['jquery', 'hbs!templates/Tickets/Import', 'backbone', 'marionette'],
    function ($, template, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            template: template,
	        serializeData: function(){
		        var index, data = {};
		        data.types = [];
		        for(index in window.CFMeta.types) {
			        data.types.push({
				        value: index,
				        label: window.CFMeta.types[index].label
			        });
		        }
		        return data;
	        }
        });
    }
);
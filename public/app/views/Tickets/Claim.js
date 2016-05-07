define(['jquery', 'hbs!templates/Tickets/Claim', 'backbone', 'marionette', 'jquery.growl'],
    function ($, template, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            events: {
	            "click .claim": "claim",
	            "click .cancel": function() {
		            window.location.reload();
	            },
	            "click .finish": function() {
		            window.location.reload();
	            }
            },
            template: template,
            onBeforeRender: function () {
                $('#modal').modal('hide');
            },
            serializeData: function(){
			    var data = this.model.toJSON();
			    var index, selected;
			    data.states = [];
			    for(index in window.CFMeta.states) {
				    selected = index == data.status ? ' selected' : '';
				    data.states.push({
					    value: index,
					    label: window.CFMeta.states[index].label,
					    selected: selected
				    });
			    }
			    data.types = [];
			    for(index in window.CFMeta.types) {
				    selected = index == data.type ? ' selected' : '';
				    data.types.push({
					    value: index,
					    label: window.CFMeta.types[index].label,
					    selected: selected
				    });
			    }
			    data.claimed = data.claimed == 1 ? ' selected' : '';
			    return data;
	        },
            claim: function() {
	            this.model.set({claimed: 1, notes: $('#notes').val()});
	            this.model.urlRoot = "/api/tickets/claim";
	            this.model.save({claimed: 1, notes: $('#notes').val()}, {patch: true, success: function(m, data) {
		           $('#modal').modal();
	            }});
            }
        });
    }
);
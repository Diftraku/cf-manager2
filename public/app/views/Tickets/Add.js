define(['jquery', 'hbs!templates/Tickets/View', 'backbone', 'marionette'],
    function ($, template, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            events: {
                "click .back": function() {
	                window.history.back();
                },
                "click .cancel": function() {
	                window.history.back();
                },
                "click .save": "saveModel",
		        "change": "updateModel"
	        },
            template: template,
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
                data.edit = true;
                return data;
            },
	        updateModel: function(e) {
		        if (this.model.has(e.target.id)) {
			        this.model.set(e.target.id, e.target.value);
		        }
	        },
	        saveModel: function() {
				this.model.save({}, {
					success: function (model, response, options) {
						console.log(response);
						$.growl.success({message: "Ticket created", timeout: function() {
							window.location.href = '#/tickets/view/'+response.id;
						}});
					},
					error: function (model, response, options) {
						console.log(model, response, options);
						$.growl.error({message: "Failed to create ticket"});

					}
				})
	        }
        });
    }
);
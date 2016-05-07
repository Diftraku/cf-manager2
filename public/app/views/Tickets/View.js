define(['jquery', 'hbs!templates/Tickets/View', 'backbone', 'marionette'],
    function ($, template, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            events: {
	            "click .edit": function() {
		            window.location.hash = window.location.hash.replace(/view/, "edit");
	            },
	            "click .print": function() {
		            window.location.href = '/api/ticket/pdf/'+this.model.get('hash')+'.pdf';
	            },
	            "click .back": function() {
		            window.location.href = '#/tickets/list';
	            }
            },
            template: template,
            ui: {
                inputs: "input",
                textarea: "textarea",
                selects: "select"
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
            onRender: function(){
                $(this.ui.inputs).attr('readonly', 'true');
                $(this.ui.textarea).attr('readonly', 'true');
                $(this.ui.selects).attr('disabled', 'disabled');
            }
        });
    }
);
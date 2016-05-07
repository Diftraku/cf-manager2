define(['App', 'jquery', 'hbs!templates/Tickets/Lookup', 'hbs!templates/Tickets/SelectionList', 'hbs!templates/Tickets/SelectionListItem', 'views/Tickets/Claim', 'views/Tickets/List', 'models/Ticket', 'backbone', 'marionette', 'jquery.growl'],
    function (App, $, lookupTemplate,  selectionListTemplate, selectionListItemTemplate,claimView, listView, ticketModel, Backbone) {
        return Backbone.Marionette.ItemView.extend({
            events: {
	            "click .lookup": "lookup",
                "click .select": "select",
                "click .cancel": function() {

	            },
                "change": "updateData"
            },
            template: lookupTemplate,
            onBeforeRender: function(){
                var model = Backbone.Model.extend({
                    urlRoot: "/api/tickets/claim",
                    defaults: {
                        hash: '',
                        check: '',
	                    last_name: '',
	                    email: ''
                    }
                });
                this.model = new model;
            },
            updateData: function(e) {
                if (this.model.has(e.target.id)) {
                    this.model.set(e.target.id, e.target.value);
                }
            },
            lookup: function() {
                var hash, check, last_name, email;
	            hash = this.model.get('hash');
	            check = this.model.get('check');
	            last_name = this.model.get('last_name');
	            email = this.model.get('email');
	            if (hash.length > 0 && hash.length > 40) {
		            $.growl.error({ message: "QR Code is too long, check input and try again." });
	            }
	            else if (check.length > 0 && check.length > 40) {
		            $.growl.error({ message: "Check Number is too long, check input and try again." });
	            }
	            else if (hash.length == 0 && check.length == 0 && last_name.length == 0 && email.length == 0) {
                    $.growl.error({ message: "All fields are empty! Cannot lookup with nothing." });
                }
                else {
		            var self = this;
                    this.model.save(null, {success: function(m, data){
	                    var model = new ticketModel(data);
	                    App.mainRegion.show(new claimView({model: model}));
                    }, error: function(m, resp, options){
	                    if (resp.status == 300) {
		                    // Multiple results
		                    var modal = $('#modal');
                            //modal.find('div.modal-dialog').addClass('modal-lg');
                            //modal.find('h4.modal-title').text('Multiple results');
                            self.ticketCollection = new Backbone.Collection(resp.responseJSON.data);
		                    App.modalBody.show(
			                    new listView(
				                    {
					                    collection: self.ticketCollection,
					                    template: selectionListTemplate,
					                    itemView: Backbone.Marionette.ItemView.extend({
						                    template: selectionListItemTemplate,
						                    tagName: "tr",
						                    serializeData: function(){
							                    var status = this.model.attributes.status;
							                    var type = this.model.attributes.type;
							                    return _.extend(this.model.toJSON(), {
								                    statusDescription: window.CFMeta.states[status].description,
								                    statusLabel: window.CFMeta.states[status].label,
								                    typeLabel: window.CFMeta.types[type].label,
								                    date: new Date(this.model.attributes.date).toLocaleDateString('fi-FI')
							                    });
						                    }
					                    })
				                    }
			                    )
		                    )
                            //modal.find('a.btn-primary').addClass('select').text('Select').show();
                            modal.modal();
	                    }
	                    else if (resp.status == 400) {
		                    // Ticket already claimed
		                    var modal = $('#modal');
		                    modal.find('h4.modal-title').text('Ticket has already been claimed');
		                    modal.find('div.modal-body p').text(resp.responseJSON.message);
		                    modal.find('a.btn-primary').hide();
		                    modal.modal();
	                    }
                    }});
                }
            },
            select: function () {
                var id = $('input[name=ticketSelection]:checked').val();
		        var ticket = this.ticketCollection.get(id);
                var form = $('form');
                form.trigger('reset');
                this.model.set({last_name: '', email: '', check: '', hash: ticket.get('hash')});
                form.find('input[name=hash]').val(ticket.get('hash'));
                $('#modal').modal('hide');
            }
        });
    }
);
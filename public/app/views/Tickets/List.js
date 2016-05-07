define(['jquery', 'hbs!templates/Tickets/List', 'hbs!templates/Tickets/ListItem', 'hbs!templates/Pagination', 'backbone', 'marionette'],
    function ($, template, itemTemplate, paginationTemplate, Backbone) {
        return Backbone.Marionette.CompositeView.extend({
            events: {
                "click .prev": "prevPage",
                "click .next": "nextPage",
                "click .first": "firstPage",
                "click .last": "lastPage"
            },
            template: template,
            itemView: Backbone.Marionette.ItemView.extend({
                template: itemTemplate,
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
            }),
            paginationView: Backbone.Marionette.ItemView.extend({
                template: paginationTemplate,
                tagName: "tr",
                initialize: function (options) {
                    this.options = options;
                },
                serializeData: function(){
                    console.log(this.options);
                    return this.options;
                }
            }),
            itemViewContainer: "tbody",
            ui: {
                paginator: "#paginator"
            },
            onCompositeRendered: function (){
                this.renderPagination();
            },
            renderPagination: function() {
                var data = {
                    pagination: _.isEmpty(this.collection.pagination) ? {} : this.collection.pagination,
                    currentPage: _.isEmpty(this.collection.currentPage) ? 1 : this.collection.currentPage,
                    totalPages: _.isEmpty(this.collection.totalPages) ? 1 : this.collection.totalPages
                };
                data.pagination.page = data.currentPage;
                data.pagination.options = {limit: 3};
                data.pagination.isLastPage = (data.currentPage == data.totalPages);
                data.pagination.isFirstPage = (data.currentPage == 1);
                var view = new this.paginationView(data);
                this.ui.paginator.html(view.render().el);
            },
            uriPath: "#tickets/list",
            prevPage: function () {
                if (!(this.collection.currentPage == 1)) {
                    window.location.hash = this.uriPath + "/" + (this.collection.currentPage-1);
                }
            },
            nextPage: function () {
                if (!(this.collection.currentPage == this.collection.totalPages)) {
                    window.location.hash = this.uriPath + "/" + (this.collection.currentPage+1);
                }
            },
            firstPage: function () {
                if (!(this.collection.currentPage == 1)) {
                    window.location.hash = this.uriPath + "/" + 1;
                }
            },
            lastPage: function () {
                if (!(this.collection.currentPage == this.collection.totalPages)) {
                    window.location.hash = this.uriPath + "/" + this.collection.totalPages;
                }
            },
            howManyPer: function (count) {
                count = (typeof count == 'undefined') ? 20 : count;
                switch (count) {
                    case 20:
                    case 40:
                    case 60:
                        this.collection.howManyPer(count);
                        break;
                    default:
                        this.collection.howManyPer(20);
                        break;
                }
            }
        });
    }
);
define(["models/Ticket", "jquery", "backbone", "paginator"],
    function (Ticket, $, Backbone, Paginator) {
        return Backbone.Paginator.requestPager.extend({
            model : Ticket,
            paginator_core : {
                type : 'GET',
                dataType : 'json',
                url : '/ticket'
            },
            paginator_ui : {
                firstPage : 1,
                currentPage : 1,
                perPage : 20,
                totalPages : 10
            },
            server_api : {
                'filter' : '',
                'limit' : function() {
                    return this.perPage
                },
                'offset': function () {
                    return this.currentPage * this.perPage - this.perPage;
                },
                // field to sort by
                'order_by' : 'id'
            },
            parse : function(response) {
                var tickets, count;
                if (response.hasOwnProperty('data')) {
                    var data = response.data;
                    if (data.hasOwnProperty('tickets')) {
                        tickets = data.tickets;
                    }
                    if (data.hasOwnProperty('count')) {
                        count = data.count;
                    }
                    console.log(tickets, count);
                    this.totalPages = Math.ceil(count / this.perPage);
                    this.pagination = {
                        page: this.currentPage,
                        pageCount: this.totalPages,
                        isLastPage: this.currentPage == this.lastPage,
                        isFirstPage: this.currentPage == this.paginator_ui.firstPage
                    };
                    return tickets;
                }
            }
        });
    }
);
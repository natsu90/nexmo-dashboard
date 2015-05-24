define(['jquery', 'underscore', 'backbone','collections/number/NumberCollection','text!templates/number/listCallsTemplate.html'], function($, _, Backbone, NumberCollection, listCallsTemplate) {
    var CallLogsView = Backbone.View.extend({
        el: $("#page"),
        render: function() {
        	var that = this;
        	this.collection = new NumberCollection();
        	this.collection.fetch({
                calls: true,
               success: function(collection, response) {
                   var template = _.template(listCallsTemplate, {
                       logs: collection.models
                   });
                   that.$el.html(template);

                  call_logs_datatable = $('#call-logs').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                  });

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/calls"]').addClass('active');
               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
        },
    });
    return CallLogsView;
});
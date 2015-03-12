define(['jquery', 'underscore', 'backbone','collections/message/InboundCollection','text!templates/message/listInboundTemplate.html'], function($, _, Backbone,InboundCollection, listInboundTemplate) {
    var InboundView = Backbone.View.extend({
        el: $("#page"),
        render: function() {
        	var that = this;
        	this.collection = new InboundCollection();
        	this.collection.fetch({
               success: function(collection, response) {
                   var template = _.template(listInboundTemplate, {
                       inbound: that.collection.models
                   });
                   that.$el.html(template);

                  inbound_datatable = $('#inbound').dataTable({
                    "order": [[ 4, "desc" ]],
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false,
                    "fnCreatedRow": function (nRow, aData, iDataIndex) {
                        $(nRow).attr('data-id', aData[0]);
                      },
                    "aoColumnDefs": [
                        { "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] },
                    ]
                  });

                  $inbound_menu = $('.sidebar-menu li').removeClass('active').has('a[href="#/inbound"]').addClass('active').find('a');
                  $inbound_notification = $inbound_menu.find('.notification');
                  if($inbound_notification.length > 0)
                    $inbound_notification.fadeOut('slow');
               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
          this.collection.live({pusherChannel: pusher_subscriber, eventType: "inbound"});
        },
    });
    return InboundView;
});
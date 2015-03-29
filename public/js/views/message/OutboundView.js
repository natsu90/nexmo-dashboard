define(['jquery', 'underscore', 'backbone','collections/message/OutboundCollection','text!templates/message/listOutboundTemplate.html'], function($, _, Backbone,OutboundCollection, listOutboundTemplate) {
    var OutboundView = Backbone.View.extend({
        el: $("#page"),
        render: function() {
        	var that = this;
        	this.collection = new OutboundCollection();
        	this.collection.fetch({
               success: function(collection, response) {
                   var template = _.template(listOutboundTemplate, {
                       outbound: collection.models
                   });
                   that.$el.html(template);

                  outbound_datatable = $('#outbound').dataTable({
                    "order": [[ 4, "desc" ]],
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false,
                    "fnRowCallback": function (nRow, aData, iDataIndex) {
                        
                        var label = 'warning';
                        switch(aData[5].toLowerCase())
                        {
                          case 'failed':
                            label = 'danger'; break;
                          case 'queued':
                            label = 'default'; break;
                          case 'sent':
                            label = 'info'; break;
                          case 'delivered':
                            label = 'success'; break;
                        }

                        $(nRow).attr('data-id', aData[0]).find('td:eq(4)').html('<label class="label label-'+label+'">'+aData[5].charAt(0).toUpperCase()+aData[5].slice(1)+'</label>');
                      },
                    "aoColumnDefs": [
                        { "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] },
                    ]
                  });

                  $outbound_menu = $('.sidebar-menu li').removeClass('active').has('a[href="#/outbound"]').addClass('active').find('a');
                  $outbound_notification = $outbound_menu.find('.notification');
                  if($outbound_notification.length > 0)
                    $outbound_notification.fadeOut('slow');
               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
          this.collection.live({pusherChannel: pusher_subscriber, eventType: "outbound"});
        },
    });
    return OutboundView;
});
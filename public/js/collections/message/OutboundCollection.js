define(['jquery', 'underscore', 'backbone', 'models/message/OutboundModel', 'backbone_live'], function($, _, Backbone, OutboundModel, backbone_live) {
    var OutboundCollection = Backbone.LiveCollection.extend({
        model: OutboundModel,
        url: "api/v1/outbound",
        parse: function(data) {
            return data.outbounds;
        },
        add: function(data) {

            $outbound_menu = $('.sidebar-menu > li > a[href="#/outbound"]');
            $outbound_notification = $outbound_menu.find('.notification');
            if($outbound_notification.length == 0) {
                $outbound_menu.append('<span class="label label-success pull-right notification">0</span>').fadeIn('slow');
                $outbound_notification = $outbound_menu.find('.notification');
            }
            $outbound_notification.text(parseInt($outbound_notification.text()) + 1).fadeOut().fadeIn();
            if(Backbone.history.fragment == 'outbound')
                $outbound_notification.fadeOut('slow');

        	if(typeof outbound_datatable !== 'undefined') {
        		var row_data = [data.id, data.from, data.to, data.text, data.created_at, data.status];
        		outbound_datatable.fnAddData(row_data);
        	}
        },
        update: function(data) {
        	if(typeof outbound_datatable !== 'undefined') {
        		outbound_datatable.fnUpdate([data.id, data.from, data.to, data.text, data.created_at, data.status], $('tr[data-id='+data.id+']'));
        	}
        }
    });
    return OutboundCollection;
});
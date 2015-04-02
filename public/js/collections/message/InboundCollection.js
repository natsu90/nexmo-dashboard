define(['jquery', 'underscore', 'backbone', 'models/message/InboundModel', 'backbone_live'], function($, _, Backbone, InboundModel, backbone_live) {
    var InboundCollection = Backbone.LiveCollection.extend({
        model: InboundModel,
        url: "api/v1/inbound",
        parse: function(data) {
            return data.inbounds;
        },
        add: function(data) {

            $inbound_menu = $('.sidebar-menu > li > a[href="#/inbound"]');
            $inbound_notification = $inbound_menu.find('.notification');
            if($inbound_notification.length == 0) {
                $inbound_menu.append('<span class="label label-success pull-right notification">0</span>').fadeIn('slow');
                $inbound_notification = $inbound_menu.find('.notification');
            }
            $inbound_notification.text(parseInt($inbound_notification.text())+1).fadeOut().fadeIn();
            if(Backbone.history.fragment == 'inbound' || Backbone.history.fragment == '')
                $inbound_notification.remove();

        	if(typeof inbound_datatable !== 'undefined') {
        		var row_data = [data.id, data.from, data.to, data.text, data.created_at, '<a href="#/send/'+data.from+'"><i class="fa fa-pencil"></i> Reply</a>'];
        		inbound_datatable.fnAddData(row_data);
        	}
        }
    });
    return InboundCollection;
});
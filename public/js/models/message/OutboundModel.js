define(['underscore', 'backbone'], function(_, Backbone) {
    var OutboundModel = Backbone.Model.extend({
    	urlRoot: 'api/v1/outbound'
    });
    return OutboundModel;
});
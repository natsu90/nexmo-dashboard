define(['underscore', 'backbone'], function(_, Backbone) {
    var NumberModel = Backbone.Model.extend({
    	urlRoot: 'api/v1/number'
    });
    return NumberModel;
});
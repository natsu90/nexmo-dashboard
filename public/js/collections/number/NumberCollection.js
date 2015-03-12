define(['jquery', 'underscore', 'backbone', 'models/number/NumberModel', 'backbone_live'], function($, _, Backbone, NumberModel, backbone_live) {
    var NumberCollection = Backbone.LiveCollection.extend({
        model: NumberModel,
        url: "api/v1/number",
        parse: function(data) {
            return data.numbers;
        },
        add: function(data) {
            console.log('number added');
        },
        update: function(data) {
            console.log('number updated');
        },
        remove: function(data) {
            console.log('number removed');
        }
    });
    return NumberCollection;
});
define(['jquery', 'underscore', 'backbone', 'models/number/CallLogsModel'], function($, _, Backbone, CallLogsModel) {
    var CallLogsCollection = Backbone.Collection.extend({
        model: CallLogsModel,
        url: "api/v1/call_logs",
        parse: function(data) {
            return data.call_logs;
        }
    });
    return CallLogsCollection;
});
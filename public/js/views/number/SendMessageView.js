define([
  'jquery', 
  'underscore', 
  'backbone',
  'collections/number/NumberCollection',
  'text!templates/message/sendMessageTemplate.html',
  'models/message/OutboundModel',
  ], function($, _, Backbone, NumberCollection, sendMessageTemplate, OutboundModel) {
    var SendMessageView = Backbone.View.extend({

        el: $("#page"),
        /*
        events: {
          "submit form": "sendMessage"
        },
        */
        sendMessage: function(e) {
          e.preventDefault();

          var separators = [',', '/', ';', '\\\n'],
              unused_symbols = ['\\+', ' ', '-', '\\(', '\\)'],
              senders = $('#to').val().split(new RegExp(separators.join('|'), 'g')),
              message = $('#message').val().trim();

          if(message == "")
            return;

          $('#send-btn').button('loading');
          var regex_foo = new RegExp(unused_symbols.join('|'), 'g'),
              outbound_data = {
                from: $('#from').val(),
                to: senders.map(function(num) {
                  return num.trim().replace(regex_foo, '');
                }).filter(function(num) {
                  return num.trim() !== "";
                }).join(';'),
                text: message
              };
          console.log(outbound_data);
           
          outbound = new OutboundModel(outbound_data);
          outbound.save();
          /*
          senders.forEach(function(sender) {
            if(sender.trim() == "")
              return;

            var outbound_data = {
              from: $('#from').val(),
              to: sender,
              text: message
            };
          
            var outbound = new OutboundModel(outbound_data);
            outbound.save();
          
          });
          */
          window.location.hash = '/outbound';
        }, 
        render: function(number) {
        	var that = this;
        	this.collection = new NumberCollection();
        	this.collection.fetch({
               success: function(collection, response) {
                   var template = _.template(sendMessageTemplate, {
                       number: that.collection.models
                   });

                   that.$el.html(template);
                   that.$('form').off('submit').on('submit', that.sendMessage);
                   $('#to').val(number);

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/send"]').addClass('active');
               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
          this.collection.live({pusherChannel: pusher_subscriber, eventType: "number"});
        }
    });
    return SendMessageView;
});
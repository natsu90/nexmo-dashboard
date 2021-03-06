define([
  'jquery',
  'underscore',
  'backbone',
  'views/message/InboundView',
  'views/message/OutboundView',
  'views/message/SendMessageView',
  'views/number/UpdateNumberView',
  'views/number/BuyNumberView',
  'views/number/CallLogsView',
], function($, _, Backbone, InboundView, OutboundView, SendMessageView, UpdateNumberView, BuyNumberView, CallLogsView) {
 
  var AppRouter = Backbone.Router.extend({
    routes: {
      // Define some URL routes
      'inbound': 'showInbound',
      'outbound': 'showOutbound',
      'send(/:number)': 'sendMessage',
      'buy(/:country)': 'buyNumber',
      'number/(:number)': 'updateNumber',
      'calls': 'showCalls',
 
      // Default
      '*actions': 'showInbound'
    }
  });
 
  var initialize = function(){
 
    var app_router = new AppRouter;
 
    app_router.on('route:showInbound', function () {
 
        var inboundView = new InboundView();
        inboundView.render();
    });
 
    app_router.on('route:showOutbound', function () {
 
        var outboundView = new OutboundView();
        outboundView.render();
    });

    app_router.on('route:sendMessage', function (number) {
 
        var sendMessageView = new SendMessageView();
        sendMessageView.render(number);
    });

    app_router.on('route:updateNumber', function (number) {
 
        var updateNumberView = new UpdateNumberView();
        updateNumberView.render(number);
    });

    app_router.on('route:buyNumber', function (country) {
 
        var buyNumberView = new BuyNumberView();
        buyNumberView.render(country);
    });

    app_router.on('route:showCalls', function () {
 
        var callLogsView = new CallLogsView();
        callLogsView.render();
    });

    var pusher_channel = 'boom',
        pusher = new Pusher(pusher_key);
      window.pusher_subscriber = pusher.subscribe(pusher_channel);

    pusher_subscriber.bind('update_balance', function(balance) {
      $('#credit-balance').text(balance.toString().replace(/0+$/g, '').substr(0,10)).fadeOut().fadeIn();
    });

    String.prototype.ucfirst = function() {
      return this.charAt(0).toUpperCase() + this.slice(1);
    };
 
    // Unlike the above, we don't call render on this view as it will handle
    // the render call internally after it loads data. Further more we load it
    // outside of an on-route function to have it loaded no matter which page is
    // loaded initially.
 
    Backbone.history.start();
  };
  return {
    initialize: initialize
  };
});
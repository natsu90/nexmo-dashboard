define(['jquery', 'underscore', 'backbone','collections/number/NumberCollection', 'models/number/NumberModel', 'text!templates/number/buyNumberTemplate.html'], function($, _, Backbone,NumberCollection, NumberModel, buyNumberTemplate) {
    var InboundView = Backbone.View.extend({
        el: $("#page"),
        searchNumber: function(e) {
          window.location.hash = '/buy/'+$(e.target).val();
        },
        buyNumber: function(e) {
          var $this = $(e.target), number = new NumberModel({
            number: $this.data('number'),
            country_code: $this.data('country-code'),
            type: $this.data('type'),
            features: $this.data('features')
          });
          $this.button('loading');

          number.save(null, {
            success: function() {
              window.location.hash = '/number/'+$this.data('number');
            },
            error: function() {
              $this.button('reset').fadeOut().fadeIn();
            }
          });
        },
        render: function(country) {
        	var that = this;
          if(typeof country == 'undefined' || country == null)
            country = 'MY';
        	this.collection = new NumberCollection();
        	this.collection.fetch({
                buy: country,
               success: function(collection, response) {
                   var template = _.template(buyNumberTemplate, {
                       numbers: collection.models
                   });
                   that.$el.html(template);

                  number_datatable = $('#number').dataTable({
                    "bPaginate": true,
                    "bLengthChange": false,
                    "bFilter": false,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                  });

                  $('#country').off('change').on('change', that.searchNumber).select2().select2('val', country);

                  $('.buy-number').off('click').on('click', that.buyNumber);

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/buy"]').addClass('active');
               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
          this.collection.live({pusherChannel: pusher_subscriber, eventType: "number"});
        },
    });
    return InboundView;
});
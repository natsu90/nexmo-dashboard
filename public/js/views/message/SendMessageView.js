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

          $btn = $('#send-btn').button('loading');
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
          outbound.save(null, {
            success: function() {
              window.location.hash = '/outbound';
            },
            error: function(e) {
              $btn.button('reset');
            }
          });
        }, 
        render: function(number) {
        	var that = this;
        	this.collection = new NumberCollection();
        	this.collection.fetch({
               success: function(collection, response) {
                   var template = _.template(sendMessageTemplate, {
                       numbers: collection.models
                   });

                   that.$el.html(template);
                   that.$('form').off('submit').on('submit', that.sendMessage);
                   if(number)
                    $('#to').val(number+';');

                  $('#from').select2({
                      allowClear:true,
                      formatNoMatches: function(term) {
                        /* customize the no matches output */
                        return "<a href='#' id='addNew' class='btn btn-default'>Add Sender ID</a>"
                      }
                    })
                    .parent().find('.select2-with-searchbox').on('click','#addNew',function(){
                      /* add the new term */
                      var newTerm = $(this).closest('.select2-with-searchbox').find('.select2-input').val(),
                          $from = $('#from');

                      $('<option value="'+newTerm+'">'+newTerm+'</option>').appendTo($from);
                      $from.select2('val', newTerm) // select the new term
                        .select2('close');   // close the dropdown
                    });

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/send"]').addClass('active');

               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
        }
    });
    return SendMessageView;
});
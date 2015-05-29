define([
  'jquery', 
  'underscore', 
  'backbone',
  'collections/number/NumberCollection',
  'text!templates/number/updateNumberTemplate.html',
  'models/number/NumberModel',
  ], function($, _, Backbone, NumberCollection, updateNumberTemplate, NumberModel) {
    var UpdateNumberView = Backbone.View.extend({

        el: $("#page"),
        updateNumber: function(e) {
          e.preventDefault();

          var $voice_callback_type = $('[name=voice_callback_type]'), $voice_callback_value = $('#voice_callback_value');

          var $btn = $('[type=submit]:focus').button('loading'),
              number = new NumberModel({
                id: $('#number').val()
              });

          if($btn.data('action') == 'delete') {

            number.destroy({
              beforeSend: function(xhr){
                xhr.setRequestHeader('Authorization', 'Bearer '+auth_token);
              },
              success: function () {
                window.location.hash = '/buy';
              },
              error: function() {
                $btn.button('reset').fadeOut().fadeIn();
                console.log('error');
              }
            });

          } else if($btn.data('action') == 'update') {

            if(typeof $voice_callback_type.filter('checked') == 'undefined' || $voice_callback_value.val().trim() == '')
              return;

            number.save({
              voice_callback_type: $voice_callback_type.filter(':checked').val(),
              voice_callback_value: $voice_callback_value.val(),
              own_callback_url: $('#own_callback_url').val()
            }, {
              beforeSend: function(xhr){
                xhr.setRequestHeader('Authorization', 'Bearer '+auth_token);
              },
              patch: true, 
              success: function () {
                $btn.button('reset');
              }, 
              error: function () {
                $btn.button('reset').fadeOut().fadeIn();
                console.log('error');
              }
            });
          }
        }, 
        render: function(number) {
        	var that = this;
        	this.collection = new NumberCollection();
        	this.collection.fetch({
                beforeSend: function(xhr){
                  xhr.setRequestHeader('Authorization', 'Bearer '+auth_token);
                },
               success: function(collection, response) {
                   var currentNumber = collection.filter(function(o) { 
                      return _.where(o, {number: parseInt(number)}).length > 0 || _.where(o, {number: number.toString()}).length > 0;
                    })[0],

                   template = _.template(updateNumberTemplate, {
                       number: currentNumber
                   });

                   that.$el.html(template);
                   that.$('form').off('submit').on('submit', that.updateNumber);

                   var voice_callback_type = currentNumber.get('voice_callback_type');
                   if(voice_callback_type != null && voice_callback_type != '')
                    $('[name=voice_callback_type][value='+voice_callback_type+']').click();

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/number/'+number+'"]').addClass('active');

               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
          this.collection.live({pusherChannel: pusher_subscriber, eventType: "number"});
        }
    });
    return UpdateNumberView;
});
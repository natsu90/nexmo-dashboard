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
          if(typeof $voice_callback_type.filter('checked') == 'undefined' || $voice_callback_value.val().trim() == '')
            return;

          var $btn = $('[type=submit]:focus').button('loading'),
              number = new NumberModel({
                id: $('#number').val()
              });

          if($btn.data('action') == 'delete') {

            console.log(number, 'deleting');

          } else if($btn.data('action') == 'update') {

            number.save({
              voice_callback_type: $voice_callback_type.filter(':checked').val(),
              voice_callback_value: $voice_callback_value.val()
            }, {patch: true});
          }
        }, 
        render: function(number) {
        	var that = this;
        	this.collection = new NumberCollection();
        	this.collection.fetch({
               success: function(collection, response) {
                   var currentNumber = collection.filter(function(o) { 
                      return _.where(o, {number: parseInt(number)}).length > 0 || _.where(o, {number: number.toString()}).length > 0;
                    })[0],

                   template = _.template(updateNumberTemplate, {
                       number: currentNumber
                   });

                   that.$el.html(template);
                   that.$('form').off('submit').on('submit', that.updateNumber);

                   $('[name=voice_callback_type][value='+currentNumber.get('voice_callback_type')+']').click();

                  $('.sidebar-menu li').removeClass('active').has('a[href="#/number/'+number+'"]').addClass('active');

               },
               error: function(collection, response) {
                   console.log("error");
               }
           });
        }
    });
    return UpdateNumberView;
});
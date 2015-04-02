define(['jquery', 'underscore', 'backbone', 'models/number/NumberModel', 'backbone_live'], function($, _, Backbone, NumberModel, backbone_live) {
    var NumberCollection = Backbone.LiveCollection.extend({
        model: NumberModel,
        url: "api/v1/number",
        parse: function(data) {
            return data.numbers;
        },
        add: function(data) {
            
            $numbers_menu = $('.sidebar-menu > li').has('a[href="#"]:contains("Numbers")');
            $numbers_notification = $numbers_menu.find('.notification');
            $numbers_menu.find('.treeview-menu').append('<li><a href="#/number/'+data.number+'"><i class="fa fa-'+ (data.type.indexOf('mobile') >= 0 ? 'mobile' : 'phone') +'"></i> '+data.number+'</a></li>')
                .find('a:contains('+data.number+')').addClass('active');
            $numbers_notification.text(parseInt($numbers_notification.text()) + 1).fadeOut().fadeIn();
        },
        update: function(data) {
            console.log('number updated');
        },
        remove: function(data) {

            $numbers_menu = $('.sidebar-menu > li').has('a[href="#"]:contains("Numbers")');
            $numbers_notification = $numbers_menu.find('.notification');
            $numbers_menu.find('.treeview-menu > li').has('a:contains('+data.number+')').remove();
            $numbers_notification.text(parseInt($numbers_notification.text()) - 1).fadeOut().fadeIn();
        },
        fetch: function(options) {
            if(typeof options.buy != 'undefined')
                options.url = this.url+'/search/'+options.buy;
            else if(typeof options.calls != 'undefined')
                options.url = this.url+'/calls';

            return Backbone.Collection.prototype.fetch.call(this, options);
        }
    });
    return NumberCollection;
});
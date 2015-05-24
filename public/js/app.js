define([
  'jquery',
  'underscore',
  'backbone',
  'router', // Request router.js
  'bootstrap',
  'pusher',
  'adminlte',
  'slimscroll',
  'text',
  'datatables',
  'datatables_bootstrap',
  'backbone_live',
  'pace',
  'select2'
], function($, _, Backbone, Router, Bootstrap){
  var initialize = function(){
    // Pass in our Router module and call it's initialize function
    Router.initialize();
  };
 
  return {
    initialize: initialize
  };
});
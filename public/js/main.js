require.config({
    paths: {
        jquery: 'libs/jquery/dist/jquery.min',
        underscore: 'libs/underscore/underscore',
        backbone: 'libs/backbone/backbone',
        templates: '../templates',
        bootstrap: 'libs/bootstrap/dist/js/bootstrap',
        adminlte: 'libs/AdminLTE',
        slimscroll: 'libs/slimScroll/jquery.slimscroll.min',
        text: 'libs/text/text',
        datatables: 'libs/datatables/media/js/jquery.dataTables.min',
        datatables_bootstrap: ['//cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap'],
        pusher: ['//js.pusher.com/2.2/pusher.min'],
        backbone_live: 'libs/backbone-live',
        pace: ['//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min'],
        select2: 'libs/select2/select2.min'
    },
    shim: {
        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        'jquery': {
            exports: '$'
        },
        'bootstrap': {
            deps: ['jquery'],
            exports: '$'
        },
        'underscore': {
            exports: '_'
        },
        'slimscroll': {
            deps: ['jquery']
        },
        'adminlte': {
            deps: ['jquery','bootstrap','slimscroll']
        },
        'text': {
            
        },
        'datatables': {
            deps: ['jquery']
        },
        'datatables_bootstrap': {
            deps: ['jquery','datatables']
        },
        'pusher': {

        },
        'backbone_live': {
            deps: ['backbone']
        },
        'pace': {
            
        },
        'select2': {
            deps: ['jquery']
        }
    },
    waitSeconds: 0
});
require(['app', ], function(App) {
    App.initialize();
});
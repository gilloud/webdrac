'use strict';

app.config(function ($routeProvider, $locationProvider) {
    $locationProvider.html5Mode(true);
    
    $routeProvider
    .when('/', {
        templateUrl: 'views/partials/main.html',
        controller: 'MainCtrl'
    }).
    when('/login', {
        templateUrl: 'views/partials/login.html',
        controller: LoginCtrl
    }).
    when('/logout', {
        templateUrl: 'views/partials/logout.html',
        controller: LogoutCtrl
    }).
    when('/:application', {
        templateUrl: 'views/partials/home_specific.html',
        controller: HomeSpecificCtrl
    }).
    when('/:application/list/:listId', {
        templateUrl: 'views/partials/list.html',
        controller: ListCtrl
    }).
    when('/:application/create/:wfId', {
        templateUrl: 'views/partials/create.html',
        controller: CreateCtrl
    }).
    when('/:application/move/:objectId', {
        templateUrl: 'views/partials/move.html',
        controller: MoveCtrl
    }).
    when('/:application/:object/:id', {
        templateUrl: 'views/partials/object.html',
        controller: ObjectCtrl
    })
    .when('/:application/:object/list/:action', {
        templateUrl: 'views/partials/list.html',
        controller: ListCtrl
    })
    .when('/:application/:object/transition/:action', {
        templateUrl: 'views/partials/action.html',
        controller: ActionCtrl
    })
    .when('/:application/error/:errorId', {
        templateUrl: 'views/partials/error.html',
        controller: ErrorCtrl
    })
    .otherwise({
        redirectTo: '/:application/error/404'
    });
});

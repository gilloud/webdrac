'use strict';

angular.module('webdrac')
    .controller('MainCtrl', function ($scope) {
        $scope.awesomeThings = [
            'HTML5 Boilerplate',
            'AngularJS',
            'Karma'
        ];
    }).controller('MenuCtrl', function ($scope) {
        $scope.ApplicationName = 'aaa';
    })function HomeCtrl($scope, $routeParams) {
    $scope.name = "HomeCtrl";
    $scope.params = $routeParams;
}
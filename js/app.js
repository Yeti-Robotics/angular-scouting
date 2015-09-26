/*global angular, $*/

var Scouter = {
    id: 0,
    name: ''
};
var app;
app = angular.module('app', ['ngRoute']);

app.controller('MainController', function ($scope, $http, $location) {
    'use strict';

    $http.get('json/data.json').then(function (response) {
        $scope.data = response.data;
    });

    $scope.change = function (item) {
        return item + '-changed';
    };

    $scope.$route = $http;

    $scope.role = "What is your role?";
    $scope.path = "/";

    $scope.changeRole = function (role) {
        $scope.role = role;
    };

    $scope.goToPath = function () {
        Scouter.id = $("#scouterid").val();
        $http.post('/php/checkUser.php', {
                id: Scouter.id,
                pswd: $("#password").val()
            })
            .then(function (response) {
                if (response) {
                    Scouter.name = response;
                    if ($scope.role === 'Scouter') {
                        $location.path("/form");
                    } else if ($scope.role === 'Wagerer') {
                        $location.path("/wager");
                    }
                } else {
                    //return an error or something
                }
            });
    }
});

app.controller('FormController', function ($scope) {
    $scope.scouterName = Scouter.name;

    $scope.stackRows = [];

    $scope.addStack = function () {
        $scope.stackRows.push({});
    };

    $scope.removeStack = function (stack) {
        var rowNum = $scope.stackRows.indexOf(stack);
        $scope.stackRows.splice(rowNum, 1);
    };

});

app.controller("ListController", function($scope, $http) {
    $http.get('php/list.php').then(function(response) {
       $scope.data = response.data;
    });
});

app.directive('stack', function () {
    return {
        templateUrl: 'html/stack.html',
        scope: {
            removeStack: '&'
        }
    }
});

app.config(['$routeProvider', function ($routeProvider, $locationProvider) {
    'use strict';

    $routeProvider.when('/', {
        templateUrl: 'html/home.html',
        controller: 'MainController'
    }).when('/form', {
        templateUrl: 'html/form.html',
        controller: 'FormController'
    }).when('/wager', {
        templateUrl: 'html/TheCasino.html'
    }).when("/list", {
        templateUrl: 'html/list.html',
        controller: 'ListController'
    }).otherwise({
        redirectTo: '/'
    });
}]);
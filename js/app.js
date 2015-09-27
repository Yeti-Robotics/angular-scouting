/*global angular, $*/

var Scouter = {
    id: 0,
    name: '',
    pswd: '',
    byteCoins: 0
};

var app;
app = angular.module('app', ['ngRoute']);

app.controller('MainController', function ($scope, $http, $location) {
    'use strict';

    $scope.role = "What is your role?";
    $scope.path = "/";

    $scope.changeRole = function (role) {
        $scope.role = role;
    };

    $scope.goToPath = function () {
        Scouter.id = $("#scouterid").val();
        Scouter.pswd = $("#password").val();
        $http.post('/php/checkUser.php', {
                id: Scouter.id,
                pswd: Scouter.pswd
            })
            .then(function (response) {
                if (!response.error) {
                    Scouter.name = response.name;
                    if ($scope.role === 'Scouter') {
                        $location.path("/form");
                    } else if ($scope.role === 'Wagerer') {
                        $location.path("/wager");
                    }
                } else {
                    //return an error or something
                }
            });
    };
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

app.controller("ListController", function ($scope, $http) {
    $http.get('php/list.php').then(function (response) {
        $scope.data = response.data;
    });
});

app.controller("JoeBannanas", function ($scope, $http) {
    $scope.id = Scouter.id;

    $scope.byteCoins = Scouter.byteCoins;

    $scope.refreshByteCoins = function () {
        $http.post("php/getByteCoins.php", {
            id: Scouter.id,
            pswd: Scouter.pswd
        }).then(function (response) {
            Scouter.byteCoins = response.byteCoins;
        }, function (response) {
            $scope.reportError("could not properly get your number of Byte Coins");
        });
    };
    $scope.refreshByteCoins();

    $http.get("json/NCRE.json").then(function (response) {
        $scope.NCRE = response.data;
    });

    $scope.toOptionLabel = function (teams) {
        return teams[0].teamNumber + "-" + teams[1].teamNumber + "-" +
            teams[2].teamNumber + " vs " + teams[3].teamNumber + "-" +
            teams[4].teamNumber + "-" + teams[5].teamNumber;
    };

    $scope.reportSuccess = function (wager) {
        $scope.refreshByteCoins();
    };

    $scope.reportError = function (error) {
        //something like, "Sorry, we " + error + ", maybe try again?"
    };

    $scope.wageredByteCoins = 0;

    $scope.currentWager = {
        wagerType: "",
        alliancePredicted: false,
        matchPredicted: false,
        withenPoints: false,
        pointsPredicted: false
    };


    //Templates
    $scope.allianceWager = {
        alliancePredicted: '',
        matchPredicted: 0
    };
    $scope.closeMatchWager = {
        withenPoints: 0, //can be set to >300 for any. people will get points if the scored points are less then the number set here (for predicting close games)
        matchPredicted: 0
    };
    $scope.pointsWager = {
        alliancePredicted: '',
        minPointsPredicted: 0, //only applies to allinaces, negative if less than
        matchPredicted: 0
    };


    $scope.sendAllianceWager = function () {
        if ($scope.currentWager.wagerType === "alliance" && $scope.currentWager.alliancePredicted && $scope.currentWager.matchPredicted) {
            $http.post("php/wager.php", {
                wagerType: "alliance",
                points: $scope.pointsWagered,
                alliance: $scope.current.alliancePredicted,
                match: $scope.currentWager.matchPredicted
            }).then(function (response) {
                $scope.reportSuccess();
            }, function (response) {

            });
        }
    };

    $scope.sendCloseMatchWager = function () {
        if ($scope.currentWager.wagerType === "alliance" && $scope.currentWager.withenPoints && $scope.currentWager.matchPredicted) {
            $http.post("php/wager.php", {
                wagerType: "closeMatch",
                points: $scope.pointsWagered,
                match: $scope.closeMatchWager.matchPredicted,
                withenPoints: $scope.closeMatchWager.withenPoints
            }).then(function (response) {
                $scope.reportSuccess();
            }, function (response) {

            });
        }
    };

    $scope.sendPointsWager = function () {
        if ($scope.currentWager.wagerType === "alliance" && $scope.currentWager.pointsPredicted && $scope.currentWager.matchPredicted) {
            $http.post("php/wager.php", {
                wagerType: "points",
                points: $scope.pointsWagered,
                pointsInGame: $scope.allianceWager.pointsPredicted,
                match: $scope.currentWager.matchPredicted
            }).then(function (response) {
                $scope.reportSuccess();
            }, function (response) {

            });
        }
    };
});

app.directive('stack', function () {
    return {
        templateUrl: 'html/stack.html',
        scope: {
            removeStack: '&'
        }
    };
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
        templateUrl: 'html/TheCasino.html',
        controller: 'JoeBannanas'
    }).when("/list", {
        templateUrl: 'html/list.html',
        controller: 'ListController'
    }).otherwise({
        redirectTo: '/'
    });
}]);

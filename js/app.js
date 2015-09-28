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
        Scouter.id = $scope.scouterId;
        Scouter.pswd = $scope.scouterPswd;
        $http.post('/php/checkUser.php', {
                id: Scouter.id,
                pswd: Scouter.pswd
            })
            .then(function (response) {
                var result = response.data;
                if (result) {
                    Scouter.name = result;
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

app.controller('FormController', function ($scope, $http) {
    'use strict';

    $scope.stackRows = {
        rows: []
    };

    $scope.formData = {
        name: Scouter.name
    };

    //    $scope.stacks_totes = '0';
    //    $scope.capped_stack = '0';
    //    $scope.cap_height = '0';

    $scope.addStack = function () {
        $scope.stackRows.rows.push({
            stacks_totes: '0',
            capped_stack: '0',
            cap_height: '0'
        });
    };

    $scope.removeStack = function (stack) {
        var rowNum = $scope.stackRows.rows.indexOf(stack);
        $scope.stackRows.rows.splice(rowNum, 1);
    };

    $scope.submit = function () {
        //        $scope.formData.stackRows = $scope.stackRows.rows;
        $http.post('php/submit.php', $scope.formData).then(function (response) {}, function (response) {
            console.log("data: " + response.data + "\n error code: " + response.status + "\n error text: " + response.statusText);
        });
        $('input, select, textarea').removeClass('ng-dirty ng-touched ng-valid-parse');
        $('input, select, textarea').addClass('ng-pristine ng-untouched ng-valid');
        $('input, select, textarea').val('');
        $scope.formData = {};
    }

});

app.controller("ListController", function ($scope, $http) {
    'use strict';

    $http.get('php/list.php').then(function (response) {
        $scope.data = response.data;
    });
});

app.controller("JoeBannanas", function ($scope, $http) {
    'use strict';

    $scope.id = Scouter.id;
    $scope.byteCoins = Scouter.byteCoins;

    $scope.refreshByteCoins = function () {
        $http.post("php/getByteCoins.php", {
            id: Scouter.id,
            pswd: Scouter.pswd
        }).then(function (response) {
            Scouter.byteCoins = response.data;
            $scope.byteCoins = response.data;
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

    $scope.currentWager = {
        wagerType: '',
        wageredByteCoins: 0,
        alliancePredicted: '',
        matchPredicted: 0,
        withenPoints: 0,
        minPointsPredicted: 0,
        getValue: function () {

            if (this.wagerType === "alliance") {

                return this.wageredByteCoins * 2;
            } else if (this.wagerType === "closeMatch") {
                return this.wageredByteCoins / this.withenPoints;
            } else if (this.wagerType === "points") {
                if (this.pointsPredicted > 110) {
                    return (this.wageredByteCoins * Math.log(this.minPointsPredicted) / 2); //Actually VERY NICE scale, thanks math ;)
                }
            }
            return 0;
        }
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
                points: $scope.currentWager.wageredByteCoins,
                alliance: $scope.currentWager.alliancePredicted,
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
                points: $scope.currentWager.wageredByteCoins,
                match: $scope.currentWager.matchPredicted,
                withenPoints: $scope.currentWager.withenPoints
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
                points: $scope.currentWager.wageredByteCoins,
                pointsInGame: $scope.currentWager.minPointsPredicted,
                match: $scope.currentWager.matchPredicted
            }).then(function (response) {
                $scope.reportSuccess();
            }, function (response) {

            });
        }
    };
});

app.directive('stack', function () {
    'use strict';
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

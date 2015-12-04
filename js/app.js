/*global angular, $*/

var app;
app = angular.module('app', ['ngRoute']);

app.run(function ($rootScope, $location, $http, $window) {
    'use strict';
    
    $rootScope.user = {
        username: '',
        name: '',
        byteCoins: 0,
        logOut: function () {
            $window.sessionStorage.removeItem('token');
            this.user.name = '';
            $rootScope.loggedIn = false;
        }
    };
    
    $rootScope.loggedIn = !($window.sessionStorage.token == null);
    
    $rootScope.log = function () {
        if ($rootScope.loggedIn) {
            $window.sessionStorage.removeItem('token');
        }
        $rootScope.loggedIn = !$rootScope.loggedIn;
    };
    
    $rootScope.validateLogin = function () {
        $http.get('php/validateSession.php', {
            params: {
                token: $window.sessionStorage["token"]
            }
        }).then(function (response) {
            if (response.data == 'false') {
                $rootScope.user.logOut();
                $location.path('/login');
            }
        }, function (response) {
            $rootScope.user.logOut();
            $location.path('/login');
        });
    };
    
    $rootScope.$watch(function () {
        return $location.path();
    }, function () {
        if ($location.path() == '/wager') {
			$rootScope.validateLogin();
        }
    });
});

app.controller('LoginController', function ($rootScope, $scope, $http, $location, $window) {
    'use strict';
    
    $(document).ready(function () {
        $('#loginForm').validate();
        $('#username').rules("add", {
            messages: {
                required: "Username cannot be empty"
            }
        });
        $('#password').rules("add", {
            messages: {
                required: "Password cannot be empty"
            }
        });
    });

    $scope.login = function () {
        $http.post('php/checkUser.php', {
            username: $scope.scouterUsername,
            pswd: $scope.scouterPswd
        }).then(function (response) {
            var result = response.data;
            console.log(result);
            $window.sessionStorage["token"] = result.token;
            $rootScope.user.name = result.name;
            $rootScope.user.username = $scope.scouterId;
            $rootScope.loggedIn = true;
            $location.path('/wager');
        }, function (response) {
            $("#loginForm").validate().showErrors({
                "loginFields": "Invalid username/password"
            });
        });
    };
});

app.controller('RegisterController', function ($scope, $http, $location) {
    'use strict';

    $scope.password = '';
    $scope.confirmPassword = '';
    $scope.username = '';
    $scope.firstName = '';
    $scope.lastName = '';
    $scope.name = '';

    $scope.validate = function () {
        return $scope.username.length > 0 && $scope.password.length > 0 && $scope.password === $scope.confirmPassword;
    };
    
    $(document).ready(function () {
        $("#registerForm").validate();
        $("#username").rules("add", {
            messages: {
                required: "You must have a username!"
            }
        });
        $("#password").rules("add", {
            messages: {
                required: "You must have a password!"
            }
        });
    });

    $scope.register = function () {
        if ($scope.validate()) {
            console.log('registered');
            $scope.firstName = $scope.firstName[0].toUpperCase() + $scope.firstName.slice(1);
            $scope.lastName = $scope.lastName[0].toUpperCase() + $scope.lastName.slice(1);
            $scope.name = $scope.firstName + ' ' + $scope.lastName;
            $http.post('php/register.php', {
                name: $scope.name,
                username: $scope.username,
                password: $scope.password
            }).then(function (response) {
                $location.path("/login");
                console.log(response.data);
            }, function (response) {
                console.log(response.data);
                $("#registerForm").validate().showErrors({
                    "username": "Username already taken"
                });
            });
        } else {
            $("#registerForm").validate().showErrors({
                "password": "Passwords do not match",
                "confirmPassword": "Passwords do not match"
            });
        }
    };
});

app.controller('FormController', function ($rootScope, $scope, $http, $window) {
    'use strict';
    
    $scope.formData = {
        stackRows: {
            rows: []
        },
        name: $rootScope.user.name
    };

    $(document).ready(function () {
        $('#scouting_form').validate();
        console.log('Inititalize validation');
    });

    $scope.addStack = function () {
        $scope.formData.stackRows.rows.push({
            stacks_totes: '0',
            capped_stack: '0',
            cap_height: '0'
        });
    };

    $scope.removeStack = function (stack) {
        var rowNum = $scope.formData.stackRows.rows.indexOf(stack);
        $scope.formData.stackRows.rows.splice(rowNum, 1);
    };

    $scope.submit = function () {
        if ($('#scouting_form').valid()) {
            console.log("valid");
            $http.post('php/formSubmit.php', $scope.formData).then(function (response) {
                console.log("submitted");
                console.log(response);
                $('#scouting_form').trigger('reset');
                $('body').scrollTop(0);
                if ($('#scouting_form').prev().attr('id') != "success_message") {
                    $("#scouting_form").before('<div id="success_message" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"<span aria-hidden="true">&times;</span></button><strong>Success!</strong> Now do it again.</div>');
                }
                $scope.formData.stackRows.rows = [];
            }, function (response) {
                console.log("Error during submission");
                console.log(response);
            });
        } else {
            console.log("Not valid");
        }
    };
});

app.controller('PitFormController', function ($rootScope, $scope, $http, $window) {
    'use strict';

    $(document).ready(function () {
        $('#pitForm').validate();
        console.log('Inititalize validation');
        if ($(".robotimage").length == 0 && $("#comments").val() == '') {
            $("#comments").each(function () {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "You must submit a comment and/or at least one picture."
                    }
                });
            });
        }
    });

    $scope.unrequireComments = function () {
        $("#comments").rules("remove", "required");
    };

    $scope.pitFormData = {
        name: $rootScope.user.name
    };

    $scope.pictures = [];

    $scope.picNum = [];

    $scope.updateDisplay = function (picture, rowNum) {
        var reader = new FileReader();
        var file = picture.files[0];
        if ($scope.pictures[rowNum] == null) {
            $scope.pictures.push(file);
        } else {
            $scope.pictures[rowNum] = file;
        }
        reader.readAsDataURL(file);
        reader.onload = function () {
            $(picture).parent().prev().children().attr("src", reader.result);
        };
    };

    var num = 0;

    $scope.addPicture = function () {
        $scope.picNum.push(num);
        num++;
    };

    $scope.removePicture = function (picture) {
        var rowNum = $scope.picNum.indexOf(picture);
        $scope.picNum.splice(rowNum, 1);
        $scope.pictures.splice(rowNum, 1);
    };

    $scope.submit = function () {
        if ($('#pitForm').valid()) {
            console.log("valid");
            var formData = new FormData();
            for (var i = 0; i < $scope.pictures.length; i++) {
                formData.append('files[]', $scope.pictures[i]);
            }
            for (var key in $scope.pitFormData) {
                if ($scope.pitFormData.hasOwnProperty(key)) {
                    formData.append(key, $scope.pitFormData[key]);
                }
            }
            $http.post("php/pitFormSubmit.php", formData, {
                transformRequest: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            }).then(function (response) {
                console.log("submitted");
                console.log(response.data);
                $('body').scrollTop(0);
                $scope.pitFormData = {
                    name: $rootScope.user.name
                };
                $scope.pictures = [];
                $scope.picNum = [];
                if ($('#pitForm').prev().attr('id') != "success_message") {
                    $("#pitForm").before('<div id="success_message" class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"<span aria-hidden="true">&times;</span></button><strong>Success!</strong> Now do it again.</div>');
                }
            }, function (response) {
                console.log("Error during submission");
                console.log(response);
            });
        } else {
            console.log("Not valid");
        }
    }

});

app.controller('PitController', function ($scope, $http, $routeParams, $location) {
    'use strict';

    $scope.teamNumber = $routeParams.teamNumber;

    $scope.teamLink = function () {
        $('#errorModal').on('hidden.bs.modal', function () {
            $location.path("/team/" + $scope.teamNumber);
            $scope.$apply();
        });
    }

    $scope.pitData = {
        pictures: [],
        comments: [],
        token: $window.sessionStorage["token"]
    }

    $scope.picIndex;

    $scope.noComments = false;

    $scope.noPictures = false;

    $scope.nextPicture = function () {
        if ($scope.picIndex < ($scope.pitData.pictures.length - 1)) {
            $scope.picIndex++;
        } else {
            $scope.picIndex = 0;
        }
    }

    $scope.previousPicture = function () {
        if ($scope.picIndex > 0) {
            $scope.picIndex--;
        } else {
            $scope.picIndex = $scope.pitData.pictures.length - 1;
        }
    }
    
    $http.get("php/getTeamInfo.php", {
        params: {
            teamNumber: $routeParams.teamNumber
        }
    }).then(function(response) {
        console.log(response.data);
        $scope.teamInfo = response.data;
    }, function(response) {
        $scope.teamInfo = {
            name: "Error getting name",
            robotName: "Error getting robot name"
        };
    });

    $http.get('php/getPitData.php', {
        params: {
            teamNumber: $routeParams.teamNumber
        }
    }).then(function (response) {
        $scope.data = response.data;
        if (response.data.commentSection != null) {
            for (var i = 0; i < response.data.commentSection.length; i++) {
                $scope.pitData.comments.push({
                    comment: response.data.commentSection[i]['Pit Scouters Comments'],
                    commenter: response.data.commentSection[i]['Pit Scouter'],
                    timeStamp: response.data.commentSection[i]['timestamp']
                });
            }
        } else {
            $scope.noComments = true;
        }
        if (response.data.pics != null) {
            for (var i = 0; i < response.data.pics.length; i++) {
                $scope.pitData.pictures.push({
                    pictureNumber: response.data.pics[i]['Picture Number'],
                    photographer: response.data.pics[i]['Pit Scouter'],
                    timeStamp: response.data.pics[i]['timestamp']
                });
            }
            $scope.picIndex = 0;
        } else {
            $scope.noPictures = true;
        }

        $(document).ready(function () {
            if ($scope.noComments && $scope.noPictures) {
                $("#errorModal").modal("show");
            }
        });
    });
});

app.controller("ListController", function ($rootScope, $scope, $http) {
    'use strict';
    $scope.sortType = 'rating';
    $scope.sortReverse = false;

    $http.get('php/list.php').then(function (response) {
        $scope.data = response.data;
    });
});

app.controller("JoeBannanas", function ($rootScope, $scope, $http, $window) {
    'use strict';

    $scope.refreshByteCoins = function () {
        $http.post("php/getByteCoins.php", {
            token: $window.sessionStorage["token"]
        }).then(function (response) {
            $rootScope.user.byteCoins = $scope.byteCoins = response.data;
        }, function (response) {
            $scope.reportError("Could not properly get your number of Byte Coins. Are you logged in?");
        });
    };
    $scope.refreshByteCoins();

    $scope.reportSuccess = function (wager) {
        $scope.refreshByteCoins();
    };

    $scope.reportError = function (error) {
        //something like, "Sorry, we " + error + ", maybe try again?"
        $scope.lastError = error;
        $("#errorModal").modal('show');
    };

    $http.get("json/NCRE.json").then(function (response) {
        $scope.NCRE = response.data;
    }, function (response) {
        $scope.reportError("Failed to get match data");
    });


    $scope.toOptionLabel = function (teams) {
        return teams[0].teamNumber + "-" + teams[1].teamNumber + "-" +
            teams[2].teamNumber + " vs " + teams[3].teamNumber + "-" +
            teams[4].teamNumber + "-" + teams[5].teamNumber;
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
                return ((parseInt(this.wageredByteCoins, 10) / parseInt(this.withenPoints, 10)) * 3) + parseInt(this.wageredByteCoins, 10); //Terrible scale, need to fix
            } else if (this.wagerType === "points") {
                if (this.minPointsPredicted > 110) {
                    return (parseInt(this.wageredByteCoins, 10) * Math.log(parseInt(this.minPointsPredicted, 10)) / 2); //Actually VERY NICE scale, thanks math ;)
                }
            }
            return 0;
        }
    };

    $scope.changeWager = function (wagerType) {
        $scope.currentWager.wagerType = wagerType;
    };
    //Templates
    $scope.allianceWager = {
        alliancePredicted: '',
        matchPredicted: 0
    };
    $scope.closeMatchWager = {
        withenPoints: 0, //People will get points if the scored points are less then the number set here (for predicting close games)
        matchPredicted: 0
    };
    $scope.pointsWager = {
        alliancePredicted: '',
        minPointsPredicted: 0, //only applies to allinaces, negative if less than
        matchPredicted: 0
    };

    $scope.sendWager = function () {
        $rootScope.validateLogin();
        var postObject = {};
        if ($scope.currentWager.wagerType === "alliance" && $scope.currentWager.alliancePredicted && $scope.currentWager.matchPredicted) {
            postObject = {
                token: $window.sessionStorage["token"],
                wagerType: "alliance",
                wageredByteCoins: $scope.currentWager.wageredByteCoins,
                matchPredicted: $scope.currentWager.matchPredicted,
                alliancePredicted: $scope.currentWager.alliancePredicted
            };
        } else if ($scope.currentWager.wagerType === "closeMatch" && $scope.currentWager.withenPoints && $scope.currentWager.matchPredicted) {
            postObject = {
                token: $window.sessionStorage["token"],
                wagerType: "closeMatch",
                wageredByteCoins: $scope.currentWager.wageredByteCoins,
                matchPredicted: $scope.currentWager.matchPredicted,
                withenPoints: $scope.currentWager.withenPoints
            };
        } else if ($scope.currentWager.wagerType === "points" && $scope.currentWager.pointsPredicted && $scope.currentWager.matchPredicted) {
            postObject = {
                token: $window.sessionStorage["token"],
                wagerType: "points",
                wageredByteCoins: $scope.currentWager.wageredByteCoins,
                matchPredicted: $scope.currentWager.matchPredicted,
                alliancePredicted: $scope.currentWager.alliancePredicted,
                withenPoints: $scope.currentWager.withenPoints
            };
        } else {
            $scope.reportError("Incorrect wager format. Did you fill all of the fields?");
        }
        $http.post("php/wager.php", postObject).then(function (response) {
            $scope.reportSuccess(postObject);
        }, function (response) {
            $scope.reportError("Failed to send Wager.");
        });
    };
});

app.controller("LeaderboardsController", function ($scope, $http) {
    'use strict';
    $scope.sortType = 'byteCoins';
    $scope.sortReverse = true;
    $http.get('php/leaderboards.php').then(function (response) {
        $scope.data = response.data;
    });
});

app.controller("TeamController", function ($scope, $http, $routeParams) {
    'use strict';

    console.log(' team number ' + $routeParams.teamNumber);

    $scope.teamNumber = $routeParams.teamNumber;

    $scope.team = {
        number: 0,
        avgStackHeight: 0,
        avgStacksPerMatch: 0,
        heighestStackMade: 0,
        rating: 0
    }

    $scope.stacks = [];

    $scope.commentSection = {
        comments: []
    }
    
    $http.get("php/getTeamInfo.php", {
        params: {
            teamNumber: $routeParams.teamNumber
        }
    }).then(function(response) {
        console.log(response.data);
        $scope.teamInfo = response.data;
    }, function(response) {
        $scope.teamInfo = {
            name: "Error getting name",
            robotName: "Error getting robot name"
        };
    });
    
    $http.get("php/team.php", {
        params: {
            teamNumber: $routeParams.teamNumber
        }

    }).then(function (response) {
        $scope.data = response.data;
        console.log($scope.data);
        //        $scope.stacks = response.stacks;
        //        for (var i = 0; i < response.data.commentSection['comments'].length; i++ ) {
        //            $scope.commentSection.comments.push({
        //                commentText: response.data.commentSection['comments'][i],
        //                timeStamp: response.data.commentSection['timestamps'][i],
        //                name: response.data.commentSection['names'][i],
        //                matchNumber: response.data.commentSection['matchNumbers'][i]
        //            });
        //        }
        //        console.log(response.data);
        //        $scope.team = response.data.teamSection;
        //        $scope.stacks = response.data.stacksSection;
        //        $scope.toteSupplys = response.data.toteSupplySection;
        //        $scope.coopTotes = response.data.coopSection;
        //        $scope.autoSection = response.data.autoSection;
        //        

    }, function (response) {
        $scope.team = {},
            $scope.stacks = []
    });
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

app.directive('picture', function () {
    'use strict';
    return {
        templateUrl: 'html/picture.html',
        scope: {
            removePicture: '&'
        }
    }
});

app.config(['$routeProvider', function ($routeProvider, $locationProvider) {
    'use strict';

    $routeProvider.when('/', {
        templateUrl: 'html/form.html',
        controller: 'FormController'
    }).when('/wager', {
        templateUrl: 'html/TheCasino.html',
        controller: 'JoeBannanas'
    }).when("/list", {
        templateUrl: 'html/list.html',
        controller: 'ListController'
    }).when("/leaderboards", {
        templateUrl: 'html/leaderboards.html',
        controller: 'LeaderboardsController'
    }).when("/team/:teamNumber", {
        templateUrl: 'html/team.html',
        controller: 'TeamController'
    }).when("/pitForm", {
        templateUrl: 'html/pitForm.html',
        controller: 'PitFormController'
    }).when("/pit/:teamNumber", {
        templateUrl: 'html/pit.html',
        controller: 'PitController'
    }).when("/login", {
        templateUrl: 'html/login.html',
        controller: 'LoginController'
    }).when("/register", {
        templateUrl: 'html/register.html',
        controller: 'RegisterController'
    }).otherwise({
        redirectTo: '/'
    });
}]);
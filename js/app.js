/*global angular, $*/

var app;
app = angular.module('app', ['ngRoute']);

function displayMessage(message, alertType, timeVisible) {
    var timeVisible = timeVisible == undefined ? 3000 : timeVisible;
    $('.message-container').html(message).removeClass('alert-success alert-info alert-warning alert-danger').addClass('alert-' + alertType).stop(true).slideDown(500).delay(timeVisible).slideUp(500);
}

app.run(function ($rootScope, $location, $http, $window, AccountService) {
    'use strict';

    $rootScope.loggedIn = $window.localStorage.token != null;

    $rootScope.user = {
        username: '',
        name: '',
        byteCoins: 0
    };

    $rootScope.getCurrentSettings = function (onSettingsUpdate) {
        $http.get("php/getSettings.php").then(function (response) {
            $rootScope.settings = response.data;
            if (onSettingsUpdate != undefined) {
                onSettingsUpdate();
            }
        }, function (response) {
            displayMessage("Failed getting current settings.", "danger");
            $rootScope.settings = null;
            if (onSettingsUpdate != undefined) {
                onSettingsUpdate();
            }
        });
    }

    $rootScope.getCurrentSettings();

});

app.service("AccountService", function ($http, $q, $window, $rootScope, $location) {
    'use strict';

    this.login = function (username, password) {
        var deferred = $q.defer();

        $http.post("php/login.php", {
            username: username,
            pswd: password
        }).then(function (response) {
            deferred.resolve(response);
        }, function (response) {
            deferred.reject(response);
        });

        return deferred.promise;
    };

    this.logout = function () {
        $http.post("php/logout.php", {
            token: $window.localStorage["token"]
        }).finally(function (response) {
            $window.localStorage.removeItem("token");
            $rootScope.loggedIn = false;
            $rootScope.user = {
                username: '',
                name: '',
                id: '',
                byteCoins: 0
            };
            $location.path("/login");
        });
    }

    this.validateSession = function () {
        var deferred = $q.defer();

        $http.post("php/validateSession.php", {
            token: $window.localStorage["token"]
        }).then(function (response) {
            if (response.data == "false") {
                deferred.reject(response);
            } else {
                deferred.resolve(response);
                $rootScope.user = response.data;
            }
        }, function (response) {
            deferred.reject(response);
        });

        return deferred.promise;
    };
});

app.controller('LoginController', function (AccountService, $rootScope, $scope, $http, $location, $window) {
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

        var code = "38384040373937396665";
        var input = "";
        var timer;
        $(document).keyup(function (e) {
            input += e.which;

            clearTimeout(timer);
            timer = setTimeout(function () {
                input = "";
            }, 500);

            if (input == code) {
                $scope.scouterUsername = "admin";
                $scope.scouterPswd = prompt("If you really are who you claim to be, then what's the password?");
                $scope.login();
            }
        });
    });

    $scope.login = function () {
        if (!$rootScope.loggedIn) {
            AccountService.login($scope.scouterUsername, $scope.scouterPswd).then(function (response) {
                var result = response.data;
                console.log(result);
                $window.localStorage["token"] = result.token;
                $rootScope.loggedIn = true;
                if ($scope.scouterUsername == "admin") {
                    $location.path('/admin');
                } else {
                    $location.path('/scouting');
                }
            }, function (response) {
                $("#loginForm").validate().showErrors({
                    "username": "Invalid username/password",
                    "password": "Invalid username/password"
                });
            });
        }
    };

    if ($rootScope.loggedIn) {
        $location.path("/wager");
    }
});

app.controller("LogoutController", function (AccountService, $rootScope, $http, $location, $window) {
    'use strict';

    if ($rootScope.loggedIn) {
        AccountService.logout();
    } else {
        $location.path("/login");
    }
});

app.controller('RegisterController', function ($scope, $http, $location) {
    'use strict';

    $scope.teams = [
        {
            teamNumber: 3506,
            name: 'Yeti Robotics'
        },
        {
            teamNumber: 4290,
            name: 'Bots of War'
        },
        {
            teamNumber: 6894,
            name: 'Iced Java'
        }
    ];

    $(document).ready(function () {
        $("#registerForm").validate({
            rules: {
                password: {
                    minlength: 3
                },
                confirmPassword: {
                    minlength: 3,
                    equalTo: "#password"
                }
            }
        });
    });

    $scope.register = function () {
        if ($('#registerForm').valid()) {
            $http.post('php/register.php', {
                name: $scope.name,
                username: $scope.username,
                password: $scope.password,
                teamNumber: $scope.teamNumber
            }).then(function (response) {
                $location.path("/login");
                console.log(response.data);
            }, function (response) {
                console.log(response.data);
                $("#registerForm").validate().showErrors({
                    "username": "Username already taken"
                });
            });
        }
    };
});

app.controller('FormController', function ($rootScope, $scope, $http, $window, AccountService) {
    'use strict';

    $scope.fullMatches = [];
    $scope.matchesReceived = false;
    $scope.selectedTeam = false;
    $scope.robotPos = [
        "Red 1",
        "Red 2",
        "Red 3",
        "Blue 1",
        "Blue 2",
        "Blue 3"
    ];
    $scope.selectedRobotPos = "";

    $rootScope.getCurrentSettings(function () {
        if ($rootScope.settings.validateTeams) {
            $http.get("php/getFutureMatches.php").then(function (response) {
                for (var i = 0; i < response.data.length; i++) {
                    $scope.fullMatches.push({
                        teams: {
                            red: [
                                $scope.parseTeamString(response.data[i].alliances.red.team_keys[0]),
                                $scope.parseTeamString(response.data[i].alliances.red.team_keys[1]),
                                $scope.parseTeamString(response.data[i].alliances.red.team_keys[2])
                            ],
                            blue: [
                                $scope.parseTeamString(response.data[i].alliances.blue.team_keys[0]),
                                $scope.parseTeamString(response.data[i].alliances.blue.team_keys[1]),
                                $scope.parseTeamString(response.data[i].alliances.blue.team_keys[2])
                            ]
                        },
                        number: parseInt(response.data[i].match_number)
                    });
                }
                $scope.fullMatches.sort(function (a, b) {
                    a = a.number;
                    b = b.number;

                    if (a < b) {
                        return -1;
                    } else if (a > b) {
                        return 1;
                    }

                    return 0;
                });
                console.log($scope.fullMatches);
                $scope.matchesReceived = true;
            }, function (response) {
                displayMessage("Uh oh! Something went wrong with getting the future matches, looks like you'll have to enter the info manually. Try again later.", "danger");
                $scope.matchesReceived = false;
            });
        }
    });

    $scope.parseTeamString = function (teamString) {
        return parseInt(teamString.slice(3));
    }

    $scope.selectTeam = function (matchNumber, teamNumber) {
        $scope.formData.matchNumber = matchNumber;
        $scope.selectedTeam = true;
        $scope.formData.teamNumber = parseInt(teamNumber);
        $("#match-modal").modal('hide');
        $scope.resetMatchChooser();
    };

    $scope.loadTeams = function (selectedRobotPos) {
        $scope.matches = [];
        switch (selectedRobotPos) {
            case "Red 1":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.red[0],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
            case "Red 2":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.red[1],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
            case "Red 3":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.red[2],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
            case "Blue 1":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.blue[0],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
            case "Blue 2":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.blue[1],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
            case "Blue 3":
                for (var i = 0; i < $scope.fullMatches.length; i++) {
                    $scope.matches.push({
                        team: $scope.fullMatches[i].teams.blue[2],
                        number: $scope.fullMatches[i].number
                    });
                }
                break;
        }
    }

    AccountService.validateSession().then(function (response) {
        $scope.resetForm();
    }, function (error) {
        AccountService.logout();
        displayMessage('You are logged out', 'warning');
    });

    $scope.resetForm = function () {
        $scope.formData = {
            autoCheck: false,
            autoScale: 0,
            autoSwitch: 0,
            teleCheck: false,
            scaleCubes: 0,
            switchCubes: 0,
            enemySwitchCubes: 0,
            vaultCubes: 0,
            scouterId: $rootScope.user.id
        };
        $scope.selectedTeam = false;
        $("#submitButton").removeAttr("disabled");
        $("#scouting_form").trigger("reset");
        $scope.resetMatchChooser();
    };

    $scope.resetMatchChooser = function () {
        $("#matchChooser").removeClass("btn-danger").addClass("btn-primary");
        $("#matchChooser-error").remove();
    };

    $(document).ready(function () {
        $scope.validator = $('#scouting_form').validate();

        $("[required]").each(function (i) {
            $(this).siblings("label").addClass("required");
        });

        $("#selectedRobotPos").rules("add", {
            required: true,
            messages: {
                required: "You must select a position to scout."
            }
        });

        $("#score").rules("add", {
            min: 0,
            messages: {
                min: "You can't have a negative score!"
            }
        })
    });

    $scope.submit = function () {
        if ($('#scouting_form').valid() && $scope.formData.matchNumber != undefined) {
            $(window).scrollTop(0);
            displayMessage("<strong>Hold up...</strong> Your data is being uploaded now...", "info");
            $("#submitButton").attr("disabled", "disabled");
            $http.post('php/formSubmit.php', $scope.formData)
                .then(function (data) {
                    displayMessage('Form submitted successfully', 'success');
                    console.log($scope.formData);
                    $scope.matches.shift();
                    $rootScope.getCurrentSettings();
                    $scope.resetForm();
                }, function (error) {
                    displayMessage('Failed to submit form', 'danger');
                    console.error(error);
                });
        } else {
            if ($scope.formData.matchNumber == undefined && $("#matchChooser").siblings("label").length < 1) {
                $("#matchChooser").removeClass("btn-primary").addClass("btn-danger").after("<label id=\"matchChooser-error\" style=\"color: red\">You must choose a match.</label>");
            }
        }
    };

    $scope.incrementAS = function () {
        $scope.formData.autoScale++;
    };
    $scope.decrementAS = function () {
        if ($scope.formData.autoScale > 0) {
            $scope.formData.autoScale--;
        }
    };
    $scope.incrementAW = function () {
        $scope.formData.autoSwitch++;
    };
    $scope.decrementAW = function () {
        if ($scope.formData.autoSwitch > 0) {
            $scope.formData.autoSwitch--;
        }
    };
    $scope.incrementSC = function () {
        $scope.formData.scaleCubes++;
    };
    $scope.decrementSC = function () {
        if ($scope.formData.scaleCubes > 0) {
            $scope.formData.scaleCubes--;
        }
    };
    $scope.incrementWC = function () {
        $scope.formData.switchCubes++;
    };
    $scope.decrementWC = function () {
        if ($scope.formData.switchCubes > 0) {
            $scope.formData.switchCubes--;
        }
    };
    $scope.incrementEC = function () {
        $scope.formData.enemySwitchCubes++;
    };
    $scope.decrementEC = function () {
        if ($scope.formData.enemySwitchCubes > 0) {
            $scope.formData.enemySwitchCubes--;
        }
    };
    $scope.incrementVC = function () {
        $scope.formData.vaultCubes++;
    };
    $scope.decrementVC = function () {
        if ($scope.formData.vaultCubes > 0) {
            $scope.formData.vaultCubes--;
        }
    };
});

app.controller('PitFormController', function ($rootScope, $scope, $http, $window, AccountService) {
    'use strict';

    AccountService.validateSession().then(function (response) {
        $scope.resetForm();
    }, function (error) {
        AccountService.logout();
        displayMessage('You are logged out', 'warning');
    });

    $scope.resetForm = function () {
        $scope.pitFormData = {
            id: $rootScope.user.id
        };
        $scope.pictures = [];
        $scope.picNum = [];
    };

    $(document).ready(function () {
        $('#pitForm').validate();
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
            $("body").scrollTop(0);
            displayMessage("<strong>Hold up...</strong> Your data is being uploaded now...", "info");
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
                console.log(response.data);
                $('body').scrollTop(0);
                $scope.resetForm();
                displayMessage("<strong>Success!</strong> Now do it again.", "success");
            }, function (response) {
                console.log("Error during submission");
                console.log(response);
            });
        } else {
            console.log("Not valid");
        }
    }

});

app.controller('PitController', function ($scope, $http, $routeParams, $location, $window) {
    'use strict';

    $scope.teamNumber = $routeParams.teamNumber;

    $scope.error = "";

    $scope.teamLink = function () {
        $('#errorModal').on('hidden.bs.modal', function () {
            $location.path("/team/" + $scope.teamNumber);
            $scope.$apply();
        });
    }

    $scope.pitData = {
        pictures: [],
        comments: [],
        token: $window.localStorage["token"]
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

    $http.get('php/getScouterTeams.php').then(function (response) {
        $scope.teams = response.data;
        if ($scope.teams.length) {
            $scope.currentTeam = $scope.teams[0];
            $scope.loadData($scope.currentTeam.team_number);
        }
    });

    $scope.loadData = function (scoutingTeam) {
        $http.get('php/getPitData.php', {
            params: {
                teamNumber: $routeParams.teamNumber,
                scoutingTeam: scoutingTeam
            }
        }).then(function (response) {
            $scope.data = response.data;

            if ($scope.data.teamInfo.name != null) {
                $scope.name = $scope.data.teamInfo.name + ($scope.data.teamInfo.name[$scope.data.teamInfo.name.length - 1] == "s" ? "'" : "'s");
            } else {
                $scope.name = $scope.teamNumber + "'s";
            }

            if (response.data.commentSection != null) {
                $scope.noComments = false;
                $scope.pitData.comments.splice(0);
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
                $scope.noPictures = false;
                $scope.pitData.pictures.splice(0);
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
        }, function (response) {
            $scope.error = response.data.error;
        });
    };
});

app.controller("ListController", function ($rootScope, $scope, $http) {
    'use strict';
    $scope.sortType = 'avg_score';
    $scope.sortDescending = true;

    $http.get('php/getScouterTeams.php').then(function (response) {
        $scope.teams = response.data;
        if ($scope.teams.length) {
            $scope.currentTeam = $scope.teams[0];
            $scope.loadData($scope.currentTeam.team_number);
        }
    });

    $scope.loadData = function (teamNumber) {
        $http.get('php/list.php', {
            params: { teamNumber: teamNumber }
        }).then(function (response) {
            $scope.data = response.data;
            for (var i = 0; i < $scope.data.length; i++) {
                if ($scope.data[i].avg_score == null) {
                    $scope.data[i].avg_score = "No match scouting data available, only pit scouting data.";
                    $scope.data[i].avg_climb = "No match scouting data available, only pit scouting data.";
                    $scope.data[i].avg_tele_switch = "No match scouting data available, only pit scouting data.";
                    $scope.data[i].avg_tele_scale = "No match scouting data available, only pit scouting data.";
                    $scope.data[i].avg_vault = "No match scouting data available, only pit scouting data.";
                } else {
                    $scope.data[i].team_number = parseInt($scope.data[i].team_number);
                    $scope.data[i].avg_score = parseFloat(parseFloat($scope.data[i].avg_score).toFixed(2));
                    $scope.data[i].avg_climb = parseFloat(parseFloat($scope.data[i].avg_climb * 100).toFixed(2));
                    $scope.data[i].avg_tele_switch = parseFloat(parseFloat($scope.data[i].avg_tele_switch).toFixed(2));
                    $scope.data[i].avg_tele_scale = parseFloat(parseFloat($scope.data[i].avg_tele_scale).toFixed(2));
                    $scope.data[i].total_auto_cubes = parseInt($scope.data[i].total_auto_cubes);
                    $scope.data[i].avg_vault = parseFloat(parseFloat($scope.data[i].avg_vault).toFixed(2));
                }
                $scope.data[i].team_name = $scope.data[i].team_name != null ? $scope.data[i].team_name : "Name unavailable";
            }

            console.log($scope.data);
        });
    };

    $scope.filterTeams = function (value) {
        var searchRegExp = new RegExp($scope.search, "i");
        var teamString = value.team_number.toString();
        return value.team_name != null ? (value.team_name.match(searchRegExp) || teamString.match(searchRegExp)) : teamString.match(searchRegExp);
    }
});

app.controller("MatchListCntroller", function ($scope, $http, $location) {
    $http.get('php/matchList.php').then(function (response) {
        $scope.matches = response.data;

        $scope.matches.sort(function (a, b) {
            a = a.match_number;
            b = b.match_number;

            if (a < b) {
                return -1;
            } else if (a > b) {
                return 1;
            }

            return 0;
        });

        for (var i = 0; i < $scope.matches.length; i++) {
            for (var j = 0; j < 3; j++) {
                $scope.matches[i].alliances.red.team_keys[j] = parseInt($scope.matches[i].alliances.red.team_keys[j].slice(3));
            }
            for (var j = 0; j < 3; j++) {
                $scope.matches[i].alliances.blue.team_keys[j] = parseInt($scope.matches[i].alliances.blue.team_keys[j].slice(3));
            }
        }

        console.log($scope.matches);
    });

    $scope.goToMatch = function (matchNumber) {
        $location.path("/match/" + matchNumber);
    };

    $scope.filterMatches = function (value) {
        var searchRegExp = new RegExp($scope.search, "i");
        var red1 = value.alliances.red.team_keys[0].toString();
        var red2 = value.alliances.red.team_keys[1].toString();
        var red3 = value.alliances.red.team_keys[2].toString();
        var blue1 = value.alliances.blue.team_keys[0].toString();
        var blue2 = value.alliances.blue.team_keys[1].toString();
        var blue3 = value.alliances.blue.team_keys[2].toString();
        var matchNumber = value.match_number.toString();
        return matchNumber.match(searchRegExp) ||
            red1.match(searchRegExp) ||
            red2.match(searchRegExp) ||
            red3.match(searchRegExp) ||
            blue1.match(searchRegExp) ||
            blue2.match(searchRegExp) ||
            blue3.match(searchRegExp);
    };
});

app.controller("JoeBannanas", function ($rootScope, $scope, $http, $window) {
    'use strict';

    $scope.refreshByteCoins = function () {
        $http.post("php/getByteCoins.php", {
            token: $window.sessionStorage["token"]
        }).then(function (response) {
            $rootScope.user.byteCoins = $scope.byteCoins = response.data;
            $("#byteCoinsWagered").slider('setAttribute', 'max', parseInt($scope.byteCoins) + 1);
        }, function (response) {
            displayMessage("Could not properly get your number of Byte Coins. Please log in and try again", "danger");
        });
    };

    $scope.refreshByteCoins();

    $scope.manuallyEnterByteCoins = false;

    $scope.toggleManualByteCoins = function () {
        $scope.manuallyEnterByteCoins = !$scope.manuallyEnterByteCoins;
    }

    $scope.selectedMatch = false;

    $scope.selectMatch = function (match) {
        $scope.selectedMatch = {
            number: match.matchNumber,
            red: [
                match.Teams[0].teamNumber,
                match.Teams[1].teamNumber,
                match.Teams[2].teamNumber
            ],
            blue: [
                match.Teams[3].teamNumber,
                match.Teams[4].teamNumber,
                match.Teams[5].teamNumber
            ]
        };
        $scope.currentWager.matchPredicted = match.matchNumber;
        $("#match-modal").modal('hide');
    }

    $scope.reportSuccess = function (wager) {
        $scope.refreshByteCoins();
    };

    $scope.reportError = function (error) {
        $scope.lastError = error;
    };
    $scope.generateMatchs = function () {
        $http.get("php/currentWageringMatches.php").then(function (response) {
            $scope.Schedule = response.data["Schedule"];
            console.log($scope.Schedule);
        }, function (response) {
            displayMessage("Failed to get match data", "danger");
        });
    }
    $scope.generateMatchs();

    $scope.toOptionLabel = function (teams) {
        return teams[0].teamNumber + "-" + teams[1].teamNumber + "-" +
            teams[2].teamNumber + " vs " + teams[3].teamNumber + "-" +
            teams[4].teamNumber + "-" + teams[5].teamNumber;
    };

    $scope.resetForm = function () {
        console.log($("#confirm-wager-modal").modal('hide'));
        $("#byteCoinsWagered").slider('setValue', 0);
        $scope.selectedMatch = false;
        $scope.currentWager = {
            wagerType: '',
            wageredByteCoins: 0,
            alliancePredicted: '',
            matchPredicted: 0,
            withenPoints: 0,
            minPointsPredicted: 0,
            getMultiplier: function () {
                if (this.wagerType === "alliance") {
                    return 2;
                } else if (this.wagerType === "closeMatch") {
                    return 5 - (parseInt(this.withenPoints, 10) / (12.5));
                } else if (this.wagerType === "points") {
                    return (parseInt(this.minPointsPredicted, 10) / 110) + (parseInt(this.minPointsPredicted, 10) / 350);
                } else {
                    return 0;
                }
            },
            getValue: function () {
                return Math.floor(this.wageredByteCoins * this.getMultiplier());
            }
        }
    };

    $(document).ready(function () {
        $scope.resetForm();
    });

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
        $("#confirm-wager-modal").modal('hide');
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
        } else if ($scope.currentWager.wagerType === "points" && $scope.currentWager.minPointsPredicted && $scope.currentWager.matchPredicted) {
            postObject = {
                token: $window.sessionStorage["token"],
                wagerType: "points",
                wageredByteCoins: $scope.currentWager.wageredByteCoins,
                matchPredicted: $scope.currentWager.matchPredicted,
                alliancePredicted: $scope.currentWager.alliancePredicted,
                withenPoints: $scope.currentWager.minPointsPredicted
            };
        } else {
            displayMessage("Incorrect wager format. Did you fill all of the fields?", "danger");
        }
        $http.post("php/wager.php", postObject).then(function (response) {
            $scope.reportSuccess(response.data.message);
            $scope.resetForm();
        }, function (response) {
            displayMessage("Failed to send Wager", "danger");
        });
    };
});

app.controller("LeaderboardsController", function ($scope, $http) {
    'use strict';
    $scope.sortType = 'byteCoins';
    $scope.sortDesceding = true;
    $http.get('php/leaderboards.php').then(function (response) {
        $scope.data = response.data;
        for (var i = 0; i < $scope.data.length; i++) {
            $scope.data[i].byteCoins = parseInt($scope.data[i].byteCoins);
        }
    });
});

app.controller("TeamController", function ($scope, $http, $routeParams) {
    'use strict';

    $scope.teamNumber = $routeParams.teamNumber;
    $scope.error = "";
    $scope.isClimbComment = false;
    $scope.climbComments = [];

    $(document).ready(function () {
        $scope.matchCollapsed = true;
        $scope.teleMatchCollapsed = true;
        $scope.autoMatchCollapsed = true;
        $("#collapseMatch").collapse("show");
        $("#collapseTeleMatch").collapse("show");
        $("#collapseAutoMatch").collapse("show");
    });

    $http.get('php/getScouterTeams.php').then(function (response) {
        $scope.teams = response.data;
        if ($scope.teams.length) {
            $scope.currentTeam = $scope.teams[0];
            $scope.loadData($scope.currentTeam.team_number);
        }
    });

    $scope.loadData = function (scoutingTeam) {
        $http.get("php/getTeam.php", {
            params: {
                teamNumber: $routeParams.teamNumber,
                scoutingTeam: scoutingTeam
            }
        }).then(function (response) {
            $scope.data = response.data;
            console.log($scope.data);

            for (var i = 0; i < $scope.data.formData.length; i++) {
                if ($scope.data.formData[i].other_climb != "") {
                    $scope.isClimbComment = true;
                    $scope.climbComments.push({
                        "name": $scope.data.formData[i].name,
                        "match_number": $scope.data.formData[i].match_number,
                        "other_climb": $scope.data.formData[i].other_climb
                    });
                }
            }
        }, function (response) {
            $scope.error = response.data.error;
            console.error($scope.error);
        });
    };


    $scope.chooseBar = function (value) {
        if (value <= 1) {
            value *= 100;
        }
        if (value >= 80) {
            return "bg-success"
        } else if (value < 80 && value > 40) {
            return "bg-warning"
        } else {
            return "bg-danger"
        }
    }

});

app.controller("MatchController", function ($scope, $http, $routeParams) {
    'use strict';

    $(document).ready(function () {
        $scope.autoCollapsed = false;
        $scope.teleCollapsed = false;
        $scope.commentsCollapsed = false;
        $("#collapseAuto").collapse("show");
        $("#collapseTele").collapse("show");
        $("#collapseComments").collapse("show");
    });

    $scope.matchNumber = $routeParams.matchNumber;
    $scope.error = "";

    $http.get('php/getScouterTeams.php').then(function (response) {
        $scope.teams = response.data;
        if ($scope.teams.length) {
            $scope.currentTeam = $scope.teams[0];
            $scope.loadData($scope.currentTeam.team_number);
        }
    });

    $scope.loadData = function (scoutingTeam) {
        $http.get('php/getMatch.php', {
            params: {
                scoutingTeam: scoutingTeam,
                matchNumber: $routeParams.matchNumber
            }
        }).then(function (response) {
            $scope.data = response.data;

            console.log($scope.data);
        }, function (response) {
            $scope.error = response.data.error;

            console.error($scope.error);
        });
    };
});

app.controller("ScouterController", function ($scope, $http, $routeParams) {
    'use strict';

    $scope.scouterId = $routeParams.scouterId;

    $scope.error = "";

    $scope.commentSection = {
        comment: []
    };

    $http.get("php/getScouter.php", {
        params: {
            scouterId: $routeParams.scouterId
        }
    }).then(function (response) {
        $scope.data = response.data;

        console.log($scope.data)
    }, function (response) {
        $scope.error = response.data.error;
        console.log($scope.error);
    });
});

app.controller('AdminPageController', function ($rootScope, $scope, $http, $window, $location, AccountService) {
    'use strict';

    AccountService.validateSession().then(function (response) {
        if ($rootScope.user.username != "admin") {
            console.log("If you are looking at this, then you probably tried to force your way in here. If you tried to force your way in here, you could probably find the funciton that logged this. But be warned. Our security is more complicated than an if statement, and deeper then some client-side javascript. If you think you can hack this, then Game On. (But seriously don't cause I don't want to deal with incorrect data)");
            AccountService.logout();
        }
    }, function (response) {
        if ($rootScope.user.username != "admin") {
            console.log("If you are looking at this, then you probably tried to force your way in here. If you tried to force your way in here, you could probably find the funciton that logged this. But be warned. Our security is more complicated than an if statement, and deeper then some client-side javascript. If you think you can hack this, then Game On. (But seriously don't cause I don't want to deal with incorrect data)");
            AccountService.logout();
        }
    });

    $scope.prettySettings = {
        validateTeams: ($rootScope.settings.validateTeams == true) ? "Enabled" : "Disabled",
        enableCasino: ($rootScope.settings.enableCasino == true) ? "Enabled" : "Disabled",
        blue1Closest: ($rootScope.settings.blue1Closest == true) ? "closest" : "farthest"
    };

    $scope.updatePrettySettings = function () {
        $scope.prettySettings.validateTeams = ($rootScope.settings.validateTeams == true) ? "Enabled" : "Disabled";
        $scope.prettySettings.enableCasino = ($rootScope.settings.enableCasino == true) ? "Enabled" : "Disabled";
        $scope.prettySettings.blue1Closest = ($rootScope.settings.blue1Closest == true) ? "closest" : "farthest";
    };

    $scope.adminAction = function (pageAction, setting, value) {
        var post = {
            token: $window.localStorage["token"],
            action: pageAction
        };
        switch (pageAction) {
            case 'update_team':
                post.teamNumber = $scope.teamNumber;
                break;
            case 'update_wagers':
                post.matchNumber = $scope.matchNumber;
                break;
            case 'updateSettings':
                post.setting = setting;
                post.settingValue = value == 'true' ? true : false;
                break;
        }
        $http.post("php/adminAction.php", post).then(function (response) {
            $rootScope.getCurrentSettings(function () {
                $scope.updatePrettySettings();
            });
        }, function (error) {
            console.log(error);
        });
    }
});

app.directive('defensesCrossedSelector', function () {
    'use strict';
    return {
        templateUrl: 'html/defenseSelector.html',
        scope: {
            defensesCrossed: '=modelTo'
        }
    };
});

app.directive('ballsScoredSelector', function () {
    'use strict';
    return {
        templateUrl: 'html/ballsSelector.html',
        scope: {
            ballsScored: '=modelTo'
        }
    };
});

app.directive('numberPicker', function () {
    'use strict';
    return {
        templateUrl: 'html/numberPicker.html',
        scope: {
            num: '='
        }
    }
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
        templateUrl: 'html/list.html',
        controller: 'ListController'
    }).when('/wager', {
        templateUrl: 'html/casino.html',
        controller: 'JoeBannanas'
    }).when("/scouting", {
        templateUrl: 'html/form.html',
        controller: 'FormController'
    }).when("/team/:teamNumber", {
        templateUrl: 'html/team.html',
        controller: 'TeamController'
    }).when("/match/:matchNumber", {
        templateUrl: 'html/match.html',
        controller: 'MatchController'
    }).when("/match-list", {
        templateUrl: 'html/matchList.html',
        controller: 'MatchListCntroller'
    }).when("/pit-scouting", {
        templateUrl: 'html/pitForm.html',
        controller: 'PitFormController'
    }).when("/pit/:teamNumber", {
        templateUrl: 'html/pit.html',
        controller: 'PitController'
    }).when("/login", {
        templateUrl: 'html/login.html',
        controller: 'LoginController'
    }).when("/logout", {
        templateUrl: 'html/login.html',
        controller: 'LogoutController'
    }).when("/register", {
        templateUrl: 'html/register.html',
        controller: 'RegisterController'
    }).when("/scouter/:scouterId", {
        templateUrl: 'html/scouter.html',
        controller: 'ScouterController'
    }).when("/admin", {
        templateUrl: 'html/admin.html',
        controller: 'AdminPageController'
    }).otherwise({
        redirectTo: '/'
    });
}]);

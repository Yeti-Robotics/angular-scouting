/*global angular, $*/

var app;
app = angular.module('app', ['ngRoute']);

function displayMessage(message, alertType, timeVisible) {
	"use strict";
	timeVisible = timeVisible == undefined ? 3000 : timeVisible;
	$('.message-container').html(message).removeClass('alert-success alert-info alert-warning alert-danger').addClass('alert-' + alertType).stop(true).slideDown(500).delay(timeVisible).slideUp(500);
}

app.run(function ($rootScope, $location, $http, $window) {
	'use strict';
	$rootScope.user = {
		username: '',
		name: '',
		byteCoins: 0,
		logOut: function () {
			$window.sessionStorage.removeItem('token');
			this.name = '';
			$rootScope.loggedIn = false;
		}
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
		} else if ($location.path() == '/admin' && $rootScope.user.username != 'admin') {
			console.log("If you are looking at this, then you probably tried to force your way in here. If you tried to force your way in here, you could probably find the funciton that logged this. But be warned. Our security is more complicated than an if statement, and deeper then some client-side javascript. If you think you can hack this, then Game On. (But seriously don't cause I don't want to deal with incorrect data)");
			$location.path('/');
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
		$http.post('php/checkUser.php', {
			username: $scope.scouterUsername,
			pswd: $scope.scouterPswd
		}).then(function (response) {
			var result = response.data;
			$window.sessionStorage["token"] = result.token;
			$rootScope.user.name = result.name;
			$rootScope.user.username = $scope.scouterUsername;
			$rootScope.loggedIn = true;
			if ($scope.scouterUsername == "admin") {
				$location.path('/admin');
			} else {
				$location.path('/wager');
			}
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

	$scope.matchesReceived = true;

	$scope.selectedTeam = false;

	$scope.matches = [];

	$rootScope.getCurrentSettings(function () {
		if ($rootScope.settings.validateTeams) {
			$http.get("php/getFutureMatches.php").then(function (response) {
				for (var i = 0; i < response.data.Schedule.length; i++) {
					$scope.matches.push({
						teams: {
							red: [
								response.data.Schedule[i].Teams[0].teamNumber,
								response.data.Schedule[i].Teams[1].teamNumber,
								response.data.Schedule[i].Teams[2].teamNumber
							],
							blue: [
								response.data.Schedule[i].Teams[3].teamNumber,
								response.data.Schedule[i].Teams[4].teamNumber,
								response.data.Schedule[i].Teams[5].teamNumber
							]
						},
						number: response.data.Schedule[i].matchNumber
					});
				}
				$scope.matchesReceived = true;
			}, function (response) {
				displayMessage("Uh oh! Something went wrong with getting the future matches, looks like you'll have to enter the info manually. Try again later.", "danger");
				$scope.matchesReceived = false;
			});
		}
	});

	$scope.selectTeam = function (matchNumber, teamNumber) {
		$scope.formData.match_number = matchNumber;
		$scope.formData.team_number = teamNumber;
		$scope.selectedTeam = true;
		$("#match-modal").modal('hide');
	};

	$scope.resetForm = function () {
		$scope.formData = {
			name: $rootScope.user.name,
			robot_moved: false,
			auto_gear: false,
			autoHighGoal: false,
			autoHighAccuracy: "0",
			autoShootSpeed: "0",
			autoLowGoal: false,
			autoLowAccuracy: "0",
			teleHighGoal: false,
			teleHighAccuracy: "0",
			teleShootSpeed: "0",
			teleLowGoal: false,
			teleLowAccuracy: "0",
			teleGears: "0",
			load: "0",
			climbed: false,
			score: 0,
			comments: ""
		};
	};

	$(document).ready(function () {
		$scope.validator = $('#scouting_form').validate();
		$("#comments").rules("add", {
			required: true
		});
		console.log('Inititalize validation');

		$scope.resetForm();
	});

	$scope.submit = function () {
		if ($('#scouting_form').valid()) {
			console.log("valid");
			$("button[type='submit']").addClass("disabled");
			$("body").scrollTop(0);
			displayMessage("<strong>Hold up...</strong> Your data is being uploaded now...", "info");

			$scope.formData.autoHighAccuracy = parseInt($scope.formData.autoHighAccuracy);
			$scope.formData.autoShootSpeed = parseInt($scope.formData.autoShootSpeed);
			$scope.formData.autoLowAccuracy = parseInt($scope.formData.autoLowAccuracy);
			$scope.formData.teleHighAccuracy = parseInt($scope.formData.teleHighAccuracy);
			$scope.formData.teleShootSpeed = parseInt($scope.formData.teleShootSpeed);
			$scope.formData.teleLowAccuracy = parseInt($scope.formData.teleLowAccuracy);
			$scope.formData.teleGears = parseInt($scope.formData.teleGears);
			$scope.formData.load = parseInt($scope.formData.load);

			$http.post('php/formSubmit.php', $scope.formData).then(function (response) {
				console.log("submitted");
				$scope.matches.shift();
				$rootScope.getCurrentSettings();
				console.log(response.data);
				$('#scouting_form').trigger('reset');
				displayMessage("<strong>Success!</strong> Now do it again.", "success");
			$("button[type='submit']").removeClass("disabled");
				$scope.resetForm();
			}, function (response) {
				console.log("Error during submission");
				console.log(response.data);
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
			$("body").scrollTop(0);
			displayMessage("<strong>Hold up...</strong> Your data is being uploaded now...", "info");
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

	$http.get('php/getPitData.php', {
		params: {
			teamNumber: $routeParams.teamNumber
		}
	}).then(function (response) {
		$scope.data = response.data;

		if ($scope.data.teamInfo.name != null) {
			$scope.name = $scope.data.teamInfo.name + ($scope.data.teamInfo.name[$scope.data.teamInfo.name.length - 1] == "s" ? "'" : "'s");
		} else {
			$scope.name = $scope.teamNumber + "'s";
		}

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
	}, function (response) {
		$scope.error = response.data.error;
	});
});

app.controller("ListController", function ($rootScope, $scope, $http) {
	'use strict';
	$scope.sortType = 'avgScore';
	$scope.sortReverse = true;

	$scope.filterTeams = function (value) {
		var searchRegExp = new RegExp($scope.search, "i");
		var teamString = value.team.toString();
		return value.name != null ? (value.name.match(searchRegExp) || teamString.match(searchRegExp)) : teamString.match(searchRegExp);
	}

	$http.get('php/list.php').then(function (response) {
		$scope.data = response.data;
		for (var i = 0; i < $scope.data.length; i++) {
			$scope.data[i].team = parseInt($scope.data[i].team);
			$scope.data[i].avgScore = parseInt($scope.data[i].avgScore);
			$scope.data[i].totalGears = parseInt($scope.data[i].totalGears);
			$scope.data[i].avgClimbed = parseInt($scope.data[i].avgClimbed);
			$scope.data[i].name = $scope.data[i].name != null ? $scope.data[i].name : "Name unavailable";
		}
	});
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
	$scope.sortReverse = true;
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


	$scope.commentSection = {
		comments: []
	};

	$(document).ready(function () {
		setTimeout(function () {
			$('[data-toggle="tooltip"]').each(function () {
				$(this).attr("style", "outline: none");
				$(this).tooltip({
					container: 'body',
					placement: 'top',
					trigger: 'focus'
				});
			});
		}, 1000);
	});

	$http.get("php/getTeam.php", {
		params: {
			teamNumber: $routeParams.teamNumber
		}
	}).then(function (response) {
		$scope.data = response.data;

		if ($scope.data.teamInfo.name != null) {
			$scope.pitName = $scope.data.teamInfo.name + ($scope.data.teamInfo.name[$scope.data.teamInfo.name.length - 1] == "s" ? "'" : "'s");
		} else {
			$scope.pitName = $scope.teamNumber + "'s";
		}

		$scope.range = function (n) {
			return new Array(n);
		};
		
		for (var i = 0; i < $scope.data.misc.length; i++) {
			switch ($scope.data.misc[i].load) {
				case 0:
					$scope.data.misc[i].load = "Less than 50";
					break;
				case 1:
					$scope.data.misc[i].load = "~50";
					break;
				case 2:
					$scope.data.misc[i].load = "~100";
					break;
				case 3:
					$scope.data.misc[i].load = "~150";
					break;
				case 4:
					$scope.data.misc[i].load = "More than 150";
					break;
			}
		}
		
		for (var i = 0; i < $scope.data.match.teleop.length; i++) {
			switch ($scope.data.match.teleop[i].teleHighAccuracy) {
				case 0:
					$scope.data.match.teleop[i].teleHighAccuracy = "0% (No Accuracy)";
					break;
				case 1:
					$scope.data.match.teleop[i].teleHighAccuracy = "~30% (Low Accuracy)";
					break;
				case 2:
					$scope.data.match.teleop[i].teleHighAccuracy = "~50% (Medium Accuracy)";
					break;
				case 3:
					$scope.data.match.teleop[i].teleHighAccuracy = "~80% (High Accuracy)";
					break;
			}
			switch ($scope.data.match.teleop[i].teleLowAccuracy) {
				case 0:
					$scope.data.match.teleop[i].teleLowAccuracy = "0% (No Accuracy)";
					break;
				case 1:
					$scope.data.match.teleop[i].teleLowAccuracy = "~30% (Low Accuracy)";
					break;
				case 2:
					$scope.data.match.teleop[i].teleLowAccuracy = "~50% (Medium Accuracy)";
					break;
				case 3:
					$scope.data.match.teleop[i].teleLowAccuracy = "~80% (High Accuracy)";
					break;
			}
			switch ($scope.data.match.auto[i].autoHighAccuracy) {
				case 0:
					$scope.data.match.auto[i].autoHighAccuracy = "0% (No Accuracy)";
					break;
				case 1:
					$scope.data.match.auto[i].autoHighAccuracy = "~30% (Low Accuracy)";
					break;
				case 2:
					$scope.data.match.auto[i].autoHighAccuracy = "~50% (Medium Accuracy)";
					break;
				case 3:
					$scope.data.match.auto[i].autoHighAccuracy = "~80% (High Accuracy)";
					break;
			}
			switch ($scope.data.match.auto[i].autoLowAccuracy) {
				case 0:
					$scope.data.match.auto[i].autoLowAccuracy = "0% (No Accuracy)";
					break;
				case 1:
					$scope.data.match.auto[i].autoLowAccuracy = "~30% (Low Accuracy)";
					break;
				case 2:
					$scope.data.match.auto[i].autoLowAccuracy = "~50% (Medium Accuracy)";
					break;
				case 3:
					$scope.data.match.auto[i].autoLowAccuracy = "~80% (High Accuracy)";
					break;
			}
		}
		
		for (var i = 0; i < $scope.data.match.teleop.length; i++) {
			switch ($scope.data.match.teleop[i].teleShootSpeed) {
				case 0:
					$scope.data.match.teleop[i].teleShootSpeed = "Slow";
					break;
				case 1:
					$scope.data.match.teleop[i].teleShootSpeed = "Moderate";
					break;
				case 2:
					$scope.data.match.teleop[i].teleShootSpeed = "Fast";
					break;
				case 3:
					$scope.data.match.teleop[i].teleShootSpeed = "Super Fast";
					break;
				case 4:
					$scope.data.match.teleop[i].teleShootSpeed = "LightSpeed";
					break;
			}
			switch ($scope.data.match.auto[i].autoShootSpeed) {
				case 0:
					$scope.data.match.auto[i].autoShootSpeed = "Slow";
					break;
				case 1:
					$scope.data.match.auto[i].autoShootSpeed = "Moderate";
					break;
				case 2:
					$scope.data.match.auto[i].autoShootSpeed = "Fast";
					break;
				case 3:
					$scope.data.match.auto[i].autoShootSpeed = "Super Fast";
					break;
				case 4:
					$scope.data.match.auto[i].autoShootSpeed = "LightSpeed";
					break;
			}
		}
		
		console.log($scope.data);
	}, function (response) {
		$scope.error = response.data.error;
		console.log($scope.error);
	});
});

app.controller('AdminPageController', function ($rootScope, $scope, $http, $window) {
	'use strict';

	$scope.prettySettings = {
		validateTeams: ($rootScope.settings.validateTeams == true) ? "Enabled" : "Disabled",
		enableCasino: ($rootScope.settings.enableCasino == true) ? "Enabled" : "Disabled"
	};

	$scope.updatePrettySettings = function () {
		$scope.prettySettings.validateTeams = ($rootScope.settings.validateTeams == true) ? "Enabled" : "Disabled";
		$scope.prettySettings.enableCasino = ($rootScope.settings.enableCasino == true) ? "Enabled" : "Disabled";
	};

	$scope.adminAction = function (pageAction, setting, value) {
		var post = {
			token: $window.sessionStorage["token"],
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
		}, function (response) {
			console.log("Post response: " + response.data);
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
	}).when("/admin", {
		templateUrl: 'html/admin.html',
		controller: 'AdminPageController'
	}).otherwise({
		redirectTo: '/'
	});
}]);

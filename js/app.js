/*global angular, $*/

var app;
app = angular.module('app', ['ngRoute']);

function displayMessage(message, alertType, timeVisible = 3000) {
	$('.message-container').html(message).removeClass('alert-success alert-info alert-warning alert-danger').addClass('alert-' + alertType).stop(true).slideDown(500).delay(timeVisible).slideUp(500);
}

app.run(function ($rootScope, $location, $http, $window, AccountService) {
	'use strict';

	$rootScope.loggedIn = $window.sessionStorage.token != null;

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
			token: $window.sessionStorage["token"]
		}).finally(function (response) {
			$window.sessionStorage.removeItem("token");
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
			token: $window.sessionStorage["token"]
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
				$window.sessionStorage["token"] = result.token;
				$rootScope.loggedIn = true;
				if ($scope.scouterUsername == "admin") {
					$location.path('/admin');
				} else {
					$location.path('/scouting');
				}
			}, function (response) {
				$("#loginForm").validate().showErrors({
					"loginFields": "Invalid username/password"
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

	$scope.resetForm = function () {
		$scope.formData = {
			autoCheck: false,
			teleCheck: false,
			cubeRanking: '1',
			teleSpeed: '1',
			scaleCubes: 0,
			switchCubes: 0,
			enemySwitchCubes: 0
		};
	};

	$scope.resetForm();

	$(document).ready(function () {
		$scope.validator = $('#scouting_form').validate();
	});

	$scope.submit = function () {
		if ($('#scouting_form').valid()) {
			$http.post('php/formSubmit.php', $scope.formData)
				.then(function (data) {
					displayMessage('Form submitted successfully', 'success');
					$scope.resetForm();
				}, function (error) {
					displayMessage('Failed to submit form', 'danger');
					console.error(error);
				});
		}
	};
});

app.controller('PitFormController', function ($rootScope, $scope, $http, $window) {
	'use strict';

	$scope.resetForm = function () {
		$scope.pitFormData = {
			id: $rootScope.user.id
		};
		$scope.pictures = [];
		$scope.picNum = [];
	};

	$scope.resetForm();

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
		console.log($scope.picNum);
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
		var teamString = value.team_number.toString();
		return value.team_name != null ? (value.team_name.match(searchRegExp) || teamString.match(searchRegExp)) : teamString.match(searchRegExp);
	}

	$http.get('php/list.php').then(function (response) {
		$scope.data = response.data;
		for (var i = 0; i < $scope.data.length; i++) {
			$scope.data[i].bar_climb = parseInt($scope.data[i].bar_climb);
			$scope.data[i].avg_score = parseInt($scope.data[i].avg_score);
			$scope.data[i].team_number = parseInt($scope.data[i].team_number);
			$scope.data[i].team_name = $scope.data[i].team_name != null ? $scope.data[i].team_name : "Name unavailable";
		}
	});
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

	$http.get("php/getTeam.php", {
		params: {
			teamNumber: $routeParams.teamNumber
		}
	}).then(function (response) {
		$scope.data = response.data;
		console.log($scope.data);
	}, function (response) {
		$scope.error = response.data.error;
		console.error($scope.error);
	});
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

angular.module('app', [
	//Core
	'ngRoute',
	'ngResource',
	'ngCookies',
        'ngDraggable',
	'ui.bootstrap',
        'ngDialog',

	//Modules
	'controllers',
	'services'

])
.config(function($httpProvider, $routeProvider, $locationProvider) {
    $httpProvider.defaults.withCredentials = true;
	// $locationProvider.html5Mode(true);

	// Rutas de los diferentes html

	$routeProvider
		.when('/login', {
			controller:'LoginCtrl',
			templateUrl:'../../angular_templates/login.html'
		})
		.when('/', {
			controller:'menuCtrl',
			templateUrl:'../../angular_templates/MenuView.html'
		})
		.when('/adeu', {
			controller:'AdeuCtrl',
			templateUrl:'../../angular_templates/adeu.html'
		})
		.when('/register', {
			controller:'RegisterCtrl',
			templateUrl:'../../angular_templates/register.html'
		})
                .when('/board', {
			controller:'myCtrl',
			templateUrl:'../../angular_templates/MainBoard.html'
		})
		.when('/userConfig', {
			controller:'UserConfCtrl',
			templateUrl:'../../angular_templates/userConfig.html'
		})
                .when('/registerComplete', {
			controller:'RegisterCtrl',
			templateUrl:'../../angular_templates/registerComplete.html'
		})
                .when('/emailValidation/:emailKey/:id', {
			controller:'emailValidationCtrl',
			templateUrl:'../../angular_templates/emailValidation.html'
		})
		.otherwise({ redirectTo:'/' });
})
.run(function(AuthService){

	//Comprobamos el token para el login en services.js
	AuthService.init();
});


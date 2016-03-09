angular.module('app', [
	//Core
	'ngRoute',
	'ngResource',
	'ngCookies',
	'ui.bootstrap',

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
			controller:'MainCtrl',
			templateUrl:'../../angular_templates/main.html'
		})
		.when('/adeu', {
			controller:'AdeuCtrl',
			templateUrl:'../../angular_templates/adeu.html'
		})
		.when('/register', {
			controller:'RegisterCtrl',
			templateUrl:'../../angular_templates/register.html'
		})
                .when('/userConfig', {
			controller:'UserConfCtrl',
			templateUrl:'../../angular_templates/userConfig.html'
		})
		.otherwise({ redirectTo:'/' });
})
.run(function(AuthService){

	//Comprobamos el token para el login en services.js
	AuthService.init();	
});


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
			templateUrl:'../../jocomunicoapp/angular_templates/login.html'
		})
		.when('/', {
			controller:'menuCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/MenuView.html'
		})
		.when('/adeu', {
			controller:'AdeuCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/adeu.html'
		})
		.when('/register', {
			controller:'RegisterCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/register.html'
		})
                .when('/board', {
			controller:'myCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/MainBoard.html'
		})
		.otherwise({ redirectTo:'/' });
})
.run(function(AuthService){

	//Comprobamos el token para el login en services.js
	AuthService.init();	
});


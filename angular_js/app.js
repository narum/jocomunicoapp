angular.module('app', [
	//Core
	'ngRoute',
	'ngResource',
	'ngCookies',
        'ngDraggable',
	'ui.bootstrap',
        'ngDialog',
        'ngAudio',
        'ngScrollbar',
        'ngAnimate',
        
	//Modules
	'controllers',
	'services',
        'udpCaptcha'

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
			controller:'myCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/MainBoard.html'
		})
		.when('/register', {
			controller:'RegisterCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/register.html'
		})
		.when('/userConfig', {
			controller:'UserConfCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/userConfig.html'
		})
                .when('/registerComplete', {
			controller:'RegisterCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/registerComplete.html'
		})
                .when('/emailValidation/:emailKey/:id', {
			controller:'emailValidationCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/emailValidation.html'
		})
                .when('/emailSended', {
			controller:'LoginCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/emailSended.html'
		})
                .when('/passRecovery/:emailKey/:id', {
			controller:'passRecoveryCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/passRecovery.html'
		})
                .when('/panelGroups', {
			controller:'panelCtrl',
			templateUrl:'../../jocomunicoapp/angular_templates/PanelGroups.html'
		})
		.otherwise({ redirectTo:'/' });
})
.run(function(AuthService){

	//Comprobamos el token para el login en services.js
	AuthService.init();
});


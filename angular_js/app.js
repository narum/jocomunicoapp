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
			templateUrl:'../../angular_templates/login.html'
		})
		.when('/', {
			controller:'myCtrl',
			templateUrl:'../../angular_templates/MainBoard.html'
		})
		.when('/register', {
			controller:'RegisterCtrl',
			templateUrl:'../../angular_templates/register.html'
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
                .when('/emailSended', {
			controller:'LoginCtrl',
			templateUrl:'../../angular_templates/emailSended.html'
		})
                .when('/passRecovery/:emailKey/:id', {
			controller:'passRecoveryCtrl',
			templateUrl:'../../angular_templates/passRecovery.html'
		})
                .when('/panelGroups', {
			controller:'panelCtrl',
			templateUrl:'../../angular_templates/PanelGroups.html'
		})
		.otherwise({ redirectTo:'/' });
})
.run(function(AuthService){

	//Comprobamos el token para el login en services.js
	AuthService.init();
})
.run(function($rootScope){
    //Dropdown Menu Bar
    $rootScope.dropdownMenuBarChangeLanguage = false;
    
    $rootScope.dropdownMenuBar = [];
    $rootScope.dropdownMenuBar.push({name: 'Log out', href: '/', iconInitial: '/img/srcWeb/DropdownMenuBar/clauIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/clauIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/clauIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Privacitat', href: '/privacity', iconInitial: '/img/srcWeb/DropdownMenuBar/lockIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/lockIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/lockIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Contacte', href: '/contact', iconInitial: '/img/srcWeb/DropdownMenuBar/mailIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/mailIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/mailIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Tutorial', href: '/tutorial', iconInitial: '/img/srcWeb/DropdownMenuBar/tutorialIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/tutorialIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/tutorialIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'FAQ', href: '/faq', iconInitial: '/img/srcWeb/DropdownMenuBar/faqIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/faqIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/faqIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Configuració', href: '/userConfig', iconInitial: '/img/srcWeb/DropdownMenuBar/configIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/configIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/configIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Panells', href: '/panelGroups', iconInitial: '/img/srcWeb/DropdownMenuBar/panellsIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/panellsIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/panellsIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Editar panell', href: '/', iconInitial: '/img/srcWeb/DropdownMenuBar/editaPanellIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/editaPanellIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/editaPanellIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Sobre joComunico', href: '/info', iconInitial: '/img/srcWeb/DropdownMenuBar/sobrejocomIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/sobrejocomIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/sobrejocomIconSelected.png', show:true});
    $rootScope.dropdownMenuBar.push({name: 'Inici', href: '/', iconInitial: '/img/srcWeb/DropdownMenuBar/iniciIcon.png', iconHover: '/img/srcWeb/DropdownMenuBar/iniciIconHover.png', iconSelected: '/img/srcWeb/DropdownMenuBar/iniciIconSelected.png', show:true});

});


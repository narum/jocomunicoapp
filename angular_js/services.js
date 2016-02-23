angular.module('services', [])

.factory('Resources', function($rootScope, $resource){
	var baseUri = $rootScope.baseurl; // base URL enviado des de codeigniter mediante ng-init en main.html
	return {

		// Rutas de las API para las peticiones a codeigniter
		
		"nom": $resource(baseUri + "names"),
		"histo": $resource(baseUri + "histo"),
		"login": $resource(baseUri + "login"),
		"main": $resource(baseUri + "main")
	};
})

.factory('AuthService', function($rootScope, $http){

	// Funciones de comprobación del login
	
	return {
		"init": function() {
			$rootScope.isLogged = false;
			var token = window.localStorage.getItem('token'); //mirem si hi ha un token al LocalStorage de html5
			var languageid = window.localStorage.getItem('languageid'); 
			var languageabbr = window.localStorage.getItem('languageabbr');
			if(token)
				this.login(token, languageid, languageabbr);
		},
		"login": function(token, languageid, languageabbr) {
			window.localStorage.setItem('token', token); // guardem el token al localStorage
			window.localStorage.setItem('languageid', languageid); // guardem el idlanguage al localStorage
			window.localStorage.setItem('languageabbr', languageabbr); // guardem el nomlanguage al localStorage
			$http.defaults.headers.common['Authorization'] = 'Bearer '+token; // posem el token al header per a totes les peticions
			$http.defaults.headers.common['X-Authorization'] = 'Bearer '+token; // posem el token al header per a totes les peticions
			$rootScope.isLogged = true;
			$rootScope.languageid = languageid;
			$rootScope.languageabbr = languageabbr;
		},
		"logout": function() {
			window.localStorage.removeItem('token');
			window.localStorage.removeItem('languageid');
			window.localStorage.removeItem('languageabbr');
			delete $http.defaults.headers.common['Authorization'];
			$rootScope.isLogged = false;
		}
	}
})


//Función que retorna el contenido de texto de la vista al pasarle los parametros section y language
.factory('txtContent',  function(Resources, $http, $rootScope){
	return function name(section){

		var languageid = $rootScope.languageid;
		
		return Resources.main.get({'section':section, 'idLanguage':languageid}).$promise;

	}
});
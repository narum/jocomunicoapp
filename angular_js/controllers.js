angular.module('controllers', [])

// Controlador del Login

.controller('LoginCtrl', function($scope, Resources, $location, AuthService){
	
	var loginResource = Resources.login;

	// Función que coje el user y pass y comprueba que sean correctos
	$scope.login = function(form) {
		var body = { 
			user: $scope.username,
			pass: $scope.password
		};

		// Petición del login
		loginResource.save(body).$promise  // POST (en angular 'save') del user y pass
		.then(function(result){				// respuesta ok!
			var token = result.data.token;
			var languageid = result.data.languageid;
			var languageabbr = result.data.languageabbr;
			AuthService.login(token, languageid, languageabbr);
			$location.path('/home');
		})
		.catch(function(error){	// no respuesta
			alert('Nombre de usuario o contraseña erroneo');
			console.log(error);	
		});
	};
})
.controller('RegisterCtrl', function($scope){
	
	$scope.formData = {};

	$scope.submitForm = function (formData) {
    	alert('Form submitted with' + JSON.stringify(formData));
	};
})


// Controlador del buscador de pictogramas

.controller('MainCtrl', function($rootScope, $scope, $location, Resources, AuthService, txtContent){
	
	// Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
	if(!$rootScope.isLogged){
		$location.path('/login');
	}

	// Pedimos los textos para cargar la pagina
	txtContent("pictoSearch").then(function(results){
			$rootScope.content = results.data;
	});

	// Variables
	var namesResource = Resources.nom;
	var historyResource = Resources.histo;
	
	$scope.imatges = [];
	$scope.typeaheadOptions = {
	    "debounce": {
	    	"default": 500,
	    	"blur": 250
	    }
	};

	// Función buscar nombres y pictogramas
	$scope.buscar = function(val){
		if (!val || val == "") {
			return;
		}
		$scope.lastSearch = val;
		return namesResource.get({'startswith':val, 'language':$scope.languageabbr}).$promise
		.then(function(results){
			return results.data;
		});
	};

	// Función seleccionar pictograma
	$scope.onSelect = function(item, model, label, evt){
  		$scope.img = item;
  		$scope.asyncNom = $scope.lastSearch;
  		console.log(item, model);					//borrar
  	};

  	// Función historial de pictogramas
  	$scope.afegir = function() {
		historyResource.get({'pictoid': $scope.img.nameid}).$promise
		.then(function(results){
			$scope.hist = results.data;
		});

		$scope.imatges.push({url:$scope.img.imgPicto, done:false});
	};


	// Función salir del login
	$scope.sortir = function() {
		AuthService.logout();
		$location.path('/login');
	}

})

// Controlador de prueba

.controller('AdeuCtrl', function($rootScope, $scope, $location){
	if(!$rootScope.isLogged){
		$location.path('/login');
	} else {
		$scope.goodbye = "Adeu!!";
	}
});
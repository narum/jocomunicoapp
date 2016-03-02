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



//Controlador del registro de usuario
        .controller('RegisterCtrl', function($scope, Resources, $location){
            
            // Pedimos los textos para cargar la pagina, por defecto en catalan, y los idiomas disponibles
    Resources.register.get({'section':"userRegister"},{'funct':"content"}).$promise
            .then(function(results){
                $allContent = results;
        $scope.contentLanguage = true; //true = CATALAN, false = Castellano
        $scope.changeContentLanguage();
    });
    
    //Cambiar de idioma el contenido
    $scope.changeContentLanguage=function(){
        if($scope.contentLanguage){
            $scope.content = $allContent.catalan;
            $scope.languages = [{language: $allContent.catalan.catalan},{language: $allContent.catalan.spanish}];
            $scope.languageForm = $allContent.spanish.spanish;
        } else {
            $scope.content = $allContent.spanish;
            $scope.languages = [{language: $allContent.spanish.catalan},{language: $allContent.spanish.spanish}];
            $scope.languageForm = $allContent.catalan.catalan;
        }
    };
    
    //Inicializamos el formulario
    $scope.formData = {};		//Datos del formulario
    $scope.languageList = []; 	//lista de idiomas seleccionados
    $scope.state ={user:"", password:""};
    
    //Borrar el formulario
    $scope.resetForm = function(){
        $scope.formData = {};
        $scope.registerForm.$setPristine();//poner el formulario en estado inicial
    };
    
    //Validación del usuario
    $scope.checkUser=function(formData){
        if (formData.user.length < 4) {
            $scope.state.user = 'has-warning';
            $scope.registerForm.$invalid = true; // y el formulario como invalido.
        } else {
            Resources.register.get({ //enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
                'table':"SuperUser",
                'column':"SUname",
                'data':formData.user},{'funct':"checkData"}).$promise
                    .then(function(results){
                        if (results.exist == "false") {
                            $scope.state.user = 'has-success'; //Si no exixte el nombre ponemos el checkbox en success
                } else if (results.exist == "true") {
                    $scope.state.user = 'has-error'; //Si exixte el nombre ponemos el checkbox en error
                    $scope.registerForm.$invalid = true; // y el formulario como invalido.
                }
            });
        }
    };
    
    //Validar la igualdad de los dos passwords
    $scope.checkPassword=function(formData){
        if (formData.password.length < 4 || formData.password.length === undefined) {
            $scope.state.password = 'has-warning';
            $scope.registerForm.$invalid = true; // y el formulario como invalido.
        } else {
            $scope.state.password = 'has-success';
            var passOk=true;
            console.log($scope.state.password);
        }
        if (formData.password != formData.confirmPassword && passOk && $scope.registerForm.confirmPassword.$dirty) {
            $scope.state.confirmPassword = 'has-warning';
            $scope.registerForm.$invalid = true; // y el formulario como invalido.
        }else
            if (formData.password == formData.confirmPassword) {
                $scope.state.confirmPassword = 'has-success';
            }
    };
    
    //Añadir idiomas
    $scope.addLanguage=function(languageSelected){
        $scope.languageList.push({language:languageSelected.language}); //añadimos el lenguaje a la lista
        $scope.state.languageSelected = 'has-success';
        $scope.registerForm.$invalid = false; // y el formulario como valido.
        // Borramos el lenguaje selecionado del desplegable
        if ($scope.languages[0].language == languageSelected.language) {
            $scope.languages.splice(0, 1); //Borrar item de un array .splice(posicion, numero de items)
            $scope.contentLanguageDisable = true; // desactivamos el boton de cambiar idioma para evitar conflictos
        } else if($scope.languages[1].language == languageSelected.language){
            $scope.languages.splice(1, 1); //Borrar item de un array .splice(posicion, numero de items)
            $scope.contentLanguageDisable = true; // desactivamos el boton de cambiar idioma para evitar conflictos
        }
    };
    
    //Quitar idiomas
    $scope.removeLanguage=function(language){
        $scope.languages.push({language:language}); //añadimos el lenguaje a la lista
        // Borramos el lenguaje selecionado del desplegable
        if ($scope.languageList[0].language == language) {
            $scope.languageList.splice(0, 1); //Borrar item de un array .splice(posicion, numero de items)
        } else if($scope.languageList[1].language == language){
            $scope.languageList.splice(1, 1); //Borrar item de un array .splice(posicion, numero de items)
        }
        if ($scope.languageList.length == 0) {
            $scope.state.languageSelected = 'has-error';
            $scope.contentLanguageDisable = false; // activamos el boton de cambiar idioma para evitar conflictos
            $scope.registerForm.$invalid = true; // y el formulario como invalido.
        }
    };
    
    //Comprobar que ha entrado texto en el campo nombre
    $scope.checkName=function(){
        if ($scope.registerForm.name.$error.required) {
            $scope.state.name = 'has-error';
        }else{
            $scope.state.name = 'has-success';
        }
    };
    
    //Comprobar que ha entrado texto en el campo apellidos
    $scope.checkLastname=function(){
        if ($scope.registerForm.lastname.$error.required) {
            $scope.state.lastname = 'has-error';
        }else{
            $scope.state.lastname = 'has-success';
        }
    };
    
    //Validación del email
    var emailFormat = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    $scope.checkEmail=function(formData){
        if (String(formData.email).search(emailFormat) == -1) {
            console.log("hola");
            $scope.state.email = 'has-warning';
            $scope.registerForm.$invalid = true; // Formulario como invalido.
        } else {
            Resources.register.get({ //enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
                'table':"SuperUser",
                'column':"email",
                'data':formData.email},{'funct':"checkData"}).$promise
                    .then(function(results){
                        if (results.exist == "false") {
                            $scope.state.email = 'has-success'; //Si no exixte el nombre ponemos el checkbox en success
                } else if (results.exist == "true") {
                    $scope.state.email = 'has-error'; //Si exixte el nombre ponemos el checkbox en error
                    $scope.registerForm.$invalid = true; // y el formulario como invalido.
                }
            });
        }
    };
    
    
    
    $scope.submitForm = function (formData) {
        
        if ($scope.languageList.length <= 0) {
            $scope.state.languageSelected = 'has-error';
            $scope.registerForm.$invalid = true;
        } else {
            alert('Form submitted with' + JSON.stringify(formData));
    	}
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
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
                var userid = result.data.userID;
                AuthService.login(token, languageid, languageabbr, userid);
                $location.path('/userConfig');
        })
            .catch(function(error){	// no respuesta
                alert('Nombre de usuario o contraseña erroneo');
                console.log(error);
        });
    };
})



//Controlador del registro de usuario
.controller('RegisterCtrl', function($scope, Resources, md5){
    
    //Inicializamos el formulario y las variables necesarias
        $scope.formData = {};  //Datos del formulario
        $scope.languageList = []; //lista de idiomas seleccionados por el usuario
        $scope.state ={user:"", password:""};// estado de cada campo del formulario
        var numberOfLanguages = 0;// numero de idiomas (inicialmente a 0 pero se actualiza automaticamente en la siguiente función al hacer la peticion a la base de datos)
        var userOk = false; // variables de validación
        var emailOk = false; // variables de validación
        var languageOk = false; // variables de validación
        
        $scope.imgButton = $scope.baseurl + 'img/BotoCrearUsuari.png'; //Imagen del boton submit
        var currentLanguage = 1; // idioma por defecto al iniciar (catalan)
        
    //Pedimos los idiomas disponibles
    Resources.register.get({'section':'userRegister'},{'funct':"allContent"}).$promise
        .then(function(results){
            $scope.availableLanguageOptions=results.languages;// Idiomas disponibles para el desplegable del formulario
            content=results.content;// Contenido en cada idioma
            $scope.content=content[currentLanguage];// Contenido a mostrar en el idioma seleccionado
            $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;// nombre del siguiente idioma para el boton
            numberOfLanguages = ($scope.availableLanguageOptions.length);// numero de idiomas
            
    });
    
    //Cambiar el idioma del contenido
    $scope.changeContentLanguage=function(){
        currentLanguage ++;
        // El content esta dentro de un array que empieza por la posición 1 y el nombre de cada idioma en un array que empieza en la posicion 0.
        if(currentLanguage > numberOfLanguages){
            currentLanguage = 1;
            $scope.content=content[1];
            $scope.languageNameNext = $scope.availableLanguageOptions[1].languageName;
        }else{
            $scope.content=content[currentLanguage];
            if((currentLanguage+1) > numberOfLanguages){
                $scope.languageNameNext = $scope.availableLanguageOptions[0].languageName;
            }else{
                $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;
            }
        }
    };

    //Borrar el formulario
    $scope.resetForm = function(){
        $scope.formData = {};
        $scope.registerForm.$setPristine();//poner el formulario en estado inicial
    };
    
    //Validación del usuario
    $scope.checkUser=function(formData){
        if(formData.SUname == null){
            $scope.state.user = 'has-warning';
            userOk = false;  // Usamos una variable en vez del return por que la función promise tarda mas en retornar el resultado y nos dava error al comprobarlo en el submit
            return;
        }
        if (formData.SUname.length < 4 || formData.SUname.length >= 50) { // minimo y maximo de caracteres requeridos
            $scope.state.user = 'has-warning';
            userOk = false;
        } else {
            Resources.register.get({ //enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
                'table':"SuperUser",
                'column':"SUname",
                'data':formData.SUname},{'funct':"checkData"}).$promise
                    .then(function(results){
                        if (results.exist == "false") {
                            $scope.state.user = 'has-success'; //Si no exixte el nombre ponemos el checkbox en success
                            userOk = true;
                        } else if (results.exist == "true") {
                            $scope.state.user = 'has-error'; //Si exixte el nombre ponemos el checkbox en error
                            userOk = false;
                        }
                    })
                    .catch(function(error){	// no respuesta
                        console.log('get_error:',error);
                        userOk = false;
                    });
        }
    };
    
    //Validar la igualdad de los dos passwords
    $scope.checkPassword=function(formData){
        if(formData.pswd == null || formData.pswd.length >= 32){ // minimo y maximo de caracteres requeridos
            $scope.state.password = 'has-warning';
            $scope.state.confirmPassword = 'has-warning';
            return false;
        }
        if (formData.pswd.length < 4) {
            $scope.state.password = 'has-warning';
            return false;
        } else {
            $scope.state.password = 'has-success';
            var passOk=true;
        }
        if (formData.pswd != formData.confirmPassword && passOk && $scope.registerForm.confirmPassword.$dirty) {
            $scope.state.confirmPassword = 'has-warning';
            return false;
        }else
            if (formData.pswd == formData.confirmPassword) {
                $scope.state.confirmPassword = 'has-success';
                return true;
            }
    };
    
    //Comprobar que ha entrado texto en el campo nombre
    $scope.checkName=function(formData){
        if(formData.realname == null || formData.realname == '' || formData.realname.length >= 200){ // minimo y maximo de caracteres requeridos
            $scope.state.name = 'has-error';
            return false;
        }else{
            $scope.state.name = 'has-success';
            return true;
        }
    };
    
    //Comprobar que ha entrado texto en el campo apellidos
    $scope.checkLastname=function(formData){
        if(formData.surnames == null || formData.surnames == '' || formData.surnames.length >= 300){ // minimo y maximo de caracteres requeridos
            $scope.state.lastname = 'has-error';
            return false;
        }else{
            $scope.state.lastname = 'has-success';
            return true;
        }
    };
    
    //Validación del email
    var emailFormat = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    $scope.checkEmail=function(formData){
        if(formData.email == null || formData.email == '' || formData.email.length >= 300){ // comprovacion de formato y minimo y maximo de caracteres requeridos
            $scope.state.email = 'has-warning';
            emailOk = false;
            return;
        }
        if (String(formData.email).search(emailFormat) == -1) {
            $scope.state.email = 'has-warning';
            emailOk = false;
        } else {
            Resources.register.get({ //enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
              'table':"SuperUser",
              'column':"email",
              'data':formData.email},{'funct':"checkData"}).$promise
                .then(function(results){
                    if (results.exist == "false") {
                        $scope.state.email = 'has-success'; //Si no exixte el nombre ponemos el checkbox en success
                        emailOk = true;
                    } else if (results.exist == "true") {
                        $scope.state.email = 'has-error'; //Si exixte el nombre ponemos el checkbox en error
                        emailOk = false;
                }
            });
        }
    };
    
    //Añadir idiomas
    $scope.addLanguage=function(idLanguage){
        angular.forEach($scope.availableLanguageOptions, function(value, key) {
            if(value.ID_Language == idLanguage){
                $scope.languageList.push($scope.availableLanguageOptions[key]);//añadimos el idioma a la lista .push(objeto)
                $scope.availableLanguageOptions.splice(key,1);//Borrar idioma de las opciones .splice(posicion, numero de items)
                $scope.state.languageSelected = 'has-success';
                languageOk=true;
            }
        });
    };
    
    //Quitar idiomas
    $scope.removeLanguage=function(index){
        $scope.availableLanguageOptions.push($scope.languageList[index]);
        $scope.languageList.splice(index,1);//Borrar item de un array .splice(posicion, numero de items)
    };
    
    $scope.submitForm = function (formData) {
       // Llamamos las funciones para printar el error en el formulario si nunca se han llamado
        $scope.checkUser(formData);
        $scope.checkEmail(formData);
        $scope.checkPassword(formData);
        $scope.checkName(formData);
        $scope.checkLastname(formData);
        // Comprobamos si el usuario ha introducido algun idioma
        if ($scope.languageList.length==0){
            $scope.state.languageSelected = 'has-error';
            languageOk=false;
        }
        // Comprobamos todos los campos del formulario accediendo a las funciones o mirando las variables de estado
        if (userOk&&$scope.checkPassword(formData)&&$scope.checkName(formData)&&$scope.checkLastname(formData)&&emailOk&&languageOk) {
            //Borramos los campos inecesarios
            delete formData.confirmPassword;
            delete formData.languageSelected;
            //Ponemos como idioma por defecto el primero de la lista que ha seleccionado el usuario
            formData.cfgDefLanguage = $scope.languageList[0].id;
            //Ciframos el password en md5
            $pass = formData.pswd;
            formData.pswd = md5.createHash($pass);
            //Pasamos los datos a formato JSON string
            var data = {'data':JSON.stringify(formData),'table':'SuperUser'};
            //enviamos los datos del formulario.
            Resources.register.save(data,{'funct':"saveData"}).$promise
                .then(function(results){
                    console.log('response:', results);
                    alert('Form submitted with' + JSON.stringify(formData));

                angular.forEach($scope.languageList, function(value) {

                    Resources.register.save({'SUname':formData.SUname,'ID_ULanguage':value.ID_Language},{'funct':"saveUserData"}).$promise
                    .then(function(results){
                        console.log('response:', results);
                    });
                });
            });
        }
    };
})

//Controlador de la configuración de usuario
.controller('UserConfCtrl', function($scope, Resources, AuthService, txtContent, $location){
    
    
    // Función salir del login
    $scope.sortir = function() {
        AuthService.logout();
        $location.path('/login');
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
    };
    
})

// Controlador de prueba
.controller('AdeuCtrl', function($rootScope, $scope, $location){
            if(!$rootScope.isLogged){
		$location.path('/login');
    } else {
        $scope.goodbye = "Adeu!!";
    }
});
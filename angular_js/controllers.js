angular.module('controllers', [])

// Controlador del Login

        .controller('LoginCtrl', function ($scope, Resources, $location, AuthService) {
            //Definición de variables
            $scope.viewActived = false; // para activar el gif de loading...
            $scope.view2 = false;// vista de recuperación de contraseña
            var loginResource = Resources.login;
            var currentLanguage = 1; // idioma por defecto al iniciar (catalan)
            var numberOfLanguages = 0;// numero de idiomas (inicialmente a 0 pero se actualiza automaticamente en la siguiente función al hacer la peticion a la base de datos)

            //Pedimos el contenido en los idiomas disponibles.
            Resources.register.get({'section': 'login'}, {'funct': "allContent"}).$promise
                    .then(function (results) {
                        $scope.availableLanguageOptions = results.languages;// Idiomas disponibles para el desplegable del formulario
                        content = results.content;// Contenido en cada idioma
                        $scope.content = content[currentLanguage];// Contenido a mostrar en el idioma seleccionado
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;// nombre del siguiente idioma para el boton
                        numberOfLanguages = ($scope.availableLanguageOptions.length);// numero de idiomas
                        $scope.viewActived = true; // para activar la vista
                    });
            //Cambiar el idioma del contenido
            $scope.changeContentLanguage = function () {
                currentLanguage++;
                // El content esta dentro de un array que empieza por la posición 1 y el nombre de cada idioma en un array que empieza en la posicion 0.
                if (currentLanguage > numberOfLanguages) {
                    currentLanguage = 1;
                    $scope.content = content[1];
                    $scope.languageNameNext = $scope.availableLanguageOptions[1].languageName;
                } else {
                    $scope.content = content[currentLanguage];
                    if ((currentLanguage + 1) > numberOfLanguages) {
                        $scope.languageNameNext = $scope.availableLanguageOptions[0].languageName;
                    } else {
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;
                    }
                }
            };

            // Función que coje el user y pass y comprueba que sean correctos
            $scope.login = function () {
                var body = {
                    user: $scope.username,
                    pass: $scope.password
                };
                // Petición del login
                loginResource.save(body).$promise  // POST (en angular 'save') del user y pass
                        .then(function (result) {				// respuesta ok!
                            console.log(result.data.userConfig);
                            var token = result.data.token;
                            var userConfig = result.data.userConfig;
                            if (userConfig.UserValidated == '1') {
                                AuthService.login(token, userConfig);
                                $location.path('/');
                            } else {
                                $scope.state = 'has-warning';
                            }
                        })
                        .catch(function (error) {	// no respuesta
                            $scope.state = 'has-error';
                            console.log(error);
                        });
            };
            // Cambiar estados del formulario
            $scope.changeFormState = function () {
                $scope.state = '';
                $scope.state2 = '';
            };

            // Renovar la contrasseña
            $scope.forgotPass = function () {
                Resources.register.save({'user': $scope.user}, {'funct': "passRecoveryMail"}).$promise
                        .then(function (results) {
                            console.log(results.message);
                            if (results.exist) { // Cambiar por results.sended cuando funcione el servidor smtp y envie el mail!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                $location.path('/emailSended');
                            } else {
                                $scope.state2 = 'has-error';
                            }
                        });
            };
        })

//Controlador del registro de usuario
        .controller('RegisterCtrl', function ($scope, $rootScope, Resources, md5, $q, $location) {

            //Inicializamos el formulario y las variables necesarias
            $scope.formData = {};  //Datos del formulario
            $scope.languageList = []; //lista de idiomas seleccionados por el usuario
            $scope.state = {user: "", password: ""};// estado de cada campo del formulario
            var numberOfLanguages = 0;// numero de idiomas (inicialmente a 0 pero se actualiza automaticamente en la siguiente función al hacer la peticion a la base de datos)
            var userOk = false; // variables de validación
            var emailOk = false; // variables de validación
            var languageOk = false; // variables de validación
            var currentLanguage = 1; // idioma por defecto al iniciar (catalan)
            $scope.viewActived = false; // para activar el gif de loading...

            //Pedimos los idiomas disponibles
            Resources.register.get({'section': 'userRegister'}, {'funct': "allContent"}).$promise
                    .then(function (results) {
                        $scope.availableLanguageOptions = results.languages;// Idiomas disponibles para el desplegable del formulario
                        content = results.content;// Contenido en cada idioma
                        $scope.content = content[currentLanguage];// Contenido a mostrar en el idioma seleccionado
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;// nombre del siguiente idioma para el boton
                        numberOfLanguages = ($scope.availableLanguageOptions.length);// numero de idiomas
                        $scope.viewActived = true; // para activar la vista del formulario

                    });

            //Cambiar el idioma del contenido
            $scope.changeContentLanguage = function () {
                currentLanguage++;
                // El content esta dentro de un array que empieza por la posición 1 y el nombre de cada idioma en un array que empieza en la posicion 0.
                if (currentLanguage > numberOfLanguages) {
                    currentLanguage = 1;
                    $scope.content = content[1];
                    $scope.languageNameNext = $scope.availableLanguageOptions[1].languageName;
                } else {
                    $scope.content = content[currentLanguage];
                    if ((currentLanguage + 1) > numberOfLanguages) {
                        $scope.languageNameNext = $scope.availableLanguageOptions[0].languageName;
                    } else {
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;
                    }
                }
            };

            //Borrar el formulario
            $scope.resetForm = function () {
                $scope.formData = {};
                $scope.registerForm.$setPristine();//poner el formulario en estado inicial
            };

            //Validación del usuario
            $scope.checkUser = function (formData) {
                if (formData.SUname == null) {
                    $scope.state.user = 'has-warning';
                    userOk = false;  // Usamos una variable en vez del return por que la función promise tarda mas en retornar el resultado y nos dava error al comprobarlo en el submit
                    return;
                }
                if (formData.SUname.length < 4 || formData.SUname.length >= 50) { // minimo y maximo de caracteres requeridos
                    $scope.state.user = 'has-warning';
                    userOk = false;
                } else {
                    Resources.register.get({//enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
                        'table': "SuperUser",
                        'column': "SUname",
                        'data': formData.SUname}, {'funct': "checkData"}).$promise
                            .then(function (results) {
                                if (results.exist == "false") {
                                    $scope.state.user = 'has-success'; //Si no exixte el nombre ponemos el checkbox en success
                                    userOk = true;
                                } else if (results.exist == "true") {
                                    $scope.state.user = 'has-error'; //Si exixte el nombre ponemos el checkbox en error
                                    userOk = false;
                                }
                            })
                            .catch(function (error) { // no respuesta
                                console.log('get_error:', error);
                                userOk = false;
                            });
                }
            };

            //Validar la igualdad de los dos passwords
            $scope.checkPassword = function (formData) {
                if (formData.pswd == null || formData.pswd.length >= 32) { // minimo y maximo de caracteres requeridos
                    $scope.state.password = 'has-warning';
                    $scope.state.confirmPassword = 'has-warning';
                    return false;
                }
                if (formData.pswd.length < 4) {
                    $scope.state.password = 'has-warning';
                    return false;
                } else {
                    $scope.state.password = 'has-success';
                    var passOk = true;
                }
                if (formData.pswd != formData.confirmPassword && passOk && $scope.registerForm.confirmPassword.$dirty) {
                    $scope.state.confirmPassword = 'has-warning';
                    return false;
                } else
                if (formData.pswd == formData.confirmPassword) {
                    $scope.state.confirmPassword = 'has-success';
                    return true;
                }
            };

            //Comprobar que ha entrado texto en el campo nombre
            $scope.checkName = function (formData) {
                if (formData.realname == null || formData.realname == '' || formData.realname.length >= 200) { // minimo y maximo de caracteres requeridos
                    $scope.state.name = 'has-error';
                    return false;
                } else {
                    $scope.state.name = 'has-success';
                    return true;
                }
            };

            //Comprobar que ha entrado texto en el campo apellidos
            $scope.checkLastname = function (formData) {
                if (formData.surnames == null || formData.surnames == '' || formData.surnames.length >= 300) { // minimo y maximo de caracteres requeridos
                    $scope.state.lastname = 'has-error';
                    return false;
                } else {
                    $scope.state.lastname = 'has-success';
                    return true;
                }
            };

            //Validación del email
            var emailFormat = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
            $scope.checkEmail = function (formData) {
                if (formData.email == null || formData.email == '' || formData.email.length >= 300) { // comprovacion de formato y minimo y maximo de caracteres requeridos
                    $scope.state.email = 'has-warning';
                    emailOk = false;
                    return;
                }
                if (String(formData.email).search(emailFormat) == -1) {
                    $scope.state.email = 'has-warning';
                    emailOk = false;
                } else {
                    Resources.register.get({//enviamos los datos de la tabla de la base de datos donde queremos comprobar el nombre
                        'table': "SuperUser",
                        'column': "email",
                        'data': formData.email}, {'funct': "checkData"}).$promise
                            .then(function (results) {
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
            $scope.addLanguage = function (idLanguage) {
                angular.forEach($scope.availableLanguageOptions, function (value, key) {
                    if (value.ID_Language == idLanguage) {
                        $scope.languageList.push($scope.availableLanguageOptions[key]);//añadimos el idioma a la lista .push(objeto)
                        $scope.availableLanguageOptions.splice(key, 1);//Borrar idioma de las opciones .splice(posicion, numero de items)
                        $scope.state.languageSelected = 'has-success';
                        languageOk = true;
                    }
                });
            };

            //Quitar idiomas
            $scope.removeLanguage = function (index) {
                $scope.availableLanguageOptions.push($scope.languageList[index]);
                $scope.languageList.splice(index, 1);//Borrar item de un array .splice(posicion, numero de items)
            };

            //Genero de la aplicación (Masculino/femenino)
            $scope.sex = function (sex) {
                if (sex == 'female') {
                    $scope.state.female = 'has-success';
                    $scope.state.male = '';
                    $scope.formData.cfgIsFem = '1';
                    return true;
                } else if (sex == 'male') {
                    $scope.state.female = '';
                    $scope.state.male = 'has-success';
                    $scope.formData.cfgIsFem = '0';
                    return true;
                }
                if (sex.cfgIsFem == null || sex.cfgIsFem == '') {
                    $scope.state.female = 'has-error';
                    $scope.state.male = 'has-error';
                    return false;
                } else {
                    return true;
                }
            }

            $scope.submitForm = function (formData) {
                // Llamamos las funciones para printar el error en el formulario si nunca se han llamado
                $scope.checkUser(formData);
                $scope.checkEmail(formData);
                $scope.checkPassword(formData);
                $scope.checkName(formData);
                $scope.checkLastname(formData);
                $scope.sex(formData);
                // Comprobamos si el usuario ha introducido algun idioma
                if ($scope.languageList.length == 0) {
                    $scope.state.languageSelected = 'has-error';
                    languageOk = false;
                }
                // Comprobamos todos los campos del formulario accediendo a las funciones o mirando las variables de estado
                if (userOk && $scope.checkPassword(formData) && $scope.checkName(formData) && $scope.checkLastname(formData) && emailOk && languageOk && $scope.sex(formData)) {
                    $location.path('/registerComplete');

                    //Borramos los campos inecesarios
                    delete formData.confirmPassword;
                    delete formData.languageSelected;
                    //Ponemos como idioma por defecto el primero de la lista que ha seleccionado el usuario
                    formData.cfgDefUser = $scope.languageList[0].ID_Language;
                    //Ciframos el password en md5
                    $pass = formData.pswd;
                    formData.pswd = md5.createHash($pass);
                    //Pasamos los datos a formato JSON string
                    var data = {'data': JSON.stringify(formData), 'table': 'SuperUser'};
                    //enviamos los datos del formulario.
                    Resources.register.save(data, {'funct': "saveData"}).$promise
                            .then(function (results) {
                                console.log('response:', results);
                                var promises = []; //PROMESAS
                                angular.forEach($scope.languageList, function (value) {
                                    var deferred = $q.defer();//PROMESAS
                                    //enviamos los usuarios con cada idioma.
                                    Resources.register.save({'SUname': formData.SUname, 'ID_ULanguage': value.ID_Language}, {'funct': "saveUserData"}).$promise
                                            .then(function (results) {
                                                deferred.resolve(results);//PROMESAS
                                                $id_su = results.ID_SU;
                                                console.log('response:', results);
                                            });
                                    promises.push(deferred.promise);//PROMESAS
                                });

                                //Funcion que se llama al finalizar todas las promesas
                                $q.all(promises).then(function () {
                                    //Vista confirmación
                                    Resources.register.save({'user': $id_su}, {'funct': "generateValidationMail"}).$promise
                                            .then(function (results) {
                                                console.log(results.message);
                                                if (results.exist) { // Cambiar por results.sended cuando funcione el servidor smtp y envie el mail!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                                    $scope.viewActived = true; // para activar la vista
                                                }
                                            });
                                });
                            });
                }
            };
        })

//User email validation
        .controller('emailValidationCtrl', function ($scope, $routeParams, Resources) {
            $scope.activedValidation = false;// para activar el gif de loading
            $scope.viewActived = false; // para activar el gif de loading...
            //Pedimos los idiomas disponibles
            var currentLanguage = 1; // idioma por defecto al iniciar (catalan)
            Resources.register.get({'section': 'emailValidation'}, {'funct': "allContent"}).$promise
                    .then(function (results) {
                        $scope.availableLanguageOptions = results.languages;// Idiomas disponibles para el desplegable del formulario
                        content = results.content;// Contenido en cada idioma
                        $scope.content = content[currentLanguage];// Contenido a mostrar en el idioma seleccionado
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;// nombre del siguiente idioma para el boton
                        numberOfLanguages = ($scope.availableLanguageOptions.length);// numero de idiomas
                        $scope.viewActived = true; // para activar la vista

                    });
            //Cambiar el idioma del contenido
            $scope.changeContentLanguage = function () {
                currentLanguage++;
                // El content esta dentro de un array que empieza por la posición 1 y el nombre de cada idioma en un array que empieza en la posicion 0.
                if (currentLanguage > numberOfLanguages) {
                    currentLanguage = 1;
                    $scope.content = content[1];
                    $scope.languageNameNext = $scope.availableLanguageOptions[1].languageName;
                } else {
                    $scope.content = content[currentLanguage];
                    if ((currentLanguage + 1) > numberOfLanguages) {
                        $scope.languageNameNext = $scope.availableLanguageOptions[0].languageName;
                    } else {
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;
                    }
                }
            };

            //Enviamos la clave y el id para comprobar el email del usuario
            Resources.register.save({'emailKey': $routeParams.emailKey, 'ID_SU': $routeParams.id}, {'funct': "emailValidation"}).$promise
                    .then(function (results) {
                        $scope.validated = results.validated;
                        $scope.activedValidation = true;// para activar la vista;
                    });
        })

//Password recovery controller
        .controller('passRecoveryCtrl', function ($scope, $routeParams, Resources, md5) {

            //HTML views
            $scope.linkExpiredView = false;
            $scope.enterPassView = false;
            $scope.passChangedView = false;

            //initialize variables
            $scope.formData = {};  //Datos del formulario
            $scope.state = {password: "", confirmPassword: ""};// estado de cada campo del formulario
            var currentLanguage = 1; // idioma por defecto al iniciar (catalan)

            //HTML text content
            Resources.register.get({'section': 'passRecovery'}, {'funct': "allContent"}).$promise
                    .then(function (results) {
                        $scope.availableLanguageOptions = results.languages;// Idiomas disponibles para el desplegable del formulario
                        content = results.content;// Contenido en cada idioma
                        $scope.content = content[currentLanguage];// Contenido a mostrar en el idioma seleccionado
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;// nombre del siguiente idioma para el boton
                        numberOfLanguages = ($scope.availableLanguageOptions.length);// numero de idiomas
                        $scope.viewActived = true; // para activar la vista
                    });

            //Check if url user exists
            Resources.register.save({'emailKey': $routeParams.emailKey, 'ID_SU': $routeParams.id}, {'funct': "emailValidation"}).$promise
                    .then(function (results) {
                        if (results.userExist) {
                            $scope.linkExpiredView = false;
                            $scope.enterPassView = true;
                            $scope.passChangedView = false;
                        } else {
                            $scope.linkExpiredView = true;
                            $scope.enterPassView = false;
                            $scope.passChangedView = false;
                        }
                    });

            //Change HTML text content language
            $scope.changeContentLanguage = function () {
                currentLanguage++;
                // El content esta dentro de un array que empieza por la posición 1 y el nombre de cada idioma en un array que empieza en la posicion 0.
                if (currentLanguage > numberOfLanguages) {
                    currentLanguage = 1;
                    $scope.content = content[1];
                    $scope.languageNameNext = $scope.availableLanguageOptions[1].languageName;
                } else {
                    $scope.content = content[currentLanguage];
                    if ((currentLanguage + 1) > numberOfLanguages) {
                        $scope.languageNameNext = $scope.availableLanguageOptions[0].languageName;
                    } else {
                        $scope.languageNameNext = $scope.availableLanguageOptions[currentLanguage].languageName;
                    }
                }
            };

            //Check if passwords are equal.
            $scope.checkPassword = function (formData) {
                if (formData.pswd == null || formData.pswd.length >= 32) { // minimo y maximo de caracteres requeridos
                    $scope.state.password = 'has-warning';
                    $scope.state.confirmPassword = 'has-warning';
                    return false;
                }
                if (formData.pswd.length < 4) {
                    $scope.state.password = 'has-warning';
                    return false;
                } else {
                    $scope.state.password = 'has-success';
                    var passOk = true;
                }
                if (formData.pswd != formData.confirmPassword && passOk && $scope.PassForm.confirmPassword.$dirty) {
                    $scope.state.confirmPassword = 'has-warning';
                    return false;
                } else
                if (formData.pswd == formData.confirmPassword) {
                    $scope.state.confirmPassword = 'has-success';
                    return true;
                }
            };
            //Send new password
            $scope.sendPass = function (formData) {

                if ($scope.checkPassword(formData)) {
                    //HTML views
                    $scope.linkExpiredView = false;
                    $scope.enterPassView = false;
                    $scope.passChangedView = false;
                    //md5 password encode and Json formating
                    $password = md5.createHash(formData.pswd);
                    $pass = '{"pswd":"' + $password + '"}';
                    //Send new password
                    Resources.register.save({'emailKey': $routeParams.emailKey, 'ID_SU': $routeParams.id, 'pass': $pass}, {'funct': "changePass"}).$promise
                            .then(function (results) {
                                if (results.passChanged) {
                                    $scope.linkExpiredView = false;
                                    $scope.enterPassView = false;
                                    $scope.passChangedView = true;
                                }
                            });
                }
            }
        })

//Controlador de la configuración de usuario
        .controller('UserConfCtrl', function ($scope, $rootScope, Resources, AuthService, txtContent, $location) {
            $scope.viewActived = false;
            // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
            if (!$rootScope.isLogged) {
                $location.path('/login');
            }
            // Pedimos los textos para cargar la pagina
            txtContent("userConfig").then(function (results) {
                $scope.content = results.data;
                $scope.viewActived = true;
            });

        })
// Controlador del buscador de pictogramas

        .controller('MainCtrl', function ($rootScope, $scope, $location, Resources, AuthService, txtContent) {

            // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
            if (!$rootScope.isLogged) {
                $location.path('/login');
            }

            // Pedimos los textos para cargar la pagina
            txtContent("pictoSearch").then(function (results) {
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
            $scope.buscar = function (val) {
                if (!val || val == "") {
                    return;
                }
                $scope.lastSearch = val;
                return namesResource.get({'startswith': val, 'language': $scope.languageabbr}).$promise
                        .then(function (results) {
                            return results.data;
                        });
            };

            // Función seleccionar pictograma
            $scope.onSelect = function (item, model, label, evt) {
                $scope.img = item;
                $scope.asyncNom = $scope.lastSearch;
                console.log(item, model);					//borrar
            };

            // Función historial de pictogramas
            $scope.afegir = function () {
                historyResource.get({'pictoid': $scope.img.nameid}).$promise
                        .then(function (results) {
                            $scope.hist = results.data;
                        });

                $scope.imatges.push({url: $scope.img.imgPicto, done: false});
            };


            // Función salir del login
            $scope.sortir = function () {
                AuthService.logout();
                $location.path('/login');
            }

        })

        .controller('myCtrl', function ($location, $scope, ngAudio, $http, ngDialog, txtContent, $rootScope, $interval, $timeout) {
            // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
            if (!$rootScope.isLogged) {
                $location.path('/login');
            }
            // Pedimos los textos para cargar la pagina
            txtContent("mainboard").then(function (results) {
                $rootScope.content = results.data;
            });
            // Get event Edit call in the mune bar
            $rootScope.$on("EditCallFromMenu", function () {
                $scope.edit();
            });
            // Get event Init call in the mune bar
            $rootScope.$on("IniciCallFromMenu", function () {
                //MODIF: Se tiene que hacer con configuracion de usuario

                $scope.config(4);
            });
            //MODIF: Solo para hacer pruebas
            $rootScope.$on("ScanCallFromMenu", function () {
                $scope.InitScan();
            });

            //MODIF: Coger de BBDD escaneo por intervalo o no en el if
            $scope.InitScan = function ()
            {
                var userConfig = JSON.parse(localStorage.getItem('testObject'));
                // 0 custom, 1 rows, 2 columns
                $scope.cfgScanningCustomRowCol = userConfig.cfgScanningCustomRowCol;
                $scope.inScan = true;
                $scope.timerScan = userConfig.cfgScanningAutoOnOff == 1 ? true : false;
                $scope.longclick = false;
                function myTimer() {
                    $scope.NextBlockScan();
                }
                if ($scope.timerScan) {
                    $interval.cancel($scope.intervalScan);
                    var Intervalscan = userConfig.cfgTimeScanning;
                    function myTimer() {
                        $scope.nextBlockScan();
                    }
                    ;
                    $scope.intervalScan = $interval(myTimer, Intervalscan);

                }

                $scope.arrayScannedCells = null;
                $scope.currentScanBlock = 1;
                $scope.currentScanBlock1 = 1;
                $scope.currentScanBlock2 = 1;
                $scope.isScanningCancel = false;
                if ($scope.cfgPredOnOff === '1') {
                    $scope.isScanning = "prediction";
                } else if(userConfig.cfgMenuDeleteLastActive + userConfig.cfgMenuDeleteAllActive + userConfig.cfgMenuReadActive > 1){
                    $scope.isScanning = "sentence";
                }else{
                    $scope.isScanning = "board";
                }
                $scope.getMaxScanBlock1();

            };
            // picto is -1 pred, 0 sentemce, others mainboard.
            $scope.isScanned = function (picto) {
                //Custom scan
                if ($scope.inScan && $scope.isScanning === "board" && $scope.isScanningCancel === false) {
                    if ($scope.cfgScanningCustomRowCol == 0 && (
                            (picto.customScanBlock1 == $scope.currentScanBlock1 && $scope.currentScanBlock == 1) ||
                            (picto.customScanBlock1 == $scope.currentScanBlock1 && $scope.currentScanBlock == 2 && picto.customScanBlock2 == $scope.currentScanBlock2) ||
                            ($scope.currentScanBlock == 3 && $scope.arrayScannedCells != null && $scope.indexScannedCells != -1 && picto.posInBoard == $scope.arrayScannedCells[$scope.indexScannedCells].posInBoard))) {
                        return true;
                    }
                    // Rows first
                    else if ($scope.cfgScanningCustomRowCol == 1 && (
                            ($scope.currentScanBlock == 1 && picto.posInBoard / $scope.columns <= $scope.currentScanBlock1 && picto.posInBoard / $scope.columns > $scope.currentScanBlock1 - 1) ||
                            ($scope.currentScanBlock == 2 && picto.posInBoard / $scope.columns <= $scope.currentScanBlock1 && picto.posInBoard / $scope.columns > $scope.currentScanBlock1 - 1 && (picto.posInBoard - 1) % $scope.columns == $scope.currentScanBlock2 - 1))) {
                        return true;
                    } else if ($scope.cfgScanningCustomRowCol == 2 && (
                            ($scope.currentScanBlock == 1 && (picto.posInBoard - 1) % $scope.columns == $scope.currentScanBlock1 - 1) ||
                            ($scope.currentScanBlock == 2 && (picto.posInBoard - 1) % $scope.columns == $scope.currentScanBlock1 - 1 && picto.posInBoard / $scope.columns <= $scope.currentScanBlock2 && picto.posInBoard / $scope.columns > $scope.currentScanBlock2 - 1))) {
                        return true;
                    }
                }
                return false;


            };
            // When we get out from scanMode stops the interval
            $scope.$watch('inScan', function () {
                if ($scope.inScan === false) {
                    $interval.cancel($scope.intervalScan);
                }
            });


            $scope.scanLeftClick = function ()
            {
                if ($scope.inScan) {
                    if (!$scope.longclick)
                    {
                        $scope.selectBlockScan();
                    } else if ($scope.timerScan == 0) {
                        $scope.nextBlockScan();
                    }
                }
            };
            $scope.playLongClick = function ()
            {
                var userConfig = JSON.parse(localStorage.getItem('testObject'));
                if ($scope.inScan) {
                    if ($scope.longclick)
                    {
                        $timeout.cancel($scope.scanLongClickTime);
                        $scope.scanLongClickController = true;
                        $scope.scanLongClickTime = $timeout($scope.selectBlockScan, userConfig.cfgTimeClick);
                    }
                }
            };
            $scope.cancelLongClick = function ()
            {
                if ($scope.inScan) {
                    if ($scope.longclick)
                    {
                        if ($scope.scanLongClickController)
                        {
                            $timeout.cancel($scope.scanLongClickTime);
                            $scope.nextBlockScan();


                        } else
                        {

                        }
                    }
                }
            };

            $scope.scanRightClick = function ()
            {
                if ($scope.inScan) {
                    if (!$scope.longclick)
                    {
                        $scope.nextBlockScan();
                    } else if ($scope.timerScan == 0) {
                        $scope.nextBlockScan();
                    }
                }
            };

            // Get the number of scan blocks
            $scope.getMaxScanBlock1 = function ()
            {
                if ($scope.cfgScanningCustomRowCol == 1) {
                    $scope.maxScanBlock1 = $scope.rows;
                } else if ($scope.cfgScanningCustomRowCol == 2) {
                    $scope.maxScanBlock1 = $scope.columns;
                } else if ($scope.cfgScanningCustomRowCol == 0) {
                    var url = $scope.baseurl + "Board/getMaxScanBlock1";
                    var postdata = {idboard: $scope.idboard};

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.maxScanBlock1 = response.max;
                    });
                }
            };
            // Get the number of level 2 scan blocks
            $scope.getMaxScanBlock2 = function ()
            {
                $scope.currentScanBlock2 = 1;
                if ($scope.cfgScanningCustomRowCol == 1) {
                    $scope.maxScanBlock2 = $scope.columns;
                } else if ($scope.cfgScanningCustomRowCol == 2) {
                    $scope.maxScanBlock2 = $scope.rows;
                } else if ($scope.cfgScanningCustomRowCol == 0) {
                    var url = $scope.baseurl + "Board/getMaxScanBlock2";
                    var postdata = {idboard: $scope.idboard, scanGroup: $scope.currentScanBlock1};

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.maxScanBlock2 = response.max;
                        // If there is no subgroup passes to the next scan level (3)
                        if ($scope.maxScanBlock2 === null) {
                            $scope.currentScanBlock2 = null;
                            $scope.isScanningCancel = false;
                            $scope.selectBlockScan();
                        }
                        // There is no group selected
                        if ($scope.maxScanBlock2 === "No group found") {
                            $scope.InitScan();
                        }
                    });
                }
            };
            // Change teh current scan block
            $scope.nextBlockScan = function () {
                if ($scope.inScan) {
                    if ($scope.isScanningCancel === true) {
                        $scope.isScanningCancel = false;
                    } else if($scope.isScanning === "goodPhrase"){
                        $scope.isScanning = "badPhrase";
                    } else if($scope.isScanning === "badPhrase"){
                        $scope.feedback(1);
                    }else {
                        if ($scope.currentScanBlock == 1) {
                            if ($scope.isScanning === "prediction") {
                                $scope.isScanning = "sentence";
                            } else if ($scope.isScanning === "sentence") {
                                $scope.isScanning = "board";
                                $scope.currentScanBlock1 = 1;
                            } else if ($scope.isScanning === "board") {
                                $scope.currentScanBlock1 = $scope.currentScanBlock1 + 1;
                                if ($scope.currentScanBlock1 > $scope.maxScanBlock1) {
                                    $scope.InitScan();
                                }
                            }
                        } else if ($scope.currentScanBlock == 2) {
                            if ($scope.isScanning === "prediction") {
                                $scope.indexScannedCells = $scope.indexScannedCells + 1;
                                // MODIF: cambiar a la cfg de usuario
                                if ($scope.indexScannedCells > $scope.arrayScannedCells.length) {
                                    $scope.InitScan();
                                }
                            } else if ($scope.isScanning === "read") {
                                if (JSON.parse(localStorage.getItem('testObject')).cfgMenuDeleteLastActive == 1) {
                                    $scope.isScanning = "deletelast";
                                } else if (JSON.parse(localStorage.getItem('testObject')).cfgMenuDeleteAllActive == 1) {
                                    $scope.isScanning = "deleteall";
                                } else {
                                    $scope.InitScan();
                                }
                            } else if ($scope.isScanning === "deletelast") {
                                if (JSON.parse(localStorage.getItem('testObject')).cfgMenuDeleteAllActive  == 1) {
                                    $scope.isScanning = "deleteall";
                                } else {
                                    $scope.InitScan();
                                }
                            } else if ($scope.isScanning === "deleteall") {
                                $scope.InitScan();
                            } else if ($scope.isScanning === "board") {
                                if ($scope.cfgScanningCustomRowCol == 0) {
                                    if ($scope.currentScanBlock2 !== null) {
                                        $scope.currentScanBlock2 = $scope.currentScanBlock2 + 1;
                                        if ($scope.currentScanBlock2 > $scope.maxScanBlock2) {
                                            $scope.currentScanBlock2 = null;
                                        }
                                    } else {
                                        $scope.InitScan();
                                    }
                                } else {
                                    $scope.currentScanBlock2 = $scope.currentScanBlock2 + 1;
                                    if ($scope.currentScanBlock2 > $scope.maxScanBlock2) {
                                        $scope.InitScan();
                                    }
                                }
                            }
                        }// Only custom scan
                        else if ($scope.currentScanBlock === 3) {
                            $scope.indexScannedCells = $scope.indexScannedCells + 1;
                            if ($scope.indexScannedCells > $scope.arrayScannedCells.length) {
                                $scope.InitScan();
                            }
                        }
                    }







//                    // If we are in the first scan level passes to the next (cyclic)
//                    if ($scope.currentScanBlock === 1) {
//                        $scope.currentScanBlock1 = $scope.currentScanBlock1 + 1;
//                        if ($scope.currentScanBlock1 > $scope.maxScanBlock1) {
//                            $scope.currentScanBlock1 = -1;
//                        }
//                    }// If we are in the second scan level passes to the next (cyclic) but...
//                    else if ($scope.currentScanBlock === 2 && $scope.cfgScanningCustomRowCol == 0) {
//                        // CurrentScanBlock will be null when we are over all the cell that have no scan block
//                        if ($scope.currentScanBlock2 !== null) {
//                            // If we are not over the mentioned scanblock pass to the next
//                            $scope.currentScanBlock2 = $scope.currentScanBlock2 + 1;
//                            // If we pass the last scan block...
//                            if ($scope.currentScanBlock2 > $scope.maxScanBlock2) {
//                                // We are scannig the prediction bar or the sentece bar so start again
//                                if ($scope.currentScanBlock1 == 0 || $scope.currentScanBlock1 == -1) {
//                                    $scope.InitScan();
//                                    //We have to go to the cells that have no scan block
//                                } else {
//                                    $scope.currentScanBlock2 = null;
//                                }
//
//
//                            }
//                            // If we are over this strange block, int scan again
//                        } else {
//                            $scope.InitScan();
//                        }
//
//                    } // If we are not in custom scan there are no null group so pass to the next(cyclic) 
//                    else if ($scope.currentScanBlock === 2 && ($scope.cfgScanningCustomRowCol == 1 || $scope.cfgScanningCustomRowCol == 2)) {
//                        $scope.currentScanBlock2 = $scope.currentScanBlock2 + 1;
//                        // All cells were scanned. Start again
//                        if ($scope.currentScanBlock2 > $scope.maxScanBlock2) {
//                            $scope.InitScan();
//                            return;
//                        }
//                    }// If we are in the third scan pass one by one over the array (cyclic)
//                    else if ($scope.currentScanBlock === 3) {
//                        $scope.indexScannedCells = $scope.indexScannedCells + 1;
//                        if ($scope.indexScannedCells >= $scope.arrayScannedCells.length) {
//                            $scope.InitScan();
//                        }
//                    }
                }
            };
            //Pass to the next scan level (subgroup)
            $scope.selectBlockScan = function () {
                if ($scope.inScan) {
                    if ($scope.longclick)
                    {
                        $scope.scanLongClickController = false;
                    }
                    if ($scope.isScanningCancel === true) {
                        $scope.InitScan();
                    } else if($scope.isScanning === "goodPhrase"){
                        $scope.feedback(1);
                    } else if($scope.isScanning === "badPhrase"){
                        $scope.feedback(-1);
                    }else {
                        if ($scope.currentScanBlock === 1) {
                            if($scope.timerScan == 1)$scope.isScanningCancel = true;
                            $scope.currentScanBlock = 2;
                            if ($scope.isScanning === "prediction") {
                                $scope.arrayScannedCells = $scope.recommenderArray;
                                $scope.indexScannedCells = 0;
                            } else if ($scope.isScanning === "sentence") {
                                if (JSON.parse(localStorage.getItem('testObject')).cfgMenuReadActive == 1) {
                                    $scope.isScanning = "read";
                                } else if (JSON.parse(localStorage.getItem('testObject')).cfgMenuDeleteLastActive == 1) {
                                    $scope.isScanning = "deletelast";
                                } else if (JSON.parse(localStorage.getItem('testObject')).cfgMenuDeleteAllActive == 1) {
                                    $scope.isScanning = "deleteall";
                                } else {
                                    $scope.InitScan();
                                }
                            } else if ($scope.isScanning === "board") {
                                $scope.getMaxScanBlock2();
                            }
                        } else if ($scope.currentScanBlock === 2) {
                            $scope.currentScanBlock = 3;
                            if ($scope.isScanning === "prediction") {
                                $scope.addToSentence($scope.arrayScannedCells[$scope.indexScannedCells].pictoid);
                            } else if ($scope.isScanning === "read") {
                                $scope.generate();
                                //$scope.InitScan();
                            } else if ($scope.isScanning === "deletelast") {
                                $scope.deleteLast();
                                $scope.InitScan();
                            } else if ($scope.isScanning === "deleteall") {
                                $scope.deleteAll();
                                $scope.InitScan();
                            } else if ($scope.isScanning === "board") {
                                if ($scope.cfgScanningCustomRowCol != 0) {
                                    $scope.selectScannedCell();
                                } else {
                                     if($scope.timerScan == 1) $scope.isScanningCancel = true;
                                    var url = $scope.baseurl + "Board/getScannedCells";
                                    var postdata = {idboard: $scope.idboard, numCustomScanBlock1: $scope.currentScanBlock1, numCustomScanBlock2: $scope.currentScanBlock2};
                                    $http.post(url, postdata).success(function (response)
                                    {
                                        $scope.arrayScannedCells = response.array;
                                        $scope.indexScannedCells = 0;
                                        //There is one cell in that group

                                        if ($scope.arrayScannedCells.length === 1) {
                                            $scope.selectScannedCell();
                                        }
                                    });
                                }
                            }
                        } else if ($scope.currentScanBlock === 3) {
                            $scope.selectScannedCell();
                        }
                    }
                }
            };

            // Select the current cell (the index point to the array with all the cells)
            $scope.selectScannedCell = function ()
            {
                var postdata = {idboard: "", pos: ""};
                var url = $scope.baseurl + "Board/getCell";
                if ($scope.cfgScanningCustomRowCol == 1) {
                    var postdata = {idboard: $scope.idboard, pos: $scope.columns * ($scope.currentScanBlock1 - 1) + $scope.currentScanBlock2};
                } else if ($scope.cfgScanningCustomRowCol == 2) {
                    var postdata = {idboard: $scope.idboard, pos: $scope.columns * ($scope.currentScanBlock2 - 1) + $scope.currentScanBlock1};
                } else {
                    if ($scope.arrayScannedCells === null) {
                        $scope.InitScan();
                        return false;
                    }
                    var postdata = {idboard: $scope.arrayScannedCells[$scope.indexScannedCells].ID_RBoard, pos: $scope.arrayScannedCells[$scope.indexScannedCells].posInBoard};
                }
                $http.post(url, postdata).success(function (response)
                {
                    $scope.clickOnCell(response.info);
                    $scope.InitScan();
                });
            };


            // Get the user config and show the board
            $scope.config = function (boardconf)
            {
                //-----------Iniciacion-----------

                var url = $scope.baseurl + "Board/loadCFG";
                var userConfig = JSON.parse(localStorage.getItem('testObject'));
                var postdata = {idusu: userConfig.ID_User, lusu: userConfig.languageabbr, lusuid: userConfig.cfgDefUser};

                $http.post(url, postdata);
                //MODIF: mirar la board predeterminada 
                $scope.getPrimaryUserBoard();
                $scope.userViewHeight = 100;
                $scope.searchFolderHeight = 0;

                $scope.puntuando = false;
                $scope.tense = "defecte";
                $scope.tipusfrase = "defecte";
                $scope.negativa = false;
                $scope.SearchType = "Tots";
                $scope.inEdit = false;
                $scope.inScan = false;

                //-----------Iniciacion-----------
                // Remove 
                if (boardconf === 1)
                {
                    $scope.showall();
                }
                if (boardconf === 2)
                {
                    $scope.showright();
                }
                if (boardconf === 3)
                {
                    $scope.showleft();
                }
                if (boardconf === 4)
                {
                    $scope.showmid();
                }
                $scope.showup();
                // Remove ^
                // Prediction bar configuration
                $scope.cfgPredOnOff = userConfig.cfgPredOnOff;
                $scope.cfgPredBarVertHor = userConfig.cfgPredBarVertHor;
                $scope.cfgPredBarNumPred = userConfig.cfgPredBarNumPred;
                if ($scope.cfgPredOnOff === '1' && $scope.cfgPredBarVertHor === '0') { // Prediction on and vertical
                    $scope.predViewWidth = 1;
                    $scope.userViewWidth = 11;
                    if (window.innerWidth < 1050) {
                        $scope.predViewWidth = 2;
                        $scope.userViewWidth = 10;
                    }
                }
                // Sentece bar configuration
                $scope.cfgMenuReadActive = userConfig.cfgMenuReadActive;
                $scope.cfgMenuDeleteLastActive = userConfig.cfgMenuDeleteLastActive;
                $scope.cfgMenuDeleteAllActive = userConfig.cfgMenuDeleteAllActive;
                $scope.cfgSentenceBarUpDown = userConfig.cfgSentenceBarUpDown;
                $scope.pictoBarWidth = 12 - $scope.cfgMenuReadActive - $scope.cfgMenuDeleteLastActive - $scope.cfgMenuDeleteAllActive;
                /*$scope.grid1hide = false;
                 $scope.grid2hide = false;
                 $scope.grid3hide = false;
                 $scope.grid1 = 2;
                 $scope.grid2 = 8;
                 $scope.grid3 = 2;*/
                $scope.getPred();
            };

            $scope.getPrimaryUserBoard = function () {
                var url = $scope.baseurl + "Board/getPrimaryUserBoard";

                $http.post(url).success(function (response)
                {
                    $scope.idboard = response.idboard;
                    $scope.showBoard('0');
                });
            };
            /*
             * Return: array fron 0 to repeatnum
             */
            $scope.range = function ($repeatnum)
            {
                var n = [];
                for (i = 0; i < $repeatnum; i++)
                {
                    n.push(i);
                }
                return n;
            };
            /*
             * Change mainboard views (history, prediction...)
             */
            $scope.showall = function ()
            {

            };
            $scope.showright = function ()
            {

            };
            $scope.showleft = function ()
            {

            };
            $scope.showmid = function ()
            {

            };

            $scope.showupdown = function ()
            {

                $scope.sentenceViewHeight = 20;
                $scope.userViewWidth = 12;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 60;
            };
            $scope.showdown = function ()
            {
                $scope.sentenceViewHeight = 0;
                $scope.userViewWidth = 12;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 80;
            };
            $scope.showup = function ()
            {
                $scope.sentenceViewHeight = 16;
                $scope.userViewWidth = 12;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 78;
            };
            $scope.showmiddle = function ()
            {
                $scope.sentenceViewHeight = 0;
                $scope.userViewWidth = 12;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 100;
            };
            /*
             * Show board and the pictograms
             */
            $scope.showBoard = function (id)
            {
                //If the id is 0, show the actual board. Else the current board is changed (and showed)
                if (id === '0') {
                    id = $scope.idboard;
                } else {
                    $scope.idboard = id;
                }
                var url = $scope.baseurl + "Board/showCellboard";
                var postdata = {idboard: id};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.columns = response.col;
                    $scope.rows = response.row;
                    $scope.data = response.data;
                    //                    $scope.oldW = response.col;
                    //                    $scope.oldH = response.row;
                });
            };
            $scope.getPred = function ()
            {
                var url = $scope.baseurl + "Board/getPrediction";
                $http.post(url).success(function (response)
                {
                    $scope.recommenderArray = response.recommenderArray;
                }).error(function (error) {
                    alert(error);
                    alert("error on predictor update building");
                });
            };

            /*
             * Show edit view board
             */
            $scope.edit = function ()
            {
                $scope.getPrimaryBoard();
                $scope.inEdit = true;
                $scope.inScan = false;
                $scope.cfgPredOnOff = 0;
                $scope.predViewWidth = 0;
                $scope.boardHeight = 96;
                $scope.userViewWidth = 9;
                $scope.editViewWidth = 3;
                $scope.userViewHeight = 80;
                $scope.searchFolderHeight = 20;
                if (window.innerWidth < 1050) {
                    $scope.userViewWidth = 8;
                    $scope.editViewWidth = 4;
                }



                var url = $scope.baseurl + "Board/getCellboard";
                var postdata = {idboard: $scope.idboard};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.nameboard = response.name;
                    $scope.altura = $scope.range(20)[response.row].valueOf();
                    $scope.amplada = $scope.range(20)[response.col].valueOf();
                    $scope.autoreturn = (response.autoReturn === '1' ? true : false);
                });
            };
            // Gets all the boards in the group and select the primary
            $scope.getPrimaryBoard = function () {
                var url = $scope.baseurl + "Board/getBoards";
                var postdata = {idboard: $scope.idboard};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.allBoards = response.boards;
                    $scope.primaryBoard = {ID_Board: response.primaryBoard.ID_Board};
                });
            };

            $scope.changeAutoReturn = function (autoreturn)
            {
                var postdata = {id: $scope.idboard, value: autoreturn.valueOf()};
                var URL = $scope.baseurl + "Board/changeAutoReturn";
                $http.post(URL, postdata).
                        success(function ()
                        {

                        });
            };

            // Change the primary board of the group
            $scope.changePrimaryBoard = function (value)
            {
                var postdata = {id: value.ID_Board, idBoard: value.ID_GBBoard};
                var url = $scope.baseurl + "Board/changePrimaryBoard";

                $http.post(url, postdata).success(function (response)
                {

                });
            };
            // Change the shown board
            $scope.changeBoard = function (viewBoard)
            {
                $scope.showBoard(viewBoard.ID_Board);
                // We are in edit mode so update the edit information
                $scope.edit();
            };
            // Change the name board
            $scope.changeNameBoard = function (nameboard, boardindex)
            {
                var postdata = {Name: nameboard, ID: boardindex};
                var URL = $scope.baseurl + "Board/modifyNameboard";
                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.statusWord = response.status;
                            $scope.dataWord = response.data;
                        });
            };

            /*
             * Resize cellboard (height and width)
             */
            $scope.changeSize = function ($newH, $newW, $HW)
            {

                var url = $scope.baseurl + "Board/getCellboard";
                var postdata = {idboard: $scope.idboard};

                $http.post(url, postdata).success(function (response)
                {

                    $scope.oldH = response.row;
                    $scope.oldW = response.col;


                    if ($HW == "1") {
                        $newH = $scope.oldH;
                    } else {
                        $newW = $scope.oldW;
                    }
                    var postdata = {r: $newH, c: $newW, idboard: $scope.idboard};
                    if ($newH < $scope.oldH || $newW < $scope.oldW) {
                        $scope.openConfirmSize($newH, $scope.oldH, $newW, $scope.oldW);
                    } else {

                        var url = $scope.baseurl + "Board/modifyCellBoard";
                        $http.post(url, postdata).then(function ()
                        {
                            $scope.showBoard($scope.idboard);
                        }).error(function ()
                        {

                        });
                        $scope.edit();

                    }
                });
            };
            /*
             * Open a dialog to confirm the new resize (when you resize to a lower size)
             */
            $scope.openConfirmSize = function ($newH, $oldH, $newW, $oldW) {

                var url = $scope.baseurl + "Board/modifyCellBoard";
                var postdata = {r: $newH, c: $newW, idboard: $scope.idboard};
                //Object of all new/old sizes
                $scope.FormData = {
                    newH: $newH,
                    oldH: $oldH,
                    newW: $newW,
                    oldW: $oldW,
                    HWType: 2,
                    Dnum: 0
                };
                if ($newH !== $oldH)
                {
                    $scope.FormData.HWType = 0;
                    $scope.FormData.Dnum = ($oldH - $newH);
                } else if ($newW !== $oldW)
                {
                    $scope.FormData.HWType = 1;
                    $scope.FormData.Dnum = ($oldW - $newW);
                }
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmResize.html',
                    scope: $scope
                }).then(function (value) {
                    //if confirm
                    $http.post(url, postdata).then(function (response) {
                        $scope.showBoard('0');
                    }).error(function (response) {});
                }, function (value) {
                    //if close
                    $scope.showBoard('0');
                });
            };

            /*
             * Add the selected pictogram to the sentence
             */
            $scope.clickOnCell = function (cell) {
                if (!$scope.inEdit) {


                    if (cell.ID_CPicto !== null) {
                        $scope.addToSentence(cell.ID_CPicto);
                    }
                    if (cell.ID_CFunction !== null) {
                        $scope.clickOnFunction(cell.ID_CFunction);
                    }
                    if (cell.boardLink !== null) {
                        $scope.showBoard(cell.boardLink);
                    }
                }
            };
            /*
             * Add the selected pictogram to the sentence
             */
            $scope.addToSentence = function (id) {
                var url = $scope.baseurl + "Board/addWord";
                var postdata = {id: id};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.dataTemp = response.data;
                    $scope.getPred();
                });

                $scope.autoReturn();
            };
            $scope.autoReturn = function () {
                var url = $scope.baseurl + "Board/autoReturn";
                var postdata = {id: $scope.idboard};

                $http.post(url, postdata).success(function (response)
                {

                    if (response.idPrimaryBoard !== null) {
                        $scope.showBoard(response.idPrimaryBoard);
                    }
                });
            };
            /*
             * If you click in a function (not a pictogram) this controller carry you
             * to the specific function
             */
            $scope.clickOnFunction = function (id) {
                var url = $scope.baseurl + "Board/getFunction";
                var postdata = {id: id, tense: $scope.tense, tipusfrase: $scope.tipusfrase, negativa: $scope.negativa};

                $http.post(url, postdata).success(function (response)
                {
                    //MODIF: Falta añadir picto especial
                    $scope.dataTemp = response.data;
                    $scope.tense = response.tense;
                    $scope.tipusfrase = response.tipusfrase;
                    $scope.negativa = response.negativa;
                    if (response.control !== "") {
                        var url = $scope.baseurl + "Board/" + response.control;
                        var postdata = {tense: $scope.tense, tipusfrase: $scope.tipusfrase, negativa: $scope.negativa};

                        $http.post(url, postdata).success(function (response)
                        {
                            if (response.control === "generate") {
                                $scope.dataTemp = response.data;
                            }
                            $scope.info = response.info;
                        });
                    }
                    $scope.autoReturn();
                });
            };
            /*
             * Remove last word added to the sentence
             */
            $scope.deleteLast = function () {

                var url = $scope.baseurl + "Board/deleteLastWord";

                $http.post(url).success(function (response)
                {
                    $scope.dataTemp = response.data;
                    $scope.getPred();
                });
            };
            /*
             * Remove the whole sentence
             */
            $scope.deleteAll = function () {

                var url = $scope.baseurl + "Board/deleteAllWords";

                $http.post(url).success(function (response)
                {
                    $scope.dataTemp = response.data;
                    $scope.getPred();
                });
            };
            /*
             * Generate the current senence under contruction.
             * Add the pictograms (and the sentence itself) in the history
             */

            $scope.generate = function () {

                var url = $scope.baseurl + "Board/generate";
                var postdata = {tense: $scope.tense, tipusfrase: $scope.tipusfrase, negativa: $scope.negativa};
                $http.post(url, postdata).success(function (response)
                {
                    console.log(response);
                    //$scope.dataTemp = response.data;
                    $scope.info = response.info;
                    //$scope.data = response.data;
                    $scope.playSentenceAudio();
                    $scope.puntuar();

                    
                });
                $scope.tense = "defecte";
                $scope.tipusfrase = "defecte";
                $scope.negativa = false;


            };
            
            $scope.puntuar = function () {

                $scope.puntuando = true;
                if ($scope.inScan){
                    $scope.isScanning = "goodPhrase";
                }


            };
            $scope.feedback = function (point) {

                if(point === 1){
                    alert("one point for Spain");
                }else{
                    alert("Gracias por puntuar bien esta frase");
                }
                $scope.puntuando = false;
                if($scope.inScan){
                    $scope.InitScan();
                }


            };

            $scope.playSentenceAudio = function ()
            {
                var postdata = {voice: 0, sentence: $scope.info.frasefinal};
                var URL = $scope.baseurl + "Board/getAudioSentence";

                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.dataAudio = response.data;

                            alert($scope.dataAudio);
                            $scope.sound = ngAudio.load($scope.baseurl + $scope.dataAudio);
                            $scope.sound.play();

                        });
            };
            /*
             * Return pictograms from database. The result depends on 
             * Searchtype (noms, verbs...) and Name (letters with the word start with)
             */
            $scope.search = function (name, Searchtype)
            {
                var URL = "";
                var postdata = {id: name};
                //Radio button function parameter, to set search type
                switch (Searchtype)
                {
                    case "Tots":
                        URL = $scope.baseurl + "SearchWord/getDBAll";
                        break;
                    case "Noms":
                        URL = $scope.baseurl + "SearchWord/getDBNames";
                        break;
                    case "Verb":
                        URL = $scope.baseurl + "SearchWord/getDBVerbs";
                        break;
                    case "Adj":
                        URL = $scope.baseurl + "SearchWord/getDBAdj";
                        break;
                    case "Exp":
                        URL = $scope.baseurl + "SearchWord/getDBExprs";
                        break;
                    case "Altres":
                        URL = $scope.baseurl + "SearchWord/getDBOthers";
                        break;
                    default:
                        URL = $scope.baseurl + "SearchWord/getDBAll";
                }




                //Request via post to controller search data from database
                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.dataWord = response.data;
                        });
            };
            //Dragndrop events
            $scope.centerAnchor = true;
            $scope.toggleCenterAnchor = function () {
                $scope.centerAnchor = !$scope.centerAnchor;
            };
            var onDraggableEvent = function (evt, data) {
                //console.log("128", "onDraggableEvent", evt, data);
                if (evt.name === "draggable:start") {
                    $scope.hide = false;
                } else if (evt.name === "draggable:end") {
                    $scope.hide = true;
                }
            };
            $scope.$on('draggable:start', onDraggableEvent);
            // $scope.$on('draggable:move', onDraggableEvent);
            $scope.$on('draggable:end', onDraggableEvent);
            $scope.onDropSwap = function (posInBoard, data, evt) {
                var URL = "";
                //Significa que no hay que hacer swap, solo medio swap...
                if (data.idpicto) {
                    URL = $scope.baseurl + "Board/addPicto";
                    var postdata = {id: data.idpicto, pos: posInBoard, idboard: $scope.idboard};
                } else {
                    var postdata = {pos1: data.posInBoardPicto, pos2: posInBoard, idboard: $scope.idboard};
                    URL = $scope.baseurl + "Board/swapPicto";
                }

                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.statusWord = response.status;
                            $scope.data = response.data;
                        });
            };
            $scope.onDropRemove = function (data, evt) {

                var postdata = {pos: data.posInBoardPicto, idboard: $scope.idboard};
                var URL = $scope.baseurl + "Board/removePicto";


                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.statusWord = response.status;
                            $scope.data = response.data;
                        });
            };

            /*
             * Open edit cell dialog and asign the controller
             */
            $scope.openEditCellMenu = function (id) {
                if ($scope.inEdit) {
                    $scope.idEditCell = id;
                    ngDialog.open({
                        template: $scope.baseurl + '/angular_templates/EditCellView.html',
                        className: 'ngdialog-theme-default dialogEdit',
                        scope: $scope,
                        controller: 'Edit'
                    });

                }
                ;
            };

            /***************************************************
             *
             *  editFolders functions
             *  
             ***************************************************/
            $scope.CreateBoard = function () {
                $scope.CreateBoardData = {CreateBoardName: '', height: 0, width: 0, idGroupBoard: 0};
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmCreateBoard.html',
                    scope: $scope,
                    className: 'ngdialog-theme-default dialogCreateBoard'
                }).then(function () {
                    var postdata = {id: $scope.idboard};
                    var URL = $scope.baseurl + "Board/getIDGroupBoards"

                    $http.post(URL, postdata).success(function (response)
                    {
                        $scope.CreateBoardData.idGroupBoard = response.idGroupBoard;

                        URL = $scope.baseurl + "Board/newBoard"


                        $http.post(URL, $scope.CreateBoardData).success(function (response)
                        {
                            $scope.showBoard(response.idBoard)
                            $scope.edit();
                        });
                    });

                }, function (value) {
                });

            };
            $scope.RemoveBoard = function () {
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmRemoveBoard.html',
                    scope: $scope,
                    className: 'ngdialog-theme-default dialogRemoveBoard'
                }).then(function () {
                    var postdata = {id: $scope.idboard};
                    var URL = $scope.baseurl + "Board/removeBoard"

                    $http.post(URL, postdata).success(function (response)
                    {

                    });
                }, function (value) {
                });

            };


            $scope.copyBoard = function () {
                //MODIF: Se tiene que cojer los datos de la board i enviarlos por la siguiente linia
                $scope.CopyBoardData = {CreateBoardName: '', height: 0, width: 0, idGroupBoard: 0};
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmCopyBoard.html',
                    scope: $scope,
                    className: 'ngdialog-theme-default dialogCopyBoard'
                }).then(function () {

                }, function (value) {
                });
            };
            $scope.moveBoard = function () {
                //MODIF: Se tiene que cojer los datos de la board i enviarlos por la siguiente linia
                $scope.MoveBoardData = {CreateBoardName: '', height: 0, width: 0, idGroupBoard: 0};
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmMoveBoard.html',
                    scope: $scope,
                    className: 'ngdialog-theme-default dialogMoveBoard'
                }).then(function () {

                }, function (value) {
                });
            };
        })

        // Edit controller 
        .controller('Edit', function ($scope, $http, ngDialog) {
            // Get the cell clicked (the cell in the cicked position in the current board
            var url = $scope.baseurl + "Board/getCell";
            var postdata = {pos: $scope.idEditCell, idboard: $scope.idboard};

            $http.post(url, postdata).success(function (response)
            {
                $scope.Editinfo = response.info;
                var idCell = response.info.ID_RCell;

                $scope.uploadFile = function () {
                    var file = $scope.myFile;
                    console.log('file is ');
                    console.dir(file);
                    var uploadUrl = $scope.baseurl + "/ImgUploader/upload";
                    var fd = new FormData();
                    fd.append('file', file);
                    $http.post(uploadUrl, fd, {
                        headers: {'Content-Type': undefined}
                    })
                            .success(function () {
                            })
                            .catch(function (response) {
                                var a = response.errorText;
                                alert(response);
                            });
                };

                // Gets functions from database and shows them the dropmenu
                $scope.getFunctions = function () {
                    var url = $scope.baseurl + "Board/getFunctions";
                    $http.post(url).success(function (response)
                    {
                        $scope.functions = response.functions;
                        //Inicializa el dropdown menu
                        $scope.funcType = {ID_Function: $scope.Editinfo.ID_CFunction};
                        if ($scope.Editinfo.ID_CFunction !== null) {
                            $scope.checkboxFuncType = true;
                        }
                    });
                };
                // Gets all boards in the same group and shows them the dropmenu
                $scope.getBoards = function () {
                    var url = $scope.baseurl + "Board/getBoards";
                    var postdata = {idboard: $scope.idboard};

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.boards = response.boards;
                        $scope.boardsGroup = {ID_Board: $scope.Editinfo.boardLink};
                        if ($scope.Editinfo.boardLink !== null) {
                            $scope.checkboxBoardsGroup = true;
                        }
                    });
                };
                // Gets the sentence asigned (if there is any) to the cell and show it to the user
                $scope.getSentence = function (id) {
                    var url = $scope.baseurl + "Board/getSentence";
                    var postdata = {id: id};

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.sentenceSelectedId = response.sentence.ID_SSentence;
                        $scope.sentenceSelectedText = response.sentence.generatorString;
                    });
                };
                // Gets all pre-record sentences from database and shows it the dropmenu
                $scope.searchSentece = function (sentence) {
                    var postdata = {search: sentence};
                    var URL = $scope.baseurl + "Board/searchSentence";

                    $http.post(URL, postdata).
                            success(function (response)
                            {
                                $scope.sentenceResult = response.sentence;
                            });
                };
                // Asigns the selected sentence to the cell (provisionally) and show it to the user
                $scope.selectSentence = function (id, text) {
                    $scope.sentenceSelectedId = id;
                    $scope.sentenceSelectedText = text;
                };
                // Gets the sentence folder asigned (if there is any) to the cell and shows it to the user
                $scope.getSFolder = function (id) {
                    var url = $scope.baseurl + "Board/getSFolder";
                    var postdata = {id: id};

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.sFolderSelectedId = response.sFolder.ID_Folder;
                        $scope.sFolderSelectedImg = response.sFolder.imgSFolder;
                        $scope.sFolderSelectedText = response.sFolder.folderName;
                    });
                };
                // Gets all sentence folders from database and shows it the dropmenu
                $scope.searchSFolder = function (sFolder) {
                    var postdata = {search: sFolder};
                    var URL = $scope.baseurl + "Board/searchSFolder";

                    $http.post(URL, postdata).
                            success(function (response)
                            {
                                $scope.sFolderResult = response.sfolder;
                            });
                };
                // Asigns the selected sentence folder to the cell (provisionally) and show it to the user
                $scope.selectSFolder = function (id, img, text) {
                    $scope.sFolderSelectedId = id;
                    $scope.sFolderSelectedImg = img;
                    $scope.sFolderSelectedText = text;
                };
                // Asigns the selected pictograma to the cell (provisionally) and show it to the user
                $scope.selectPicto = function (id, img) {
                    $scope.idPictoEdit = id;
                    $scope.imgPictoEdit = img;
                };
                //Initialize the dropdwon menus and all the variables that will be shown to the user
                $scope.getFunctions();
                $scope.getBoards();
                $scope.colorSelected = response.info.color;
                $scope.cellType = response.info.cellType;
                $scope.numScanBlockText1 = $scope.range(20)[$scope.Editinfo.customScanBlock1];
                $scope.textInScanBlockText1 = $scope.Editinfo.customScanBlockText1;
                $scope.numScanBlockText2 = $scope.range(20)[$scope.Editinfo.customScanBlock2];
                $scope.textInScanBlockText2 = $scope.Editinfo.customScanBlockText2;
                $scope.idPictoEdit = response.info.ID_CPicto;
                $scope.imgPictoEdit = $scope.Editinfo.imgPicto;
                // Check the values in order to active checkbox and this stuff
                if ($scope.Editinfo.textInCell !== null) {
                    $scope.checkboxTextInCell = true;
                    $scope.textInCell = $scope.Editinfo.textInCell;
                }
                if ($scope.Editinfo.activeCell === "1") {
                    $scope.checkboxVisible = true;
                }
                if ($scope.Editinfo.isFixedInGroupBoards === "1") {
                    $scope.checkboxIsFixed = true;
                }
                if ($scope.Editinfo.customScanBlockText1 !== "") {
                    $scope.checkboxScanBlockText1 = true;
                }
                if ($scope.Editinfo.customScanBlock2 !== null) {
                    $scope.checkboxScanBlockText2 = true;
                }
                if (response.info.cellType === 'sentence') {
                    $scope.getSentence(response.info.ID_CSentence);
                }
                if (response.info.cellType === 'sfolder') {
                    $scope.getSFolder(response.info.sentenceFolder);
                }
                // When confirm is clicked, save all the provisionally data asigned to the cell
                $scope.aceptar = function () {
                    var url = $scope.baseurl + "Board/editCell";
                    var postdata = {id: idCell, idPicto: $scope.idPictoEdit, idSentence: $scope.sentenceSelectedId, idSFolder: $scope.sFolderSelectedId, boardLink: $scope.boardsGroup.ID_Board, idFunct: $scope.funcType.ID_Function, textInCell: $scope.textInCell, visible: "1", isFixed: "1", numScanBlockText1: $scope.numScanBlockText1, textInScanBlockText1: $scope.textInScanBlockText1, numScanBlockText2: $scope.numScanBlockText2, textInScanBlockText2: $scope.textInScanBlockText2, cellType: $scope.cellType, color: $scope.colorSelected};
                    // Check another time null values and config the data that will be save in the data base
                    if (!$scope.checkboxFuncType || ($scope.cellType === 'link')) {
                        postdata.idFunct = null;
                    }
                    if (!$scope.checkboxBoardsGroup || ($scope.cellType === 'funct')) {
                        postdata.boardLink = null;
                    }
                    if (!$scope.checkboxTextInCell) {
                        postdata.textInCell = null;
                    }
                    if (!$scope.checkboxVisible) {
                        postdata.visible = "0";
                    }
                    if (!$scope.checkboxIsFixed) {
                        postdata.isFixed = "0";
                    }
                    if (!$scope.checkboxScanBlockText1) {
                        postdata.numScanBlockText1 = "1";
                        postdata.textInScanBlockText1 = null;
                    }
                    if (!$scope.checkboxScanBlockText2) {
                        postdata.numScanBlockText2 = null;
                        postdata.textInScanBlockText2 = null;
                    }
                    if ($scope.cellType !== 'picto') {
                        postdata.idPicto = null;
                    }
                    if ($scope.cellType !== 'sentence') {
                        postdata.idSentence = null;
                    }
                    if ($scope.cellType !== 'sfolder') {
                        postdata.idSFolder = null;
                    }


                    $http.post(url, postdata).success(function ()
                    {
                        $scope.showBoard("0");
                        ngDialog.close();
                    });
                };
            }
            );
        })

        .controller('menuCtrl', function ($scope, $http, ngDialog, txtContent, $rootScope, AuthService, $location) {
            $scope.userConfig = function () {
                $location.path('/userConfig');
            }
            $scope.editMenu = function () {
                $rootScope.$emit("EditCallFromMenu", {});
            };
            // Función salir del login
            $scope.logOut = function () {
                ngDialog.openConfirm({
                    template: $scope.baseurl + '/angular_templates/ConfirmLogout.html',
                    scope: $scope,
                    className: 'ngdialog-theme-default dialogLogOut'
                }).then(function () {
                    AuthService.logout();
                    $location.path('/login');
                }, function (value) {

                });

            };


            $scope.home = function () {
                $rootScope.$emit("IniciCallFromMenu", {});

            };

            $scope.IniciScan = function () {
                $rootScope.$emit("ScanCallFromMenu", {});
            };
        })





//Add a directive in order to recognize the right click
        .directive('ngRightClick', function ($parse) {
            return function (scope, element, attrs) {
                var fn = $parse(attrs.ngRightClick);
                element.bind('contextmenu', function (event) {
                    scope.$apply(function () {
                        event.preventDefault();
                        fn(scope, {$event: event});
                    });
                });
            };
        })

//Add a directive to link bootstrap switch with angular
        .directive('bootstrapSwitch', [
            function () {
                return {
                    restrict: 'A',
                    require: '?ngModel',
                    link: function (scope, element, attrs, ngModel) {
                        element.bootstrapSwitch();

                        element.on('switchChange.bootstrapSwitch', function (event, state) {
                            if (ngModel) {
                                scope.$apply(function () {
                                    ngModel.$setViewValue(state);
                                });
                            }
                        });

                        scope.$watch(attrs.ngModel, function (newValue, oldValue) {
                            if (newValue) {
                                element.bootstrapSwitch('state', true, true);
                            } else {
                                element.bootstrapSwitch('state', false, true);
                            }
                        });
                    }
                };
            }
        ])
        .directive('fileModel', ['$parse', function ($parse) {
                return {
                    restrict: 'A',
                    link: function (scope, element, attrs) {
                        var model = $parse(attrs.fileModel);
                        var modelSetter = model.assign;

                        element.bind('change', function () {
                            scope.$apply(function () {
                                modelSetter(scope, element[0].files[0]);
                            });
                        });
                    }
                };
            }]);

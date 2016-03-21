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
            $location.path('/');
        })
        .catch(function(error){	// no respuesta
            $scope.state = 'has-error';
            console.log(error);
        });
    };
    // Cambiar estados del formulario
    $scope.changeFormSate=function(){
            $scope.state = '';
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
    var currentLanguage = 1; // idioma por defecto al iniciar (catalan)
    //        $scope.sound = ngAudio.load("mp3/sound.mp3");
        
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
    
    //Genero de la aplicación (Masculino/femenino)
    $scope.sex=function(sex){
        if(sex=='female'){
            $scope.state.female ='has-success';
            $scope.state.male='';
            $scope.formData.cfgIsFem = '1';
            return true;
        }else if(sex=='male'){
            $scope.state.female ='';
            $scope.state.male='has-success';
            $scope.formData.cfgIsFem = '0';
            return true;
        }
        console.log($scope.formData);
        if(sex.cfgIsFem == null || sex.cfgIsFem ==''){
            $scope.state.female ='has-error';
            $scope.state.male ='has-error';
            return false;
        }else{
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
        if ($scope.languageList.length==0){
            $scope.state.languageSelected = 'has-error';
            languageOk=false;
        }
        // Comprobamos todos los campos del formulario accediendo a las funciones o mirando las variables de estado
        if (userOk&&$scope.checkPassword(formData)&&$scope.checkName(formData)&&$scope.checkLastname(formData)&&emailOk&&languageOk&&$scope.sex(formData)) {
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

// Controlador de prueba

.controller('AdeuCtrl', function ($rootScope, $scope, $location) {
            if (!$rootScope.isLogged) {
                $location.path('/login');
    } else {
        $scope.goodbye = "Adeu!!";
    }
})

.controller('myCtrl', function ($location, $scope, $http, ngDialog, txtContent, $rootScope) {
            // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
    if (!$rootScope.isLogged) {
        $location.path('/login');
    }
    // Pedimos los textos para cargar la pagina
    txtContent("mainboard").then(function (results) {
        $rootScope.content = results.data;
    });

    $rootScope.$on("EditCallFromMenu", function () {
        $scope.edit();
    });

    $scope.config = function (boardconf)
    {
        //-----------Iniciacion-----------
        var url = $scope.baseurl + "Board/loadCFG";
        var postdata = {idusu: window.localStorage.getItem('userid'), lusu: window.localStorage.getItem('languageabbr')};

        $http.post(url, postdata);
        //MODIF: mirar la board predeterminada 

        $scope.idboard = "1";
        $scope.tense = "defecte";
        $scope.tipusfrase = "defecte";
        $scope.negativa = false;
        $scope.SearchType = "Tots";
        $scope.inEdit = false;
        //-----------Iniciacion-----------

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
        /*$scope.grid1hide = false;
                 $scope.grid2hide = false;
                 $scope.grid3hide = false;
                 $scope.grid1 = 2;
                 $scope.grid2 = 8;
                 $scope.grid3 = 2;*/
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
        $scope.grid1hide = false;
        $scope.grid2hide = false;
        $scope.grid3hide = false;
        $scope.grid1 = 2;
        $scope.grid2 = 8;
        $scope.grid3 = 2;
    };
    $scope.showright = function ()
    {
        $scope.grid1hide = true;
        $scope.grid2hide = false;
        $scope.grid3hide = false;
        $scope.grid1 = 0;
        $scope.grid2 = 10;
        $scope.grid3 = 2;
    };
    $scope.showleft = function ()
    {
        $scope.grid1hide = false;
        $scope.grid2hide = false;
        $scope.grid3hide = true;
        $scope.grid1 = 2;
        $scope.grid2 = 10;
        $scope.grid3 = 0;
    };
    $scope.showmid = function ()
    {
        $scope.grid1hide = true;
        $scope.grid2hide = false;
        $scope.grid3hide = true;
        $scope.grid1 = 0;
        $scope.grid2 = 12;
        $scope.grid3 = 0;
    };

    $scope.showupdown = function ()
    {
        $scope.subgrid1hide = false;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = false;
        $scope.subgrid1 = 20;
        $scope.subgrid2 = 60;
        $scope.subgrid3 = 20;
    };
    $scope.showdown = function ()
    {
        $scope.subgrid1hide = true;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = false;
        $scope.subgrid1 = 0;
        $scope.subgrid2 = 80;
        $scope.subgrid3 = 20;
    };
    $scope.showup = function ()
    {
        $scope.subgrid1hide = false;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = true;
        $scope.subgrid1 = 16;
        $scope.subgrid2 = 78;
        $scope.subgrid3 = 0;
    };
    $scope.showmiddle = function ()
    {
        $scope.subgrid1hide = true;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = true;
        $scope.subgrid1 = 0;
        $scope.subgrid2 = 100;
        $scope.subgrid3 = 0;
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

    /*
     * Show edit view board
     */
    $scope.edit = function ()
    {
        $scope.getPrimaryBoard();
        $scope.inEdit = true;
        $scope.grid1hide = true;
        $scope.grid2hide = false;
        $scope.grid3hide = false;
        $scope.grid1 = 0;
        $scope.grid2 = 9;
        $scope.grid3 = 3;
        if (window.innerWidth < 1050) {
            $scope.grid2 = 8;
            $scope.grid3 = 4;
        }


        var url = $scope.baseurl + "Board/getCellboard";
        var postdata = {idboard: $scope.idboard};

        $http.post(url, postdata).success(function (response)
        {
            $scope.nameboard = response.name;
            $scope.altura = $scope.range(20)[response.row].valueOf();
            $scope.amplada = $scope.range(20)[response.col].valueOf();
        });
    };

    $scope.getPrimaryBoard = function () {
        var url = $scope.baseurl + "Board/getBoards";
        var postdata = {idboard: $scope.idboard};

        $http.post(url, postdata).success(function (response)
        {
            $scope.allBoards = response.boards;
            $scope.primaryBoard = {ID_Board: response.primaryBoard.ID_Board};
        });
    };


    $scope.changePrimaryBoard = function (value)
    {
        var postdata = {id: value.ID_Board, idBoard: value.ID_GBBoard};
        var url = $scope.baseurl + "Board/changePrimaryBoard";

        $http.post(url, postdata).success(function (response)
        {

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

    $scope.changeNameBoard = function ()
    {
        var postdata = {Name: $scope.BoardName};
        var URL = $scope.baseurl + "Board/modifyNameboard";

        $http.post(URL, postdata).
                success(function (response)
        {
            $scope.statusWord = response.status;
            $scope.dataWord = response.data;
        });
    };

    $scope.changeColor = function (color) {
        $scope.colorSelected = color;
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
                    $scope.showBoard('0');
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


        if (cell.ID_CPicto !== null) {
            $scope.addToSentence(cell.ID_CPicto);
        }
        if (cell.ID_CFunction !== null) {
            $scope.clickOnFunction(cell.ID_CFunction);
        }
        if (cell.boardLink !== null) {
            $scope.showBoard(cell.boardLink);
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
        });

        var url = $scope.baseurl + "Board/autoReturn";
        var postdata = {id: $scope.idboard};

        $http.post(url, postdata).success(function (response)
        {

            if (response.idPrimaryBoard !== null) {
                alert("he entrado" + response.idPrimaryBoard);
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
            $scope.tense = response.tense;
            $scope.tipusfrase = response.tipusfrase;
            $scope.negativa = response.negativa;
            if (response.control !== "") {
                var url = $scope.baseurl + "Board/" + response.control;
                var postdata = {id: id, tense: $scope.tense, tipusfrase: $scope.tipusfrase, negativa: $scope.negativa};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.dataTemp = response.data;
                    $scope.info = response.info;
                });
            }
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
        });
        $scope.tense = "defecte";
        $scope.tipusfrase = "defecte";
        $scope.negativa = false;
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
     * Open edit cell dialog
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
})


        .controller('Edit', function ($scope, $http, ngDialog, txtContent, $rootScope) {

            var url = $scope.baseurl + "Board/getCell";
    var postdata = {id: $scope.idEditCell, idboard: $scope.idboard};

    $http.post(url, postdata).success(function (response)
    {
        $scope.Editinfo = response.info;
        var idCell = response.info.ID_RCell;



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
        $scope.getSentence = function (id) {
            var url = $scope.baseurl + "Board/getSentence";
            var postdata = {id: id};

            $http.post(url, postdata).success(function (response)
            {
                $scope.sentenceSelectedId = response.sentence.ID_SSentence;
                $scope.sentenceSelectedText = response.sentence.generatorString;
            });
        };
        $scope.searchSentece = function (sentence) {
            var postdata = {search: sentence};
            var URL = $scope.baseurl + "Board/searchSentence";

            $http.post(URL, postdata).
                    success(function (response)
            {
                $scope.sentenceResult = response.sentence;
            });
        };
        $scope.selectSentence = function (id, text) {
            $scope.sentenceSelectedId = id;
            $scope.sentenceSelectedText = text;
        };

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
        $scope.searchSFolder = function (sFolder) {
            var postdata = {search: sFolder};
            var URL = $scope.baseurl + "Board/searchSFolder";

            $http.post(URL, postdata).
                    success(function (response)
            {
                $scope.sFolderResult = response.sfolder;
            });
        };
        $scope.selectSFolder = function (id, img, text) {
            $scope.sFolderSelectedId = id;
            $scope.sFolderSelectedImg = img;
            $scope.sFolderSelectedText = text;
        };
        //Initialize the dropdwon menus.
        $scope.getFunctions();
        $scope.getBoards();
        $scope.cellType = response.info.cellType;
        $scope.numScanBlockText1 = $scope.range(20)[$scope.Editinfo.customScanBlock1];
        $scope.textInScanBlockText1 = $scope.Editinfo.customScanBlockText1;
        $scope.numScanBlockText2 = $scope.range(20)[$scope.Editinfo.customScanBlock2];
        $scope.textInScanBlockText2 = $scope.Editinfo.customScanBlockText2;
        $scope.idPictoEdit = response.info.ID_CPicto;
        $scope.imgPictoEdit = $scope.Editinfo.imgPicto;

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
        if ($scope.Editinfo.customScanBlockText2 !== null) {
            $scope.checkboxScanBlockText2 = true;
        }
        if (response.info.cellType === 'sentence') {
            $scope.getSentence(response.info.ID_CSentence);
        }
        if (response.info.cellType === 'sfolder') {
            $scope.getSFolder(response.info.sentenceFolder);
        }

        $scope.aceptar = function () {
            var url = $scope.baseurl + "Board/editCell";
            var postdata = {id: idCell, idPicto: $scope.idPictoEdit, idSentence: $scope.sentenceSelectedId, idSFolder: $scope.sFolderSelectedId, boardLink: $scope.boardsGroup.ID_Board, idFunct: $scope.funcType.ID_Function, textInCell: $scope.textInCell, visible: "1", isFixed: "1", numScanBlockText1: $scope.numScanBlockText1, textInScanBlockText1: $scope.textInScanBlockText1, numScanBlockText2: $scope.numScanBlockText2, textInScanBlockText2: $scope.textInScanBlockText2, cellType: $scope.cellType};
            if (!$scope.checkboxFuncType) {
                postdata.idFunct = null;
            }
            if (!$scope.checkboxBoardsGroup) {
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
        $scope.selectPicto = function (id, img) {
            $scope.idPictoEdit = id;
            $scope.imgPictoEdit = img;
        };


    }
            );
})

        .controller('menuCtrl', function ($scope, $http, ngDialog, txtContent, $rootScope) {
            $scope.editMenu = function () {
                alert("hola");
        $rootScope.$emit("EditCallFromMenu", {});
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
]);

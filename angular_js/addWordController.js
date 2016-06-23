angular.module('controllers')
        .controller('addWordCtrl', function ($scope, $rootScope, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
            txtContent("addWord").then(function (results) {
                $scope.content = results.data;
                $scope.initAddWordtest();
            });
            //Dropdown Menu Bar
            dropdownMenuBarInit($rootScope.contetnLanguageUserNonLoged)
                    .then(function () {
                        $rootScope.dropdownMenuBarChangeLanguage = true;//Languages button available
                        //Choose the buttons to show on bar
                        angular.forEach($rootScope.dropdownMenuBar, function (value) {
                            if (value.href == '/' || value.href == '/faq' || value.href == '/tutorial' || value.href == '/privacity') {
                                value.show = true;
                            } else {
                                value.show = false;
                            }
                        });
                    });
            $rootScope.dropdownMenuBarValue = '/'; //Button selected on this view
            $rootScope.dropdownMenuBarButtonHide = false;
            //function to change html view
            $scope.go = function (path) {
                $location.path(path);
                $rootScope.dropdownMenuBarValue = path; //Button selected on this view
            };
            //function to change html content language
            $rootScope.changeLanguage = function (value) {
                $rootScope.contetnLanguageUserNonLoged = value;
                Resources.register.get({'section': 'login', 'idLanguage': value}, {'funct': "content"}).$promise
                        .then(function (results) {
                            $scope.content = results.data;
                            dropdownMenuBarInit(value);
                        });
            };
            $scope.initAddWord = function () {};
            $scope.initAddWordtest = function () {

                if ($rootScope.addWordparam != null) {
                    $scope.NewModif = $rootScope.addWordparam.newmod;
                    $scope.addWordType = $rootScope.addWordparam.type;
                    $rootScope.addWordparam = null;
                    console.log($scope.addWordType);
                } else {
                    $location.path("/panelGroups");
                }

                if ($scope.NewModif == 1) {
                    switch ($scope.addWordType)
                    {
                        case "name":
                            $scope.objAdd = {type: "name", nomtext: null, mf: false, singpl: false, contabincontab: null, determinat: null, ispropernoun: false, defaultverb: null, plural: null, femeni: null, fempl: null};
                            $scope.switchName = {s1: false, s2: false, s3: false, s4: false, s5: false, s6: false};
                            $scope.NClassList = [];
                            $scope.classNoun = [{classType: "animate", numType: 1, nameType: $scope.content.classname1},
                                {classType: "human", numType: 2, nameType: $scope.content.classname2},
                                {classType: "pronoun", numType: 3, nameType: $scope.content.classname3},
                                {classType: "animal", numType: 4, nameType: $scope.content.classname4},
                                {classType: "planta", numType: 5, nameType: $scope.content.classname5},
                                {classType: "vehicle", numType: 6, nameType: $scope.content.classname6},
                                {classType: "event", numType: 7, nameType: $scope.content.classname7},
                                {classType: "inanimate", numType: 8, nameType: $scope.content.classname8},
                                {classType: "objecte", numType: 9, nameType: $scope.content.classname9},
                                {classType: "color", numType: 10, nameType: $scope.content.classname10},
                                {classType: "forma", numType: 11, nameType: $scope.content.classname11},
                                {classType: "joc", numType: 12, nameType: $scope.content.classname12},
                                {classType: "cos", numType: 13, nameType: $scope.content.classname13},
                                {classType: "abstracte", numType: 14, nameType: $scope.content.classname14},
                                {classType: "lloc", numType: 15, nameType: $scope.content.classname15},
                                {classType: "menjar", numType: 16, nameType: $scope.content.classname16},
                                {classType: "beguda", numType: 17, nameType: $scope.content.classname17},
                                {classType: "time", numType: 18, nameType: $scope.content.classname18},
                                {classType: "hora", numType: 19, nameType: $scope.content.classname19},
                                {classType: "month", numType: 20, nameType: $scope.content.classname20},
                                {classType: "week", numType: 21, nameType: $scope.content.classname21},
                                {classType: "tool", numType: 22, nameType: $scope.content.classname22},
                                {classType: "profession", numType: 23, nameType: $scope.content.classname23},
                                {classType: "material", numType: 24, nameType: $scope.content.classname24}];
                            $scope.$apply();
                            break;
                        case "adj":
                            //MODIF: defaultverb: 100/86, subjdef: 1/3 per defecte
                            $scope.objAdd = {type: "adj", masc: null, fem: null, mascpl: null, fempl: null,defaultverb: false, subjdef: false};
                            $scope.switchAdj = {s1: false, s2: false, s3: false, s4: false, s5: false, s6: false};
                            break;
                        default:
                            break;
                    }
                } else {
                    //MODIF: Coger BBDD los valores de los objetos
                    //MODIF: Llenar los valores de cada uno de ellos
                    var URL = $scope.baseurl + "Board/XXXXXXXX";
//                      MODIF: HA DE GUARDAR LES DADES DEL POST
//                      $http.post(URL, objAdd).success(function (response)
//                      {                               
//                      });
                    switch ($scope.addWordType)
                    {
                        case "name":
                            $scope.objAdd = {type: "name", nomtext: null, mf: false, singpl: false, contabincontab: null, determinat: null, ispropernoun: null, defaultverb: null, plural: null, femeni: null, fempl: null};
                            $scope.switchName = {s1: false, s2: false, s3: false, s4: false, s5: false, s6: false};
                            break;
                        case "adj":
                            $scope.objAdd = {type: "adj", masc: null, fem: null, mascpl: null, fempl: null, subjdef: null};
                            break;
                        default:
                            break;
                    }
                }
            };
            $scope.cancelAddWord = function () {
                $location.path("/panelGroups");
            };


            $scope.saveAddWord = function () {
                var URL = $scope.baseurl + "Board/XXXXXXXX";
                console.log($scope.objAdd);
//      MODIF: HA DE ENVIAR LES DADES AL POST
//                $http.post(URL, objAdd).success(function (response)
//                {
//                });

                $location.path("/panelGroups");
            };
            $scope.uploadFileToWord = function () {
                $scope.myFile = document.getElementById('file-input').files;
                $scope.uploading = true;
                var i;
                var uploadUrl = $scope.baseurl + "ImgUploader/upload";
                var fd = new FormData();
                for (i = 0; i < $scope.myFile.length; i++) {
                    fd.append('file' + i, $scope.myFile[i]);
                }
                $http.post(uploadUrl, fd, {
                    headers: {'Content-Type': undefined}
                })
                        .success(function (response) {
                            $scope.uploading = false;
                            $scope.URLImg = response.url;
                            if (response.error) {
                                //open modal
                                console.log(response.errorText);
                                $scope.errorText = response.errorText;
                                $('#errorImgModal').modal({backdrop: 'static'});
                            }
                        })
                        .error(function (response) {
                            //alert(response.errorText);
                        });
            };
            $scope.addNClass = function (nameTypeClass) {
                angular.forEach($scope.classNoun, function (value, key) {
                    if (value.classType == nameTypeClass) {
                        $scope.NClassList.push($scope.classNoun[key]);//añadimos el idioma a la lista .push(objeto)
                        $scope.classNoun.splice(key, 1);//Borrar idioma de las opciones .splice(posicion, numero de items)
                        $scope.state.languageSelected = 'has-success';
                        languageOk = true;
                    }
                });
            };
            $scope.removeLanguage = function (index) {
                $scope.classNoun.push($scope.NClassList[index]);
                $scope.NClassList.splice(index, 1);//Borrar item de un array .splice(posicion, numero de items)
            };
























        });
        
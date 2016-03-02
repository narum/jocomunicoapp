angular.module('controllers', [])

// Controlador del Login

        .controller('LoginCtrl', function ($scope, Resources, $location, AuthService) {

            var loginResource = Resources.login;

            // Función que coje el user y pass y comprueba que sean correctos
            $scope.login = function (form) {
                var body = {
                    user: $scope.username,
                    pass: $scope.password
                };

                // Petición del login
                loginResource.save(body).$promise  // POST (en angular 'save') del user y pass
                        .then(function (result) {				// respuesta ok!
                            var token = result.data.token;
                            var languageid = result.data.languageid;
                            var languageabbr = result.data.languageabbr;
                            AuthService.login(token, languageid, languageabbr);
                            $location.path('/home');
                        })
                        .catch(function (error) {	// no respuesta
                            alert('Nombre de usuario o contraseña erroneo');
                            console.log(error);
                        });
            };
        })
        .controller('RegisterCtrl', function ($scope) {

            $scope.formData = {};

            $scope.submitForm = function (formData) {
                alert('Form submitted with' + JSON.stringify(formData));
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

        .controller('myCtrl', function ($scope, $http, ngDialog) {
            $scope.config = function (boardconf)
            {
                //-----------Iniciacion-----------
                var url = $scope.baseurl + "Board/loadCFG";
                var postdata = {idusu: window.localStorage.getItem('languageid'), lusu: window.localStorage.getItem('languageabbr')};

                $http.post(url, postdata);

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
                $scope.subgrid1 = 20;
                $scope.subgrid2 = 80;
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
             * Shor board and the pictograms
             */
            $scope.showBoard = function ()
            {
                var url = $scope.baseurl + "Board/showCellboard";

                $http.post(url).success(function (response)
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

                $scope.inEdit = true;
                $scope.grid1hide = false;
                $scope.grid2hide = false;
                $scope.grid3hide = true;
                $scope.grid1 = 3;
                $scope.grid2 = 9;
                $scope.grid3 = 0;

                var url = $scope.baseurl + "Board/getCellboard";

                $http.post(url).success(function (response)
                {
                    $scope.nameboard = response.name;
                    $scope.altura = $scope.range(20)[response.row].valueOf();
                    $scope.amplada = $scope.range(20)[response.col].valueOf();
//                    $scope.oldH = $scope.altura;
//                    $scope.oldW = $scope.amplada;
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
            /*
             * Resize cellboard (height and width)
             */
            $scope.changeSize = function ($newH, $newW, $HW)
            {
               
                var url = $scope.baseurl + "Board/getCellboard";

                $http.post(url).success(function (response)
                {

                    $scope.oldH = response.row;
                    $scope.oldW = response.col;

                    var postdata = {r: $newH, c: $newW};
                    if ($HW == "1"){
                        $newH = $scope.oldH;
                    }else{
                        $newW = $scope.oldW;
                    }
                    if ($newH < $scope.oldH || $newW < $scope.oldW) {
                        $scope.openConfirmSize($newH, $scope.oldH, $newW, $scope.oldW);
                    } else {
                        var url = $scope.baseurl + "Board/modifyCellBoard";
                        $http.post(url, postdata).then(function (response)
                        {
                            $scope.showBoard();
                        }).error(function (response)
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
                 var postdata = {r: $newH, c: $newW};
                //Object of all new/old sizes
                $scope.FormData = {
                    newH: $newH,
                    oldH: $oldH,
                    newW: $newW,
                    oldW: $oldW,
                    HWType: 2,
                    Dnum: 0
                };
                if ($newH != $oldH)
                {
                    $scope.FormData.HWType = 0;
                    $scope.FormData.Dnum = ($oldH - $newH);
                } else if ($newW != $oldW)
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
                        $scope.showBoard();
                    }).error(function (response) {});
                }, function (value) {
                    //if close
                    $scope.showBoard();
                });
            };

            /*
             * Open edit cell dialog
             */
            $scope.openEditCellMenu = function ($id) {
                var iscope = $scope.$new(true);
                //get basic info
                var url = $scope.baseurl + "Board/getCell";
                var postdata = {id: $id, idboard: 1};

                $http.post(url, postdata).success(function (response)
                {

                    iscope.info = response.info;
                    iscope.baseurl = $scope.baseurl;
                    iscope.getFunctions = function () {

                    };
                    ngDialog.open({
                        template: $scope.baseurl + '/angular_templates/EditCellView.html',
                        scope: iscope
                    });
                    $scope.cellType = "picto";
                });


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
            $scope.search = function ($Searchtype)
            {
                var postdata = {id: $scope.Name};
                //Radio button function parameter, to set search type
                switch ($Searchtype)
                {
                    case "Tots":
                        var URL = $scope.baseurl + "SearchWord/getDBAll";
                        break;
                    case "Noms":
                        var URL = $scope.baseurl + "SearchWord/getDBNames";
                        break;
                    case "Verb":
                        var URL = $scope.baseurl + "SearchWord/getDBVerbs";
                        break;
                    case "Adj":
                        var URL = $scope.baseurl + "SearchWord/getDBAdj";
                        break;
                    case "Exp":
                        var URL = $scope.baseurl + "SearchWord/getDBExprs";
                        break;
                    case "Altres":
                        var URL = $scope.baseurl + "SearchWord/getDBOthers";
                        break;
                    default:
                        var URL = $scope.baseurl + "SearchWord/getDBAll";
                }




                //Request via post to controller search data from database
                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.statusWord = response.status;
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
                    var postdata = {id: data.idpicto, pos: posInBoard};
                } else {
                    var postdata = {pos1: data.posInBoardPicto, pos2: posInBoard};
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

                var postdata = {pos: data.posInBoardPicto};
                var URL = $scope.baseurl + "Board/removePicto";


                $http.post(URL, postdata).
                        success(function (response)
                        {
                            $scope.statusWord = response.status;
                            $scope.data = response.data;
                        });
            }
            ;
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
        });

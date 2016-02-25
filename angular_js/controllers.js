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

        .controller('myCtrl', function ($scope, $http) {
            $scope.config = function (boardconf)
            {
                $scope.SearchType = "Tots";
                $scope.inEdit = false;
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
            $scope.range = function ($repeatnum)
            {
                var n = [];
                for (i = 1; i < $repeatnum + 1; i++)
                {
                    n.push(i);
                }
                return n;
            }
            $scope.openDefault = function () {
                ngDialog.open({
                    template: 'firstDialogId',
                    controller: 'InsideCtrl',
                    className: 'ngdialog-theme-default'
                });
            };
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
            $scope.showBoard = function ()
            {
                var url = $scope.baseurl + "Board/showCellboard";
                var postdata = {r: '0', c: '0'};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.columns = response.col;
                    $scope.rows = response.row;
                    $scope.data = response.data;
                });
            };

            //Controladores de editar
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
                });
            };

            $scope.changeWidth = function ($newSize)
            {
                var url = $scope.baseurl + "Board/modifyCellBoard";
                var postdata = {r: $scope.altura, c: $newSize};
                alert($scope.amplada + "   " + $newSize);
                if ($newSize < $scope.amplada) {
                    alert("QUE COJONES HACES");
                } else {

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.nameboard = response.name;
                        $scope.altura = $scope.range(20)[response.row].valueOf();
                        $scope.amplada = $scope.range(20)[response.col].valueOf();
                    });
                }
            };
            $scope.changeHeight = function ($newSize)
            {
                var url = $scope.baseurl + "Board/modifyCellBoard";
                var postdata = {r: $newSize , c:$scope.amplada};
                alert($scope.altura + "   " + $newSize);
                if ($newSize < $scope.altura) {
                    alert("QUE COJONES HACES");
                } else {

                    $http.post(url, postdata).success(function (response)
                    {
                        $scope.nameboard = response.name;
                        $scope.altura = $scope.range(20)[response.row].valueOf();
                        $scope.amplada = $scope.range(20)[response.col].valueOf();
                    });
                }
            };

            $scope.openMenu = function ($id) {

                open($scope.baseurl + 'editMenu.html', '', 'top=300,left=300,width=300,height=300');
            };

            // Desde aqui son del div de sentencias
            $scope.addToSentence = function (id) {

                var url = $scope.baseurl + "Board/addWord";
                var postdata = {id: id};

                $http.post(url, postdata).success(function (response)
                {
                    $scope.dataTemp = response.data;
                });
            };
            $scope.deleteLast = function () {

                var url = $scope.baseurl + "Board/deleteLastWord";

                $http.post(url).success(function (response)
                {
                    $scope.dataTemp = response.data;
                });
            };
            $scope.deleteAll = function () {

                var url = $scope.baseurl + "Board/deleteAllWords";

                $http.post(url).success(function (response)
                {
                    $scope.dataTemp = response.data;
                });
            };
            $scope.generate = function () {

                var url = $scope.baseurl + "Board/generate";
                $scope.tense = "defecte";
                $scope.tipusfrase = "defecte";
                $scope.negativa = false;
                var postdata = {tense: $scope.tense, tipusfrase: $scope.tipusfrase, negativa: $scope.negativa};
                $http.post(url, postdata).success(function (response)
                {
                    console.log(response);
                    $scope.dataTemp = response.data;
                    $scope.info = response.info;
                });
                $scope.tense = "defecte";
                $scope.tipusfrase = "defecte";
                $scope.negativa = false;
            };

            //Search controllers
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
            $scope.range = function ($max) {
                var range = [];
                for (i = 0; i < $max; i++) {
                    range.push(i);
                }
                return range;
            };
            //Dragndrop events
            $scope.centerAnchor = true;
            $scope.toggleCenterAnchor = function () {
                $scope.centerAnchor = !$scope.centerAnchor;
            };
            var onDraggableEvent = function (evt, data) {
                console.log("128", "onDraggableEvent", evt, data);
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
        });

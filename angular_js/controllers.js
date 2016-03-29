angular.module('controllers', [])

        .controller('myCtrl', function ($location, $scope, ngAudio, $http, ngDialog, txtContent, $rootScope) {
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
            
            $scope.InitScan = function ()
            {
                $scope.currentScanBlock = 1;
                $scope.currentScanBlock1 = 1;
                $scope.currentScanBlock2 = 1;
                $scope.maxScanBlock1 = 5;
                $scope.maxScanBlock2 = 5;
            };
            
            $scope.changeBlockScan = function () {
                if ($scope.currentScanBlock === 1){
                    $scope.currentScanBlock1 = $scope.currentScanBlock1 + 1;
                }else if ($scope.currentScanBlock === 2){
                    $scope.currentScanBlock2 = $scope.currentScanBlock2 + 1;
                }else if ($scope.currentScanBlock === 3){
                    //Hacerlo con un array
                }
            };
            
            $scope.selectBlockScan = function () {
                $scope.currentScanBlock = $scope.currentScanBlock + 1;
            };
            
            // Get the user config and show the board
            $scope.config = function (boardconf)
            {
                //-----------Iniciacion-----------
                $scope.userViewHeight = 100;
                $scope.searchFolderHeight = 0;
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
                $scope.showBoard('0')
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
                $scope.sentenceViewTop = true;
                $scope.sentenceViewHeight = 20;
                $scope.userViewWidth = 100;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 60;
            };
            $scope.showdown = function ()
            {
                $scope.sentenceViewTop = false;
                $scope.sentenceViewHeight = 0;
                $scope.userViewWidth = 100;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 80;
            };
            $scope.showup = function ()
            {
                $scope.sentenceViewTop = true;
                $scope.sentenceViewHeight = 16;
                $scope.userViewWidth = 100;
                $scope.searchFolderHeight = 0;
                $scope.boardHeight = 78;
            };
            $scope.showmiddle = function ()
            {
                $scope.sentenceViewTop = false;
                $scope.sentenceViewHeight = 0;
                $scope.userViewWidth = 100;
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

            /*
             * Show edit view board
             */
            $scope.edit = function ()
            {
                $scope.getPrimaryBoard();
                $scope.inEdit = true;
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
                alert(postdata.Name);
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
                });

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

                //MODIF: dir frase
                $scope.sound = ngAudio.load($scope.baseurl + "mp3/sound.mp3");
                $scope.sound.play();
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

            /*
             *
             *  editFolders functions
             *  
             */
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
        .controller('Edit', function ($scope, $http, ngDialog, txtContent, $rootScope) {
            // Get the cell clicked (the cell in the cicked position in the current board
            var url = $scope.baseurl + "Board/getCell";
            var postdata = {id: $scope.idEditCell, idboard: $scope.idboard};

            $http.post(url, postdata).success(function (response)
            {
                $scope.Editinfo = response.info;
                var idCell = response.info.ID_RCell;


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
                if ($scope.Editinfo.customScanBlockText2 !== null) {
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
        ]);
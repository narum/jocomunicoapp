angular.module('controllers')
    .controller('panelCtrl', function ($scope, $rootScope, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
        // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
        if (!$rootScope.isLogged) {
            $location.path('/login');
        }
        // Pedimos los textos para cargar la pagina
        txtContent("panelgroup").then(function (results) {
            $scope.content = results.data;
        });

        //Dropdown Menu Bar
        $rootScope.dropdownMenuBarValue = '/panelGroups'; //Button selected on this view
        $rootScope.dropdownMenuBarChangeLanguage = false;//Languages button available
        $rootScope.dropdownMenuBar = [];
        $rootScope.dropdownMenuBarButtonHide = false;

        //Choose the buttons to show on bar
        dropdownMenuBarInit($rootScope.interfaceLanguageId)
            .then(function () {
                //Choose the buttons to show on bar
                angular.forEach($rootScope.dropdownMenuBar, function (value) {
                    if (value.href == '/' || value.href == '/info' || value.href == '/panelGroups' || value.href == '/userConfig' || value.href == '/faq' || value.href == '/tutorial' || value.href == '/contact' || value.href == '/privacity' || value.href == 'logout') {
                        value.show = true;
                    } else {
                        value.show = false;
                    }
                });
            });
        //function to change html view with dropdown menu buttons
        $scope.go = function (path) {
            if (path == 'logout') {
                $('#logoutModal').modal('toggle');
            } else {
                $location.path(path);
                $rootScope.dropdownMenuBarValue = path; //Button selected on this view
            }
        };

        //Log Out Modal
        Resources.main.get({'section': 'logoutModal', 'idLanguage': $rootScope.interfaceLanguageId}, {'funct': "content"}).$promise
                .then(function (results) {
                    $scope.logoutContent = results.data;
                });
        $scope.logout = function () {
            $timeout(function () {
                AuthService.logout();
            }, 1000);
        };


        //Content Images and backgrounds
        $scope.img = [];
        $scope.img.fons = '/img/srcWeb/patterns/fons.png';
        $scope.img.lowSorpresaFlecha = '/img/srcWeb/Mus/lowSorpresaFlecha.png';
        $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
        $scope.img.Patterns4 = '/img/srcWeb/patterns/pattern4.png';
        $scope.img.Patterns6 = '/img/srcWeb/patterns/pattern6.png';
        $scope.img.loading = '/img/srcWeb/Login/loading.gif';
        
//        //Historic folders day/week/month
//        Resources.main.get({'IdU': $rootScope.userId}, {'funct': "getHistoric"}).$promise
//        .then(function (results) {
//            $scope.historicFolders.push({'ID_Folder':'0','name':'today', 'img':'img/pictos/hoy.png', 'sentences':results.today});
//            $scope.historicFolders.push({'ID_Folder':'0','name':'lastWeek', 'img':'img/pictos/semana.png', 'sentences':results.lastWeek});
//            $scope.historicFolders.push({'ID_Folder':'0','name':'lastMonth', 'img':'img/pictos/mes.png', 'sentences':results.lastMonth});
//            console.log(results);
//            console.log($scope.historicFolders);
//        });

        //User sentence folders
        $scope.historicFolders=[];
        Resources.main.get({'funct': "getSentenceFolders"}).$promise
        .then(function (results) {
            $scope.historicFolders.push({'ID_Folder':'0', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'today', 'imgSFolder':'img/pictos/hoy.png', 'folderColor':'dfdfdf', 'folderOrder':'0'});
            $scope.historicFolders.push({'ID_Folder':'0', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'lastWeek', 'imgSFolder':'img/pictos/semana.png', 'folderColor':'dfdfdf', 'folderOrder':'0'});
            $scope.historicFolders.push({'ID_Folder':'0', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'lastMonth', 'imgSFolder':'img/pictos/mes.png', 'folderColor':'dfdfdf', 'folderOrder':'0'});
            angular.forEach(results.folders, function (value) {
                $scope.historicFolders.push(value);
            });
            $scope.historicFolders.sort(function(a, b){return a.folderOrder-b.folderOrder});
        });
        //Up folder order
        $scope.upFolder = function(order){
            order = parseInt(order, 10);
            if (order>1){
                $scope.historicFolders[order+2].folderOrder = (order-1).toString();
                $scope.historicFolders[order+1].folderOrder = (order).toString();
                Resources.main.save({'ID_Folder': $scope.historicFolders[order+2].ID_Folder}, {'funct': "upHistoricFolder"}).$promise
                $scope.historicFolders.sort(function(a, b){return a.folderOrder-b.folderOrder});
            }
        };
        //Down folder order
        $scope.downFolder = function(order){
            order = parseInt(order, 10);
            if (order < ($scope.historicFolders.length -3)){
                $scope.historicFolders[order+2].folderOrder = (order+1).toString();
                $scope.historicFolders[order+3].folderOrder = (order).toString();
                Resources.main.save({'ID_Folder': $scope.historicFolders[order+2].ID_Folder}, {'funct': "downHistoricFolder"}).$promise
                $scope.historicFolders.sort(function(a, b){return a.folderOrder-b.folderOrder});
            }
        };

        //Scrollbar inside div
        $scope.$on('scrollbar.show', function () {
            console.log('Scrollbar show');
        });

        $scope.$on('scrollbar.hide', function () {
            console.log('Scrollbar hide');
        });
        $scope.$on('scrollbar.show', function () {
            console.log('Scrollbar show');
        });
        
        $scope.range = function ($repeatnum)
        {
            var n = [];
            for (i = 1; i < $repeatnum; i++)
            {
                n.push(i);
            }
            return n;
        };

        $scope.addWord = function (newModif,addWordType) {
            $rootScope.addWordparam = {newmod: newModif,type: addWordType};
            $location.path('/addWord');
        };
        $scope.initPanelGroup = function () {
            var URL = $scope.baseurl + "PanelGroup/getUserPanelGroups";

            $http.post(URL).
                    success(function (response)
                    {
                        $scope.panels = response.panels;
                    });
        };
        $scope.initPanelGroup();
        $scope.copyGroupBoard = function (idboard) {
            $scope.idboardToCopy = idboard;
            $scope.isLoged = "false";
            $scope.state = "";
            $scope.state2 = "";
            $scope.usernameCopyPanel = "";
            $scope.passwordCopyPanel = "";
            $scope.idUser = null;
            $('#ConfirmCopyGroupBoard').modal({backdrop: 'static'});
        };
        $scope.changeUser = function () {
            $scope.isLoged = "false";
            $scope.state = "";
            $scope.state2 = "";
            $scope.usernameCopyPanel = "";
            $scope.passwordCopyPanel = "";
            $scope.idUser = null;
        }
        $scope.login = function () {
            if ($scope.usernameCopyPanel == "") {
                $scope.state = 'has-warning';
            } else {
                $scope.state = '';
            }
            if ($scope.passwordCopyPanel == "") {
                $scope.state2 = 'has-warning';
            } else {
                $scope.state2 = '';
            }
            if ($scope.usernameCopyPanel != "" && $scope.passwordCopyPanel != "") {
                $scope.isLoged = "loading";
                var postdata = {user: $scope.usernameCopyPanel, pass: $scope.passwordCopyPanel};
                var url = $scope.baseurl + "PanelGroup/loginToCopy";
                $http.post(url, postdata).
                        success(function (response)
                        {
                            if (response.userID != null) {
                                $scope.idUser = response.userID;
                                $scope.isLoged = "true";
                            } else {
                                $scope.state = 'has-error';
                                $scope.state2 = 'has-error';
                                $scope.isLoged = "false";
                            }
                        });
            }
        };
        $scope.ConfirmCopyBoard = function () {
            var URL = $scope.baseurl + "PanelGroup/copyGroupBoard";
            var postdata = {id: $scope.idboardToCopy, user: $scope.idUser};
            $http.post(URL, postdata).success(function (response)
            {

            });
        };
        $scope.newPanellGroup = function () {
            $scope.CreateBoardData = {GBName: '', defH: 5, defW: 5, imgGB: ""};
            $('#ConfirmCreateGroupBoard').modal({backdrop: 'static'});
        };

        $scope.ConfirmNewPanellGroup = function () {
            var URL = $scope.baseurl + "PanelGroup/newGroupPanel";
            $http.post(URL, $scope.CreateBoardData).success(function (response)
            {
                $rootScope.editPanelInfo = {idBoard: response.idBoard};
                $location.path('/');
            });
        };


        $scope.editPanel = function (idGB) {
            var postdata = {ID_GB: idGB};
            var URL = $scope.baseurl + "PanelGroup/getPanelToEdit";

            $http.post(URL, postdata).
                    success(function (response)
                    {
                        $scope.id = response.id;
                        if ($scope.id === null) {//MODIF:--Modal no tiene panel pricipal, se añade uno para que pueda hacer algo (no se si se puede hacer, ya que el modal creo que se ira. Si pasa esto meter una variable en el objeto editpanelinfo)
                            $scope.id = response.boards[0].ID_Board;
                        }
                        // Put the panel to edit info, and load the edit panel  
                        $rootScope.editPanelInfo = {idBoard: $scope.id};
                        $location.path('/');
                    });
        };

        $scope.setPrimary = function (idGB) {
            var postdata = {ID_GB: idGB};
            var URL = $scope.baseurl + "PanelGroup/setPrimaryGroupBoard";

            $http.post(URL, postdata).
                    success(function (response)
                    {
                        $scope.initPanelGroup();
                    });
        };
        //MODIF: creo que se pude borrar
        $scope.CreateBoard = function (ID_GB) {
            $scope.CreateBoardData = {CreateBoardName: '', height: 0, width: 0, idGroupBoard: ID_GB};
            ngDialog.openConfirm({
                template: $scope.baseurl + '/angular_templates/ConfirmCreateBoard.html',
                scope: $scope,
                className: 'ngdialog-theme-default dialogCreateBoard'
            }).then(function () {

                var URL = $scope.baseurl + "Board/newBoard";


                $http.post(URL, $scope.CreateBoardData).success(function (response)
                {
                    $rootScope.editPanelInfo = {idBoard: response.idBoard};
                    $location.path('/');
                });

            }, function (value) {
            });
        };

        $scope.changeGroupBoardName = function (nameboard, idgb)
        {
            var postdata = {Name: nameboard, ID: idgb};
            var URL = $scope.baseurl + "PanelGroup/modifyGroupBoardName";
            $http.post(URL, postdata).
                    success(function (response)
                    {

                    });
        };
        $scope.$on('scrollbarPanel', function (ngRepeatFinishedEvent) {
            $scope.$broadcast('rebuild:me');
        });

    })



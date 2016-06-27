angular.module('controllers')
    .controller('sentencesFolderCtrl', function ($scope, $rootScope, txtContent, $routeParams, $location, dropdownMenuBarInit, AuthService, Resources, $timeout) {
        // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
        if (!$rootScope.isLogged) {
            $location.path('/login');
            $rootScope.dropdownMenuBarValue = '/'; //Dropdown bar button selected on this view
        }
        // Pedimos los textos para cargar la pagina
        txtContent("panelgroup").then(function (results) {
            $scope.content = results.data;
        });

        //Dropdown Menu Bar
        $rootScope.dropdownMenuBarValue = '/sentencesFolder'; //Button selected on this view
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
        $scope.img.Patterns4 = '/img/srcWeb/patterns/pattern4.png';
        $scope.img.loading = '/img/srcWeb/Login/loading.gif';
        
        //Variable declaration
        $scope.viewActived = true;
        $scope.historicSentencesView = false;
        if($routeParams.folderId<0){
            $scope.historicSentencesView = true;
        }
        
        //Get sentences folder or Historic folder
        Resources.main.save({'ID_Folder': $routeParams.folderId},{'funct': "getSentencesOrHistoricFolder"}).$promise
        .then(function (results) {
            console.log(results.ID_Folder);
            $scope.sentences = results.ID_Folder;
        });
        //scrollbar
        $scope.$on('scrollbarSentences', function (ngRepeatFinishedEvent) {
            $scope.$broadcast('rebuild:meS');
        });
    })
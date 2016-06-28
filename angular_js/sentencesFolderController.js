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
        //scrollbars
        $scope.$on('scrollbarSentences', function () {
            $scope.$broadcast('rebuild:meS');
        });
        $scope.$on('scrollbarSentences2', function () {
            $scope.$broadcast('rebuild:meS2');
        });
        $scope.$on('scrollbar.show', function () {
            console.log('Scrollbar show');
        });
        $scope.$on('scrollbar.hide', function () {
            console.log('Scrollbar hide');
        });


        //Content Images and backgrounds
        $scope.img = [];
        $scope.img.fons = '/img/srcWeb/patterns/fons.png';
        $scope.img.lowSorpresaFlecha = '/img/srcWeb/Mus/lowSorpresaFlecha.png';
        $scope.img.Patterns4 = '/img/srcWeb/patterns/pattern4.png';
        $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
        $scope.img.loading = '/img/srcWeb/Login/loading.gif';
        
        //Variable declaration
        $scope.viewActived = false;
        $scope.historicFolder = false;
        
        //Folder info
        if($routeParams.folderId<0){
            $scope.historicFolder = true;
            if($routeParams.folderId=='-1'){
                $scope.folderSelected = {'ID_Folder':'-1', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'today', 'imgSFolder':'img/pictos/hoy.png', 'folderColor':'dfdfdf', 'folderOrder':'0'};
            }else if($routeParams.folderId=='-7'){
                $scope.folderSelected = {'ID_Folder':'-7', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'lastWeek', 'imgSFolder':'img/pictos/semana.png', 'folderColor':'dfdfdf', 'folderOrder':'0'};
            }else if($routeParams.folderId=='-30'){
                $scope.folderSelected = {'ID_Folder':'-30', 'ID_SFUser':$rootScope.userId, 'folderDescr':'', 'folderName':'lastMonth', 'imgSFolder':'img/pictos/mes.png', 'folderColor':'dfdfdf', 'folderOrder':'0'};
            }
        }
        //Get sentences folder or Historic folder
        var getSentences = function(){
            Resources.main.save({'ID_Folder': $routeParams.folderId},{'funct': "getSentencesOrHistoricFolder"}).$promise
            .then(function (results) {
                $scope.sentences = results.sentences;
                $scope.viewActived = true;
                if($routeParams.folderId>0){
                    $scope.folderSelected = results.folder;
                }
            });
        };
        getSentences();
        
        //Copy sentence on folder
        $scope.copySentence = function(ID_SHistoric,ID_SSentence){
            if($scope.historicFolder){
                $scope.sentenceToCopy = ID_SHistoric;
            }else{
                $scope.sentenceToCopy = ID_SSentence;
            }
            Resources.main.get({'funct': "getSentenceFolders"}).$promise
            .then(function (results) {
                $scope.folders = results.folders;
                $('#copySentenceModal').modal('toggle');//Show modal
            });
        };
        $scope.copyOnFolder = function(ID_Folder){
            $('#copySentenceModal').modal('hide');//Hide modal
            Resources.main.save({'ID_Folder':ID_Folder, 'ID_Sentence':$scope.sentenceToCopy,'historicFolder':$scope.historicFolder},{'funct': "addSentenceOnFolder"}).$promise
            .then(function (results) {
                getSentences();
            });
        };
        $scope.deleteSentence = function(ID_SSentence){
            Resources.main.save({'ID_SSentence':ID_SSentence},{'funct': "deleteSentenceFromFolder"}).$promise
            .then(function (results) {
                console.log(results);
                getSentences();
            });
        };
    });
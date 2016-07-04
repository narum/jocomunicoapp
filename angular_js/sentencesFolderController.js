angular.module('controllers')
    .controller('sentencesFolderCtrl', function ($scope, $rootScope, txtContent, $routeParams, $location, dropdownMenuBarInit, AuthService, Resources, $timeout, $http) {
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
                console.log($scope.sentences);
                $scope.viewActived = true;
                if($routeParams.folderId>0){
                    $scope.folderSelected = results.folder;
                    $scope.newFolder = JSON.parse(JSON.stringify(results.folder)); //copy JavaScript object to new variable NOT by reference
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
                console.log(results);
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
        //edit folder
        $scope.editHistoricFolder = function(){
            $('#editHistoricFolderModal').modal('toggle');//Show modal
        };
        $scope.deleteFolderModal = function(){
            $('#deleteFolderModal').modal('toggle');//Show modal
        };
        $scope.saveFolder = function(){
            Resources.main.save({'folder':$scope.newFolder},{'funct': "editSentenceFolder"}).$promise
            .then(function (results) {
                $scope.folderSelected = JSON.parse(JSON.stringify($scope.newFolder)); //copy JavaScript object to new variable NOT by reference
            });
        };
        $scope.deleteFolder = function(){
            $scope.viewActived = false;
            Resources.main.save({'folder':$scope.newFolder},{'funct': "deleteSentenceFolder"}).$promise
            .then(function (results) {
                console.log(results);
                $location.path('/panelGroups');
            });
        };
        

        /*
         * Return uploaded images from database. There are two types, the users images an the arasaac (not user images)
         */
        $scope.searchImg = function (name, typeImgEditSearch) {
            var URL = "";
            switch (typeImgEditSearch)
            {
                case "Arasaac":
                    URL = $scope.baseurl + "ImgUploader/getImagesArasaac";
                    break;
                case "Uploads":
                    URL = $scope.baseurl + "ImgUploader/getImagesUploads";
                    break;
            }
            var postdata = {name: name};
            $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.imgData = response.data;
                });
        }

        //get all the photos attached to the pictos
        $scope.searchFoto = function (name)
        {
            var URL = $scope.baseurl + "SearchWord/getDBAll";
            var postdata = {id: name};
            //Request via post to controller search data from database
            $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.allImg = response.data;
                });
        };
        // Upload and resize the image
        $scope.uploadFile = function () {
            $scope.myFile = document.getElementById('file-input').files;
            $scope.uploading = true;
            var i;
            var uploadUrl = $scope.baseurl + "ImgUploader/upload";
            var fd = new FormData();
            fd.append('vocabulary', angular.toJson(false));
            for (i = 0; i < $scope.myFile.length; i++) {
                fd.append('file' + i, $scope.myFile[i]);
            }
            $http.post(uploadUrl, fd, {
                headers: {'Content-Type': undefined}
            })
                .success(function (response) {
                    $scope.uploading = false;
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
    });
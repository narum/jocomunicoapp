angular.module('controllers')
        .controller('addWordCtrl', function ($scope, $rootScope, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
            $scope.initAddWord = function () {
                if ($rootScope.addWordparam != null) {
                    $scope.NewModif = $rootScope.addWordparam.newmod;
                    $scope.addWordType = $rootScope.addWordparam.type;
                    $rootScope.addWordparam = null;
                    console.log($scope.addWordType);
                } else {
                    $location.path("/panelGroups");
                }
                
                if($scope.NewModif == 1){
                    switch($scope.addWordType)
                    {
                        case "name":
                            $scope.objAdd = {type: "Name",nomtext: null, mf: false, singpl: false, contabincontab: null, determinat: null, ispropernoun: null, defaultverb: null, plural: null, femeni: null, fempl: null};
                            break;
                        case "adj":
                            $scope.objAdd = {type: "Adj", masc: null, fem: null, mescpl: null, fempl: null, subjdef: null};
                            break;
                        default:
                            break;
                    }
                }
                else{
                    //MODIF: Coger BBDD los valores de los objetos
                    //MODIF: Llenar los valores de cada uno de ellos
                        var URL = $scope.baseurl + "Board/XXXXXXXX";
//                      MODIF: HA DE GUARDAR LES DADES DEL POST
//                      $http.post(URL, objAdd).success(function (response)
//                      {                               
//                      });
                    switch($scope.addWordType)
                    {
                        case "Name":
                            $scope.objAdd = {type: "Name",nomtext: null, mf: null, singpl: null, contabincontab: null, determinat: null, ispropernoun: null, defaultverb: null, plural: null, femeni: null, fempl: null};
                            break;
                        case "Adj":
                            $scope.objAdd = {type: "Adj", masc: null, fem: null, mescpl: null, fempl: null, subjdef: null};
                            break;
                        default:
                            break;
                    }
                }
            };
            $scope.cancelAddWord = function (){
                $location.path("/panelGroups");
            };

            
            $scope.saveAddWord = function (){
                var URL = $scope.baseurl + "Board/XXXXXXXX";
                alert($scope.objAdd.singpl);
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
        })
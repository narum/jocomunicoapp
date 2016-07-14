angular.module('controllers')
    .controller('consellsCtrl', function ($http, $scope, $rootScope, Resources, AuthService, txtContent, $location, $timeout, dropdownMenuBarInit) {
        
        //Dropdown Menu Bar
            $rootScope.dropdownMenuBar = null;
            if($rootScope.isLogged){
                var languageId = $rootScope.interfaceLanguageId;
                $rootScope.dropdownMenuBarChangeLanguage = false;//Languages button available
            } else {
                var languageId = $rootScope.contentLanguageUserNonLoged;
                $rootScope.dropdownMenuBarChangeLanguage = true;//Languages button available
            }
            dropdownMenuBarInit(languageId)
                    .then(function () {
                        //Choose the buttons to show on bar
                        if ($rootScope.isLogged){
                            angular.forEach($rootScope.dropdownMenuBar, function (value) {
                                if (value.href == '/' || value.href == '/about' || value.href == '/panelGroups' || value.href == '/userConfig' || value.href == '/faq' || value.href == '/download' || value.href == '/tips' || value.href == '/privacy') {
                                    value.show = true;
                                } else {
                                    value.show = false;
                                }
                            });
                        }else{
                            angular.forEach($rootScope.dropdownMenuBar, function (value) {
                                if (value.href == '/home' || value.href == '/about' || value.href == '/faq' || value.href == '/download' || value.href == '/tips' || value.href == '/privacy') {
                                    value.show = true;
                                } else {
                                    value.show = false;
                                }
                            });
                        }
                    });
            $rootScope.dropdownMenuBarValue = '/tips'; //Button selected on this view
            $rootScope.dropdownMenuBarButtonHide = false;
            //function to change html view
            $scope.go = function (path) {
                $rootScope.dropdownMenuBarValue = path; //Button selected on this view
                $location.path(path);
            };
            //function to change html content language
            $scope.changeLanguage = function (value) {
                $rootScope.contentLanguageUserNonLoged = value;
                window.localStorage.setItem('contentLanguageUserNonLoged', $rootScope.contentLanguageUserNonLoged);
                window.localStorage.setItem('contentLanguageUserNonLogedAbbr', $rootScope.contentLanguageUserNonLogedAbbr);
                Resources.register.get({'section': 'tips', 'idLanguage': value}, {'funct': "content"}).$promise
                        .then(function (results) {
                            $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;
                            $scope.content = results.data;
                            dropdownMenuBarInit(value);
                        });
            };
        
        
        $scope.linkHome = function () {
            $location.path('/home');
        };
        
        $scope.contentBar11 = false;
        $scope.contentBar21 = false;
        $scope.contentBar22 = false;
        $scope.contentBar23 = false;
        $scope.contentBar24 = false;
        $scope.contentBar25 = false;
        $scope.contentBar26 = false;
        $scope.contentBar31 = false;        

        //Imagenes
        $scope.img = [];
        $scope.img.fons = '/img/srcWeb/patterns/fons.png';
        $scope.img.loading = '/img/srcWeb/Login/loading.gif';
        $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
        $scope.img.whiteLoading = '/img/icons/whiteLoading.gif';
        $scope.img.Loading_icon = '/img/icons/Loading_icon.gif';
        $scope.img.orangeArrow = '/img/srcWeb/UserConfig/orangeArrow.png';  
        
        // Language
        $rootScope.langabbr = $rootScope.contentLanguageUserNonLogedAbbr;

        // Get content for the home view from ddbb           
        Resources.register.get({'section': 'tips', 'idLanguage': $rootScope.contentLanguageUserNonLoged}, {'funct': "content"}).$promise
        .then(function (results) {
            $scope.text = results.data;
            $scope.viewActived = true;
        });

        $scope.viewActived = false; // para activar el gif del loading        
    });
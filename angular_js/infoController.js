angular.module('controllers')
        .controller('infoCtrl', function ($scope, $rootScope, txtContent, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
           
            /*
             * MENU CONFIGURATION
             */
           
            $rootScope.contetnLanguageUserNonLoged = 1; // default language Catalan
           
            //Images
            $scope.img = [];
            $scope.img.fons = '/img/srcWeb/patterns/fons.png';
            $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
            $scope.img.loading = '/img/srcWeb/Login/loading.gif';
           
           
           //Dropdown Menu Bar
            dropdownMenuBarInit($rootScope.contetnLanguageUserNonLoged)
                    .then(function () {
                        $rootScope.dropdownMenuBarChangeLanguage = true; //Languages button available
                        //Choose the buttons to show on bar
                        angular.forEach($rootScope.dropdownMenuBar, function (value) {
                            if (value.href == '/home' || value.href == '/faq' || value.href == '/tutorial' || value.href == '/privacity') {
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
                $rootScope.interfaceLanguageId = value;
                // content for the menu
                Resources.register.get({'section': 'login', 'idLanguage': value}, {'funct': "content"}).$promise
                        .then(function (results) {
                            $scope.content = results.data;
                            dropdownMenuBarInit(value);
                        });
                // content for the home view    
                Resources.register.get({'section': 'home', 'idLanguage': value}, {'funct': "content"}).$promise
                        .then(function (results) {
                            $scope.text = results.data;
                        });
            };
                
            /*
            * HOME VIEW FUNCTIONS
            */
           
            // Get content for the home view for ddbb   
            Resources.register.get({'section': 'home', 'idLanguage': $rootScope.contetnLanguageUserNonLoged}, {'funct': "content"}).$promise
                .then(function (results) {
                    $scope.text = results.data;
                });
           
                    
        });
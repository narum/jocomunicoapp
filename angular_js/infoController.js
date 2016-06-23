angular.module('controllers', ['ngAnimate'])
        .controller('infoCtrl', function ($scope, $rootScope, $location, $http, ngDialog, dropdownMenuBarInit, AuthService, Resources, $timeout) {
           
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
                            if (value.href == '/' || value.href == '/faq' || value.href == '/tutorial' || value.href == '/privacity') {
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
           
            // Cookies popup
            $scope.acceptcookies = window.localStorage.getItem('cookiesAccepted'); 
            
            if ($scope.acceptcookies) {
                $scope.footerclass = "footer-cookies-out";
            }
            else {
                $scope.footerclass = "footer-cookies";
            }
            
            $scope.okCookies = function () {
                window.localStorage.setItem('cookiesAccepted', true);
                $scope.acceptcookies = true;
                $scope.footerclass = "footer-cookies-fade";
            };
           
            //Images
            $scope.img.button1 = 'img/srcWeb/home/about.png';
            $scope.img.button2 = 'img/srcWeb/home/login.png';
            $scope.img.button3 = 'img/srcWeb/home/open_source.png';
            $scope.img.button4 = 'img/srcWeb/home/open_source.png';

            // Link colors
            $scope.link1color = "#f0a22e";
            $scope.link2color = "#b6211b";
            $scope.link3color = "#3b93af";
            $scope.link4color = "#3b93af";
           
            // Get content for the home view for ddbb   
            Resources.register.get({'section': 'home', 'idLanguage': $rootScope.contetnLanguageUserNonLoged}, {'funct': "content"}).$promise
                .then(function (results) {
                    $scope.text = results.data;
                });
           
            
            $scope.linkAbout = function () {
                $location.path('/about');
            };
            
            $scope.linkLogin = function () {
                $location.path('/login');
            };
            
            $scope.linkCollaborators = function () {
                $location.path('/collaborators');
            };
            
            $scope.linkColor = function (id, color, inout) {
                switch(id) {
                    case "link-1":
                        $scope.link1color = color;
                        if (!inout) {
                            $scope.img.button1 = 'img/srcWeb/home/about.png';
                        }
                        else {
                            $scope.img.button1 = 'img/srcWeb/home/about-hov.png';
                        }
                        break;
                        
                    case "link-2":
                        $scope.link2color = color;
                        if (!inout) {
                            $scope.img.button2 = 'img/srcWeb/home/login.png';
                        }
                        else {
                            $scope.img.button2 = 'img/srcWeb/home/login-hov.png';
                        }
                        break;
                        
                    case "link-3":
                        $scope.link3color = color;
                        if (!inout) {
                            $scope.img.button3 = 'img/srcWeb/home/open_source.png';
                        }
                        else {
                            $scope.img.button3 = 'img/srcWeb/home/open_source-hov.png';
                        }
                        break;
                        
                    case "link-4":
                        $scope.link4color = color;
                        if (!inout) {
                            $scope.img.button4 = 'img/srcWeb/home/open_source.png';
                        }
                        else {
                            $scope.img.button4 = 'img/srcWeb/home/open_source-hov.png';
                        }
                        break;
                        
                    default:
                        break;
                }
            };
            
            
        });

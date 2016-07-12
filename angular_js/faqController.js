angular.module('controllers')
    .controller('faqCtrl', function ($http, $scope, $rootScope, Resources, AuthService, txtContent, $location, $timeout, dropdownMenuBarInit) {
        // Comprobación del login   IMPORTANTE!!! PONER EN TODOS LOS CONTROLADORES
        if (!$rootScope.isLogged) {
            $location.path('/login');
            $rootScope.dropdownMenuBarValue = '/'; //Dropdown bar button selected on this view
        }
        
        $scope.linkHome = function () {
            $location.path('/home');
        };
        
        $scope.contentBar11 = false;
        $scope.contentBar21 = false;
        $scope.contentBar22 = false;
        $scope.contentBar31 = false;
        $scope.contentBar32 = false;
        $scope.contentBar33 = false;
        $scope.contentBar34 = false;
        $scope.contentBar35 = false;
        $scope.contentBar36 = false;
        $scope.contentBar41 = false;

        //Imagenes
        $scope.img = [];
        $scope.img.fons = '/img/srcWeb/patterns/fons.png';
        $scope.img.loading = '/img/srcWeb/Login/loading.gif';
        $scope.img.Patterns1_08 = '/img/srcWeb/patterns/pattern3.png';
        $scope.img.whiteLoading = '/img/icons/whiteLoading.gif';
        $scope.img.Loading_icon = '/img/icons/Loading_icon.gif';
        $scope.img.orangeArrow = '/img/srcWeb/UserConfig/orangeArrow.png';  

        // Language
        $rootScope.langabbr = "CA";
        $rootScope.contetnLanguageUserNonLoged = 1; // default language Catalan

        // Get content for the home view from ddbb           
        Resources.register.get({'section': 'faq', 'idLanguage': $rootScope.contetnLanguageUserNonLoged}, {'funct': "content"}).$promise
        .then(function (results) {
            $scope.text = results.data;
            $scope.viewActived = true;
        });

        $scope.viewActived = false; // para activar el gif del loading        
    });
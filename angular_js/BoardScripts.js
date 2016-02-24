var app = angular.module('mySearch', ['ngSanitize', "angular-bind-html-compile", 'ngDraggable','ngDialog']);
app.controller('myCtrl', function ($scope, $http) {
    $scope.config = function (boardconf)
    {
        $scope.SearchType = "Tots";
        $scope.inEdit = false;
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
        /*$scope.grid1hide = false;
         $scope.grid2hide = false;
         $scope.grid3hide = false;
         $scope.grid1 = 2;
         $scope.grid2 = 8;
         $scope.grid3 = 2;*/
    }; 
    $scope.range = function($repeatnum)
    {
        var n = [];
        for(i=1;i<$repeatnum+1;i++)
        {
            n.push(i);
        }
        return n;
    }
    $scope.showall = function ()
    {
        $scope.grid1hide = false;
        $scope.grid2hide = false;
        $scope.grid3hide = false;
        $scope.grid1 = 2;
        $scope.grid2 = 8;
        $scope.grid3 = 2;
    };
    $scope.showright = function ()
    {
        $scope.grid1hide = true;
        $scope.grid2hide = false;
        $scope.grid3hide = false;
        $scope.grid1 = 0;
        $scope.grid2 = 10;
        $scope.grid3 = 2;
    };
    $scope.showleft = function ()
    {
        $scope.grid1hide = false;
        $scope.grid2hide = false;
        $scope.grid3hide = true;
        $scope.grid1 = 2;
        $scope.grid2 = 10;
        $scope.grid3 = 0;
    };
    $scope.showmid = function ()
    {
        $scope.grid1hide = true;
        $scope.grid2hide = false;
        $scope.grid3hide = true;
        $scope.grid1 = 0;
        $scope.grid2 = 12;
        $scope.grid3 = 0;
    };

    $scope.showupdown = function ()
    {
        $scope.subgrid1hide = false;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = false;
        $scope.subgrid1 = 20;
        $scope.subgrid2 = 60;
        $scope.subgrid3 = 20;
    };
    $scope.showdown = function ()
    {
        $scope.subgrid1hide = true;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = false;
        $scope.subgrid1 = 0;
        $scope.subgrid2 = 80;
        $scope.subgrid3 = 20;
    };
    $scope.showup = function ()
    {
        $scope.subgrid1hide = false;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = true;
        $scope.subgrid1 = 20;
        $scope.subgrid2 = 80;
        $scope.subgrid3 = 0;
    };
    $scope.showmiddle = function ()
    {
        $scope.subgrid1hide = true;
        $scope.subgrid2hide = false;
        $scope.subgrid3hide = true;
        $scope.subgrid1 = 0;
        $scope.subgrid2 = 100;
        $scope.subgrid3 = 0;
    };
    $scope.showBoard = function ()
    {
        var url = $scope.baseurl + "Board/showCellboard";
        var postdata = {r: '0', c: '0'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columns = response.col;
            $scope.rows = response.row;
            $scope.data = response.data;
        });
    };

    //Controladores de editar
    $scope.edit = function ()
    {
        $scope.inEdit = true;
        $scope.grid1hide = false;
        $scope.grid2hide = false;
        $scope.grid3hide = true;
        $scope.grid1 = 3;
        $scope.grid2 = 9;
        $scope.grid3 = 0;
        
        var url = $scope.baseurl + "Board/getCellboard";
        var postdata = {r: '0', c: '1'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columnsnum = response.col;
            $scope.rowsnum = response.row;
            $scope.nameboard = response.name;
        });
    };

    $scope.addColumn = function ()
    {
        var url = $scope.baseurl + "Board/modifyCellboard";
        var postdata = {r: '0', c: '1'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columns = response.col;
            $scope.rows = response.row;
            $scope.data = response.data;
        });
    };
    $scope.removeColumn = function ()
    {
        var url = $scope.baseurl + "Board/modifyCellboard";
        var postdata = {r: '0', c: '-1'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columns = response.col;
            $scope.rows = response.row;
            $scope.data = response.data;
        });
    };
    $scope.addRow = function ()
    {
        var url = $scope.baseurl + "Board/modifyCellboard";
        var postdata = {r: '1', c: '0'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columns = response.col;
            $scope.rows = response.row;
            $scope.data = response.data;
        });
    };
    $scope.removeRow = function ()
    {
        var url = $scope.baseurl + "Board/modifyCellboard";
        var postdata = {r: '-1', c: '0'};

        $http.post(url, postdata).success(function (response)
        {
            $scope.columns = response.col;
            $scope.rows = response.row;
            $scope.data = response.data;
        });
    };
    $scope.openMenu = function ($id) {

        open($scope.baseurl + 'editMenu.html', '', 'top=300,left=300,width=300,height=300');
    };

    // Desde aqui son del div de sentencias
    $scope.addToSentence = function (id) {

        var url = $scope.baseurl + "Board/addWord";
        var postdata = {id: id};

        $http.post(url, postdata).success(function (response)
        {
            $scope.dataTemp = response.data;
        });
    };
    $scope.deleteLast = function () {

        var url = $scope.baseurl + "Board/deleteLastWord";

        $http.post(url).success(function (response)
        {
            $scope.dataTemp = response.data;
        });
    };
    $scope.deleteAll = function () {

        var url = $scope.baseurl + "Board/deleteAllWords";

        $http.post(url).success(function (response)
        {
            $scope.dataTemp = response.data;
        });
    };
    $scope.generate = function () {

        var url = $scope.baseurl + "Board/generate";

        $http.post(url).success(function (response)
        {
            $scope.dataTemp = response.data;
            $scope.info = response.info;
            alert($scope.dataTemp);
        });
    };

    //Search controllers
    $scope.search = function ($Searchtype)
    {
        var postdata = {id: $scope.Name};
        //Radio button function parameter, to set search type
        switch ($Searchtype)
        {
            case "Tots":
                var URL = $scope.baseurl + "SearchWord/getDBAll";
                break;
            case "Noms":
                var URL = $scope.baseurl + "SearchWord/getDBNames";
                break;
            case "Verb":
                var URL = $scope.baseurl + "SearchWord/getDBVerbs";
                break;
            case "Adj":
                var URL = $scope.baseurl + "SearchWord/getDBAdj";
                break;
            case "Exp":
                var URL = $scope.baseurl + "SearchWord/getDBExprs";
                break;
            case "Altres":
                var URL = $scope.baseurl + "SearchWord/getDBOthers";
                break;
            default:
                var URL = $scope.baseurl + "SearchWord/getDBAll";
        }




        //Request via post to controller search data from database
        $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.statusWord = response.status;
                    $scope.dataWord = response.data;
                });
    };
    $scope.range = function ($max) {
        var range = [];
        for (i = 0; i < $max; i++){
            range.push(i);
        }
        return range;
    };
    //Dragndrop events
    $scope.centerAnchor = true;
    $scope.toggleCenterAnchor = function () {
        $scope.centerAnchor = !$scope.centerAnchor;
    };
    var onDraggableEvent = function (evt, data) {
        console.log("128", "onDraggableEvent", evt, data);
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
            var postdata = {id: data.idpicto, pos: posInBoard};
        } else {
            var postdata = {pos1: data.posInBoardPicto, pos2: posInBoard};
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
        
        var postdata = {pos: data.posInBoardPicto};
        var URL = $scope.baseurl + "Board/removePicto";


        $http.post(URL, postdata).
                success(function (response)
                {
                    $scope.statusWord = response.status;
                    $scope.data = response.data;
                });
    };
});
//Add a directive in order to recognize the right click
app.directive('ngRightClick', function ($parse) {
    return function (scope, element, attrs) {
        var fn = $parse(attrs.ngRightClick);
        element.bind('contextmenu', function (event) {
            scope.$apply(function () {
                event.preventDefault();
                fn(scope, {$event: event});
            });
        });
    };
});

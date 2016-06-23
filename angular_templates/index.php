<html ng-app="app">
    <head>
        <title>JoComunico</title>
        <link rel="icon" type="image/ico" href="img/icons/favicon.png">
        <base href="/"></base>

        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
        <meta name="HandheldFriendly" content="true">
        <meta name="apple-mobile-web-app-capable" content="no"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>libraries/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>libraries/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/typeahead.css">
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/app.css">
        <!--MODIF: sacar de aqui y poner en el html. Primero sacar todo lo comun-->

        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>css/generico.css">
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/libraries/ngDialog.min.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/libraries/ngDialog-theme-default.min.css"/>


    </head>
    <body>
        <div ng-view class="root" ng-init="baseurl = '<?= base_url(); ?>'"></div>

        <script type="text/javascript" src="<?= base_url(); ?>libraries/jquery.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular-animate.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular-route.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular-resource.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular-cookies.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/ui-bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/ui-bootstrap-tpls.min.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>/libraries/ng-scrollbar.js"></script>

        <script type="text/javascript" src="<?= base_url(); ?>libraries/bootstrap.min.js"></script>

        <script type="text/javascript" src="<?= base_url(); ?>angular_js/app.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/controllers.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/services.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/captcha.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/panelController.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/addWordController.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>angular_js/infoController.js"></script>

        <script type="text/javascript" src="<?= base_url(); ?>libraries/angular-bind-html-compile.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/ngTouch.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/ngDraggable.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>libraries/ngDialog.min.js"></script>
        <link href="<?= base_url(); ?>libraries/bootstrap-switch.css" rel="stylesheet">
        <script src="<?= base_url(); ?>libraries/bootstrap-switch.js"></script>
    </body>
</html>

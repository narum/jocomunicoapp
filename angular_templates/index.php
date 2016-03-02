<html ng-app="app">
<head>
	<title>Angular</title>
	<base href="/"></base>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
	<link rel="stylesheet" href="<?= base_url();?>libraries/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?= base_url();?>libraries/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?= base_url();?>css/typeahead.css">
	<link rel="stylesheet" type="text/css" href="<?= base_url();?>css/app.css">
</head>
<body>
	<div ng-view class="root" ng-init="baseurl = '<?= base_url();?>'"></div>

	<script type="text/javascript" src="<?= base_url();?>libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/angular.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/angular-animate.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/angular-route.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/angular-resource.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/angular-cookies.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/ui-bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= base_url();?>libraries/ui-bootstrap-tpls.min.js"></script>
	
	<script type="text/javascript" src="<?= base_url();?>angular_js/app.js"></script>
	<script type="text/javascript" src="<?= base_url();?>angular_js/controllers.js"></script>
	<script type="text/javascript" src="<?= base_url();?>angular_js/services.js"></script>
</body>
</html>

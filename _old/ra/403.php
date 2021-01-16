<!DOCTYPE html> 
<html><head>
<?= sfHead("403") ?>
</head><body> 
<div id="p-403" data-role="page" data-theme="b">
	<div data-role="header" data-position="fixed" >
		<h1><?= $_SERVER['REQUEST_URI'] ?></h1>
		<a href="home" data-corners="false" data-ajax="false" data-icon="home"><?= _e("Home") ?></a>
	</div>  <!-- /header -->
	<div data-role ="content" >	
		<h3 class="ui-bar ui-bar-a ui-corner-all" style="margin:5px;text-align:center;"><?= _e("403 Forbidden") ?></h3>
		<div id="logo_image" style="text-align:center;margin:5 auto">
            <img src="img/403.jpg" style="max-width: 100%">
		</div>
	</div>  <!-- /content -->
</div>  <!-- /page home -->
</body></html>
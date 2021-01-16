<?php
require_once ("rr_config_gen.php");  // generic defines
require_once ("rr_config_func.php"); // tools functions

$gst = !empty($_REQUEST['err'])? "Error" : "Warning";
$gwe = @$_REQUEST['err'] .@$_REQUEST['msg']
?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("Error") ?>
</head>
    <body>
    
        <div id="page-config-define" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1><?=$gst?></h1>
            </div> <!-- /header -->	
            <div role="main" class="ui-content">
                <h2><?=$gst?>:</h2>
                <p><?=$gwe?></p>
            </div> <!-- /content -->	
        </div> <!-- /page -->

    </body>
</html>



<?php
require_once ("rr_config_gen.php");  // generic defines
require_once ("rr_config_func.php"); // tools functions

$gwe1 =$_REQUEST['message'];
$gwe2 =$_REQUEST['_dberror'];
?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("Error") ?>
</head>
    <body>
    
        <div id="page-error" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home" data-i18n="_home">Home</a>
				<h1><?=$gst?></h1>
            </div> <!-- /header -->	
            <div data-role="content" >   
                <h2 data-i18n="_error">Error</h2>
                <p data-i18n="<?= $gwe1 ?>"></p>
                <p data-i18n="<?= $gwe2 ?>"></p>
            </div> <!-- /content -->	
        </div> <!-- /page -->

    </body>
</html>



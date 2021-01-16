<?php

function sfLiLlag()    { 
	$s="";foreach(afLocale() as $k=>$v){ 
		$s .= '<li><a href="./home?lang='. $k .'" data-ajax="false"><img src="'.$v['flag'].'" alt="'.$v['lang'].'" class="ui-li-icon ui-corner-none">'.$v['lang'].'</a></li>';
	} 
	return $s;
}

?>
<!DOCTYPE html> 
<html><head>
<?= sfHead(_e("Home")) ?>
<style>

</style>
</head><body>
<div id="p-home" data-role="page" data-theme="b">
    <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
        <h1><?=_e('Home')?></h1>
        <a href="#p-home-panel" class="ui-btn-right ui-link ui-btn ui-icon-bars ui-btn-icon-notext"><?=_e('i.home.menu')?></a>
    </div> <!-- /header --> 
    <div data-role="content" >  
    	<div style="text-align:center;margin:5 auto">
            <img style="max-width: 100%" src="<?= C_DEFAULT_SITE_LOGO ?>">
		</div>
		<div class="ui-corner-all custom-corners">
        	<div class="ui-bar ui-bar-b "><h3><?= C_CONFIG_SITE_NAME ?></h3></div>
        <!--div class="ui-body ui-body-b"></div-->
        </div>
        <?= $sfHomeLinks() ?>
    </div> <!-- /content --> 
	<div data-role="panel" id="p-home-panel" data-display="reveal" data-position="right"> 
		<ul data-role="listview" data-filter="false" class="flags16">
			<li><a href="#" class="ui-btn ui-btn-icon-left ui-icon-back" data-rel="close">   <?=_e('i.idx.close')?></a></li>
			<li><a href="info" class="ui-btn ui-btn-icon-left ui-icon-info">                 <?=_e('i.idx.help')?></a></li>
			<li><a href="login" rel="external" class="ui-btn ui-btn-icon-left ui-icon-power"><?=_e('i.idx.logout')?></a></li>
			<li data-role="list-divider"><?= _e("Available Languages") ?></li>
			<?= sfLiLlag() ?>
		</ul>
	</div> 
</div> <!-- /page-sets -->
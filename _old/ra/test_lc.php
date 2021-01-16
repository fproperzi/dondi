<?php

echo '<br>', locale_get_display_language('af','af_ZA');

foreach( glob(__DIR__."/locale/*",GLOB_ONLYDIR) as $v){ 
    $l = basename($v);
    echo '<br>',$l,', ',strtolower(substr($l,0,2)),', ', ucfirst(locale_get_display_name($l,$l)) ;
    
}

echo '<br>',locale_get_display_name('GB_WLS');
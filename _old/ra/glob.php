<?php





$d = glob(__DIR__."/locale/*",GLOB_ONLYDIR);


echo '<pre>',__DIR__,print_r($d,true),'</pre>';
echo '<pre>',print_r($_SERVER,true),'</pre>';

function get_current_url() {
  $url  = 'http' . ($_SERVER['HTTPS'] == 'on' ? 's' : '') . '://'
        . $_SERVER['SERVER_NAME']
        . ($_SERVER['SERVER_PORT'] !== 80  ? ':' . $_SERVER['SERVER_PORT'] : '')
        . $_SERVER['REQUEST_URI'];
  return $url;
}

echo '<hr>';
echo '<br>',$_SERVER['REQUEST_URI'];
echo '<br>',$_SERVER['PHP_SELF'];
echo '<br>',rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
echo '<br>',dirname($_SERVER['REQUEST_URI']);
echo '<br>',rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
echo '<br>','"//'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['REQUEST_URI']), '/\\').'/login"';
echo '<br>','//'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo '<pre>',print_r(resourcebundle_locales (''),true),'</pre>';


?>
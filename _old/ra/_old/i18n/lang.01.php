<?php
//http://stackoverflow.com/questions/2236668/file-get-contents-breaks-up-utf-8-characters
function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

$l = !empty($_GET['l']) ? $_GET['l'] : "en";

$f = __DIR__ . "/$l.json"; 
$d = __DIR__ . "/en.json";  //default 
 
//echo $f; exit; 

header('Content-Type: application/javascript');
echo "(function ( $,window ) { $.data(window,'i18n-l',";

if(file_exists($f)) echo file_get_contents_utf8($f);
else				echo file_get_contents_utf8($d);

//if(file_exists($f)) readfile($f);  // problem [é]  char became [?]   
//else                readfile($d);

echo "); }( jQuery,window ));";

?>
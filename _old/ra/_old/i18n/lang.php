<?php
define("C_CONFIG_i18n_LANG"	,"en");       // default language for application
define("C_LOGIN_USER_LANG"	,"user_lang");

//http://stackoverflow.com/questions/2236668/file-get-contents-breaks-up-utf-8-characters
// letter è in json became ?
function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

/* https://gist.github.com/humantorch/d255e39a8ab4ea2e7005
   	$available_languages = array("zh-cn", "ca", "es", "fr", "af","nl", "sp", "en");
	$default_language = "en"; // a default language to fall back to in case there's no match
    $lang = sfPreferedLanguage($default_language, $available_languages, $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
**/
function sfPreferedLanguage($default_language, $available_languages, $http_accept_language) {
    $available_languages = array_flip($available_languages);
    $langs = array();
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
        list($a, $b) = explode('-', $match[1]) + array('', '');
        $value = isset($match[2]) ? (float) $match[2] : 1.0;
        if(isset($available_languages[$match[1]])) {
            $langs[$match[1]] = $value;
            continue;
        }
        if(isset($available_languages[$a])) {
            $langs[$a] = $value - 0.1;
        }
    }
    if($langs) {
        arsort($langs);
        return key($langs); // We don't need the whole array of choices since we have a match
    } else {
        return $default_language;
    }
}
//--------------------------------------------------------
/* get best language from witch are present in directory 
**/
$a = array();
foreach( glob("*.json") as $l)   // get all language files 
	$a[] = basename($l,".json"); //['en','it','fr']

// preferred from session or browser	
$l = !empty($_SESSION[C_LOGIN_USER_LANG]) ? $_SESSION[C_LOGIN_USER_LANG] : $_SERVER["HTTP_ACCEPT_LANGUAGE"];
$l = sfPreferedLanguage(C_CONFIG_i18n_LANG, $a, $l);
$f = __DIR__ . "/$l.json"; 

 
//echo $f; exit; 

header('Content-Type: application/javascript');
echo "(function ( $,window ) { $.data(window,'i18n-l',";
echo file_get_contents_utf8($f);
echo "); }( jQuery,window ));";

?>
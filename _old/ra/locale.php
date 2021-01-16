<?php
// use sessions
session_start();

define("C_LC_DEFAULT"   ,"it_IT");
define("C_LC_FOLDER"    ,"locale");
define("C_LC_DOMAIN1"   ,"main");
define("C_LC_DOMAIN2"   ,"test");
define("C_LC_ENCODING"  ,"UTF-8");

function afDirLang(){ $a=[];$d=__DIR__."/".C_LC_FOLDER."/";foreach( glob("$d*",GLOB_ONLYDIR) as $v){ $v=str_replace($d,"",$v);$a[$v]=$v; } return $a;} 
function afBrwLang(){ global $_SERVER;$a=[];$b=explode(",", @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    foreach($b as $l) {
         preg_match ("/^(([a-zA-Z]+)(-([a-zA-Z]+)){0,1})(;q=([0-9.]+)){0,1}/" , $l, $matches );
         $m = str_replace("-","_",$matches[1]);
         if (!$matches[6]) $a[$m] = 1;
         else              $a[$m] = $matches[6];
    }
    return $a;
}
function afDBLang() {$d=array_keys(afDirLang());$b=array_keys(afBrwLang()); return array_intersect($d,$b);}
function sfDBLang() {$a=afDBLang();if(count($a)==0) return C_LC_DEFAULT; else return reset($a);}


function sfPreferedLanguage() {
    $available_languages = afDirLang();
    $langs = array();
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower(@$_SERVER['HTTP_ACCEPT_LANGUAGE']), $matches, PREG_SET_ORDER);
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
        return C_LC_DEFAULT;
    }
}

// get language preference
if (isset($_GET["lang"])) {
    $language = $_GET["lang"];
}
else if (isset($_SESSION["user_lang"])) {
    $language  = $_SESSION["user_lang"];
}
else {
    $language = sfPreferedLanguage() ;
}

echo '<pre>Browser:', print_r( afBrwLang(),true), '</pre>';
echo '<pre>Dir:', print_r( afDirLang(),true), '</pre>';

//echo '<pre>Intersect:', print_r( afDBLang(),true);
echo '<br>_GET["lang"]:',$_GET["lang"] ;
echo '<br>_SESSION["user_lang"]:',@$_SESSION["user_lang"] ;
echo '<br>Preferred:',sfPreferedLanguage() ;
echo '<br>lang:',$language;
echo '<br>locale_get_default:', locale_get_default();
echo '<br>locale_accept_from_http:',locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

echo '<br>locale_lookup:',locale_lookup(afDirLang(), locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), true, C_LC_DEFAULT);

// save language preference for future page requests
$_SESSION["user_lang"]  = $language;

//putenv("LANG=" . $language); 
echo '<br>setlocale.return:',setlocale(LC_ALL, $language);
echo '<br>',date('l jS \of F Y h:i:s A');


echo "<h1>",bindtextdomain(C_LC_DOMAIN1, C_LC_FOLDER),"</h1>"; 
bind_textdomain_codeset(C_LC_DOMAIN1, C_LC_ENCODING);
echo "<h1>",bindtextdomain(C_LC_DOMAIN2, C_LC_FOLDER),"</h1>"; 
bind_textdomain_codeset(C_LC_DOMAIN2, C_LC_ENCODING);

textdomain(C_LC_DOMAIN1);



echo '<hr>';
echo '<br>',_("hoker");
echo '<br>',dgettext(C_LC_DOMAIN1,"waiting please");
echo '<br>',dgettext(C_LC_DOMAIN2,"waiting please");
echo '<br>',dgettext(C_LC_DOMAIN2,"Let’s make the web multilingual.");
echo '<br>',_("We connect developers and translators around the globe on Lingohub for a fantastic localization experience.");
echo '<br>',sprintf(dgettext(C_LC_DOMAIN2,'Welcome back, %1$s! Your last visit was on %2$s'), 'kino', date('l'));


// ngettext() is used when the plural form of the message is dependent on the count
$i= 1;echo '<br>',sprintf(ngettext("%d page read.", "%d pages read.", $i),$i); //outputs a form used for singular
$i=15;echo '<br>',sprintf(ngettext("%d page read.", "%d pages read.", $i),$i); //outputs a form used when the count is 15

echo '<hr>',setlocale(LC_ALL, 0);
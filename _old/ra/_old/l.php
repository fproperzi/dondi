<?php

define("C_LC_DEFAULT"   			,"it_IT");
define("C_LC_FOLDER"    			,"locale");		// xgettext -k_e --from-code=UTF-8 --omit-header --package-name=RugbyAssistant --package-version=3.10 -s -j -o ./locale/main.pot *.php
define("C_LC_DOMAIN1"    			,"main");		// msgfmt main.po -o main-`date +%s`.mo    -> https://www.php.net/manual/en/function.gettext.php
define("C_LC_ENCODING"  			,"UTF-8");


function afLanguages()   { $a=[];foreach( afLocale() as $k=>$v){$a[$k] = $v['lang'];} return $a;}
function afLocale()      { 
        $a=[];foreach( glob(__DIR__."/".C_LC_FOLDER."/*",GLOB_ONLYDIR) as $v){ 
            $v = basename($v);
            if(extension_loaded( 'intl' )) {
                $l = ucfirst(locale_get_display_name($v,$v)); // language //locale_get_display_language
                $f = strtolower(locale_get_region($v));           // flag
            }
            else {
                $l = strtolower(substr($v,0,2));
                $f = strtolower(substr($v,-2));
            }
            $a[$v] = array('lang' => $l, 'flag' => "img/flags/$f.png");
        } 
        return $a;
} 

//https://gist.github.com/humantorch/d255e39a8ab4ea2e7005
function sfPreferedLocale() {
   // echo "aafLocale:<pre>",print_r((afLocale()),true),"</pre>";
    $available_languages = array_keys(afLocale()); 
    
     echo "available_languages:<pre>",print_r($available_languages,true),"</pre>";
    $langs = array();
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', @$_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches, PREG_SET_ORDER);
    foreach($matches as $match) {
        
        $m = str_replace("-","_",$match[1]);
        $v = isset($match[2]) ? (float) $match[2] : 1.0;
        list($a, $b) = explode('_', $m) + array('', '');
        
        echo "<pre>m=$m,in=".in_array($m, $available_languages).",v=$v,a=$a,in=".in_array($a, $available_languages).",b=$b</pre>";
         
        /***/if(in_array($m, $available_languages)) $langs[$m] = $v;
        else if(in_array($a, $available_languages)) $langs[$a] = $v - 0.1;
    }
    echo "langs:<pre>",print_r($langs,true),"</pre>";
    if($langs) {
        arsort($langs);
        return key($langs); // We don't need the whole array of choices since we have a match
    } else {
        return C_LC_DEFAULT;
    }
}

$locale = sfPreferedLocale();


    print "<h3>Site available in:</h3><pre>";
	print_r(array_keys(afLocale()));
	
	print "</pre>\n<h3>Browser supported languages:</h3><pre>";
	print_r(explode(',',  ($_SERVER["HTTP_ACCEPT_LANGUAGE"])));
	
	
	print "</pre>\n<h3>site will display in: <em>".$locale."</em></h3>";


?>
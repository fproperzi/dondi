<?php

define("C_DIR_IMG_USERS"            ,"img/users");   //is writable?
function aofUserPhoto() { 
    $a = glob(dirname(__FILE__)."/". C_DIR_IMG_USERS ."/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE);
    array_walk($a, function (&$v,$k,$n) { $v = substr($v, $n ); },strlen( dirname(__FILE__))+1);
    return $a;
}

$f = aofUserPhoto();
print_r($f);
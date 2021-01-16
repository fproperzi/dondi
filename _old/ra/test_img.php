<?php
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ALL"				,"id" => 0xFFFFFF	);	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ANONYMOUS"		,"id" => 0x000000	,"xxx" => "Anonymous");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST"			,"id" => 0x000001	,"txt" => "Guest");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST_2"		    ,"id" => 0x000002	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST_4"		    ,"id" => 0x000004	,"xxx" => "** not in use");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ADMIN"			,"id" => 0x000008	,"txt" => "**Admin**");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER"			,"id" => 0x0000F0	,"xxx" => "Player");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_FWD"		,"id" => 0x000010	,"txt" => "Player Forwards");  // forwards-avanti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_BCK"		,"id" => 0x000020	,"txt" => "Player Backs");     // backs-trequarti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_LEADER"	,"id" => 0x000040	,"txt" => "Player Leader");    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_SKIPPER"	,"id" => 0x000080	,"txt" => "Player Skipper");    

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF"			,"id" => 0x00FF00	,"xxx" => "Staff");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_TECNICAL"  ,"id" => 0x000F00	,"xxx" => "Staff Core");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_MANAGER"	,"id" => 0x000100	,"txt" => "Manager");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_COACH"		,"id" => 0x000200	,"txt" => "Coach");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_TRAINER"	,"id" => 0x000400	,"txt" => "Trainer");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_REFEREE"	,"id" => 0x000800	,"txt" => "Referee");	

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_MEDICAL"	,"id" => 0x00F000	,"xxx" => "Staff Medical");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_DOCTOR"	,"id" => 0x001000	,"txt" => "Doctor");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_PHYSIO"	,"id" => 0x002000	,"txt" => "Physio");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_4"			,"id" => 0x004000	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_8"			,"id" => 0x008000	,"xxx" => "** not in use");
	                                                                                    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR"			,"id" => 0x0F0000	,"xxx" => "Editor");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CORE"		,"id" => 0x010000	,"txt" => "Editor Core");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CLIPS"	,"id" => 0x020000	,"txt" => "Editor Clips");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_STATS"	,"id" => 0x040000	,"txt" => "Editor Stats");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_TEST"		,"id" => 0x080000	,"txt" => "Editor Surveys");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_FREE_1"		    ,"id" => 0x100000	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_FREE_2"	        ,"id" => 0x200000	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_FREE_4"	        ,"id" => 0x400000	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_FREE_8"		    ,"id" => 0x800000	,"xxx" => "** not in use");



// images from dir C_DIR_IMG_USERS
function aofUserPhoto1() { 
    $a = glob(__DIR__ ."/img/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE);
    array_walk($a, function (&$v,$k,$n) { $v = basename($v); });
    return $a; 
}

function aofUserPhoto() { 
    $a = [];foreach( glob(__DIR__ ."/img/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE) as $v ) $a[]=basename($v); return $a ;
}
function aofLang()       { $a=[];foreach( glob("i18n/*.json") as $v){ $v=basename($v,".json");$a[$v]=$v; } return $a;} 
function aofUserLevel()  { $a=[];foreach($GLOBALS['gsUsrLevel'] as $d) if(!empty($d['txt'])) $a[$d['id']] = $d['txt']; return $a;} 


echo "<pre>",print_r(aofUserPhoto(),true),"</pre>";
echo "<pre>",print_r(aofLang(),true),"</pre>"; 
echo "<pre>",print_r(aofUserLevel(),true),"</pre>";

//echo "<pre>global=",print_r( $GLOBALS ,true),"</pre>";
?>
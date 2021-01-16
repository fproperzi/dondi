<?php
ini_set('display_errors', 1); 
/* generic set
**/
setlocale(LC_ALL , "ita");
date_default_timezone_set("Europe/Rome");
session_start(); 
 
define("C_ABOUT_GENERAL_TITLE" 		,"RR - Rugby Repository");
define("C_ABOUT_GENERAL_VERSION"	,"Ver. 2.10");
define("C_ABOUT_GENERAL_GITHUB"	    ,"https://github.com/fproperzi/rugbyassistant");
define("C_ABOUT_GENERAL_BY"			,"Fkino Properzi");
define("C_ABOUT_GENERAL_BY_FB"		,"http://www.facebook.com/fproperzi");
define("C_ABOUT_GENERAL_LOGO"		,"img/rr.png");

define("C_LOGIN_USER_AGENT"	        ,"user_agent"   );
define("C_LOGIN_USER_SECRET"	    ,"user_secret"  );  // secret key from application installation 
define("C_LOGIN_USER_ID"	        ,"user_id"      );  
define("C_LOGIN_USER_NAME"	        ,"user_name"    );  define("C_LOGIN_USER_NAME_REGEXP"	,"/^[A-Za-z\d_]{4,20}$/");      define("C_LOGIN_USER_NAME_HELP" ,"At least 4, max 20 letters, numbers, underscores, no spaces");
define("C_LOGIN_USER_PWD"	    	,"user_pwd"     );  define("C_LOGIN_USER_PWD_REGEXP"	,"/^[A-Za-z\d_!@#$%]{5,20}$/"); define("C_LOGIN_USER_PWD_HELP"  ,"At least 5, max 20 letters, numbers, special characters (!@#$%_) or their combination");
define("C_LOGIN_USER_KEY"	        ,"user_key"     );  define("C_LOGIN_USER_KEY_REGEXP"    ,"/^\w{8}-(\w{4}-){3}\w{12}$/"); // unique guid, https://eval.in/640974
define("C_LOGIN_USER_CTIME"	        ,"user_ctime"   );  // cokie expire timestamp: time() === strtotime( date("Y-m-d H:i:s") )
define("C_LOGIN_USER_LEVEL"	        ,"user_level"   );  // check C_LOGIN_USER_LEVEL_XXX
define("C_LOGIN_USER_LANG"	        ,"user_lang"    );  // 2 letter lang used by i18n
define("C_LOGIN_USER_BANNED"	    ,"user_banned"  );
define("C_LOGIN_REMEMBER"			,"user_remember");  // remember me by cookies
define("C_LOGIN_EXPIRE"		        ,"+9 days"	    );  // 9 days expire

define("C_DEFAULT_ADMIN_NAME"	    ,"admin"	    );  // root,admin,administrator...
define("C_DEFAULT_CLIPS_PATH"	    ,"../mgl"	    );
define("C_DEFAULT_CLIPS_PATTERN"	,"/.+\.mp4$/"	);
define("C_DEFAULT_USER_IMG"         ,"img/anonymous.png");

define("C_ERROR_CONNECTION"         ,"Couldn't make connection.");
define("C_ERROR_DATABASE"           ,"Couldn't select database.");
define("C_ERROR_NOT_ALLOWED"        ,"You are not allowed to access this area.");

define("C_FILE_LOGIN"               ,"usr_login.php");
define("C_FILE_LOGOUT"              ,"usr_logout.php");
define("C_FILE_ERROR"				,"rr_error.php");

define("C_FILE_CONFIG_DEF"          ,"rr_config_def.php");
define("C_FILE_CONFIG_DEF_EDIT"     ,"rr_config_def_edit.php");
define("C_FILE_CONFIG_FUNC"         ,"rr_config_func.php");

define("C_DIR_IMG_GENERIC"          ,"img");
define("C_DIR_IMG_OTHER"            ,"img/photo");   
define("C_DIR_IMG_USERS"            ,"img/users");   //is writable?


define("C_REGEXP_ASCII"             ,"/^[[:graph:][:space:]]*$/"); // from 32 to 126
define("C_REGEXP_NAME"	            ,"/^[A-Za-z\d_]{4,20}$/");                        define("C_REGEXP_NAME_HELP" ,"At least 4, max 20 letters, numbers, underscores, no spaces");
define("C_REGEXP_PWD"	            ,"/^[A-Za-z\d_!@#$%]{5,20}$/");                   define("C_REGEXP_PWD_HELP"  ,"At least 5, max 20 letters, numbers, special characters (!@#$%_) or their combination");
define("C_REGEXP_GUID"              ,"/^\w{8}-(\w{4}-){3}\w{12}$/");
define("C_REGEXP_KEY"               ,"/^[A-Za-z\d_\-]+$/");
define("C_REGEXP_PHOTO"             ,"~^data:image/\w+;base64,[a-zA-Z0-9+/\\=]*$~"); 
//define("C_REGEXP_PHOTO"             ,"~^data:image/\w+;base64,.*$~"); 
define("C_REGEXP_TEL"               ,"/^\+?[\d ]{3,20}/");                            define("C_REGEXP_TEL_HELP"   ,"Any international code es. +39, numbers"  );
define("C_REGEXP_URL"				,"/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i");

// or empty or gived regexp
function sfEmptyOrRegExp($r) {return substr_replace($r, "^$|", 1, 0);} 

/* user levels
   bit operator 1,2,4,8,16,32,64,128
**/
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ALL"				,"id" => 0xFFFFFF	);	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ANONYMOUS"		,"id" => 0x000000	,"xxx" => "Anonymous");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST"			,"id" => 0x000001	,"txt" => "Guest");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST_PLUS"		,"id" => 0x000002	,"txt" => "Guest Plus");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ADMIN"			,"id" => 0x000008	,"txt" => "**Admin**");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER"			,"id" => 0x0000F0	,"xxx" => "Player");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_FWD"		,"id" => 0x000010	,"txt" => "Player Forwards");  // forwards-avanti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_BCK"		,"id" => 0x000020	,"txt" => "Player Backs");     // backs-trequarti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_LEADER"	,"id" => 0x000030	,"txt" => "Player Leader");    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_SKIPPER"	,"id" => 0x000040	,"txt" => "Player Skipper");    

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



/* create define from array
**/
foreach($gsUsrLevel as $d) 
	define($d['def'] ,$d['id']);


?>
<?php
define("C_ABOUT_GENERAL_TITLE" 		,"RR - Rugby Repository");
define("C_ABOUT_GENERAL_VERSION"	,"Ver. 2.10");
define("C_ABOUT_GENERAL_GITHUB"	    ,"https://github.com/fproperzi/rugbyassistant");
define("C_ABOUT_GENERAL_BY"			,"Fkino Properzi");
define("C_ABOUT_GENERAL_FB"			,"http://www.facebook.com/fproperzi");
define("C_ABOUT_GENERAL_LOGO"		,"img/rr.png"); 

define("C_DEFAULT_i18n_LANG"	    ,defined("C_CONFIG_i18n_LANG")  ? C_CONFIG_i18n_LANG  : "en"  );
define("C_DEFAULT_LOCALE"	        ,defined("C_CONFIG_LOCALE")     ? C_CONFIG_LOCALE     : "it_IT.utf8"  );
define("C_DEFAULT_TIME_ZONE"	    ,defined("C_CONFIG_TIME_ZONE")  ? C_CONFIG_TIME_ZONE  : "Europe/Rome" );  // check for date.... https://eval.in/764533
define("C_DEFAULT_DB_SQLITE3"       ,defined("C_CONFIG_DB_SQLITE3") ? C_CONFIG_DB_SQLITE3 : "db/ra.sqlite3"); // database
define("C_DEFAULT_CLIPS_PATH"       ,defined("C_CONFIG_CLIPS_PATH") ? C_CONFIG_CLIPS_PATH : "video");         // video clips
define("C_DEFAULT_ADMIN_NAME"	    ,defined("C_CONFIG_ADMIN_NAME") ? C_CONFIG_ADMIN_NAME : "admin" );        // root,admin,administrator...

/* generic set as soon as I can
**/
/* @@@ not in production */ error_reporting(E_ALL); 
/* @@@ not in production */ ini_set('display_errors', 1); 
setlocale(LC_ALL , C_DEFAULT_LOCALE);
date_default_timezone_set(C_DEFAULT_TIME_ZONE);  
session_start();  


define("C_DEFAULT_i18n_PATH"	    ,"i18n"	);
define("C_DEFAULT_IMG_PATH"         ,"img"  );

define("C_DEFAULT_ERROR_LOG"        ,"db/error_log" );
define("C_DEFAULT_CLIPS_PATTERN"	,"/.+\.mp4$/"	);
define("C_DEFAULT_USER_IMG"         ,"img/anonymous.png");
define("C_DEFAULT_ALGORITHM"        ,"sha256"); //'sha256','sha384','sha512'

define("C_DIR_IMG_GENERIC"          ,"img");
define("C_DIR_IMG_OTHER"            ,"img/photo");   
define("C_DIR_IMG_USERS"            ,"img/users");   //is writable?

define("CORE_LK_LANG"               ,"language"     );  // lookup core table
define("CORE_LK_PLAYER_ROLE"        ,"player-role"  );
define("CORE_LK_USER_STATUS"        ,"user-status"  );  // 0:ready, 1:pending, 2:banned

define("C_LOGIN_USER_AGENT"	        ,"user_agent"   );  // used by $_SESSION
define("C_LOGIN_USER_SECRET"	    ,"user_secret"  );  // secret key from application installation 
define("C_LOGIN_USER_ID"	        ,"user_id"      );  
define("C_LOGIN_USER_NAME"	        ,"user_name"    );  
define("C_LOGIN_USER_PWD"	    	,"user_pwd"     );  
define("C_LOGIN_USER_KEY"	        ,"user_key"     );  
define("C_LOGIN_USER_CTIME"	        ,"user_ctime"   );  // cokie expire timestamp: time() === strtotime( date("Y-m-d H:i:s") )
define("C_LOGIN_USER_LEVEL"	        ,"user_level"   );  // check C_LOGIN_USER_LEVEL_XXX
define("C_LOGIN_USER_LANG"	        ,"user_lang"    );  // 2 letter lang used by i18n
define("C_LOGIN_USER_STATUS"	    ,"user_status"  );  // user status  0:ready, 1:pending, 2:banned
define("C_LOGIN_REMEMBER"			,"user_remember");  // remember me by cookies
define("C_LOGIN_EXPIRE"		        ,"+9 days"	    );  // 9 days expire

/*    
      sign-up --> email-confirm --> your-email-confirmation --> waiting-admin-activation
	  sign-in --> you-are-banned
	  sign-in --> you-are-inactive --> email-confirm --> your-email-confirmation --> waiting-admin-activation 
	  sign-in --> you-are-pending --> wait-Admin-activation
	  sign-in --> you-are-active --> free-to-view
**/
define("C_LOGIN_USER_STATUS_ACTIVE"	  ,0 );				// user is free to watch
define("C_LOGIN_USER_STATUS_PENDING"  ,1 );				// user is registered, mail is confirmed, but need Admin approvation
define("C_LOGIN_USER_STATUS_INACTIVE" ,2 );				// user is registered, waiting mail confirmation
define("C_LOGIN_USER_STATUS_BANNED"	  ,3 );				// user is locked for bad abits or other reason


define("C_FILE_LOGOUT"              ,"usr_logout.php");
define("C_FILE_ERROR"				,"rr_error.php");

define("C_FILE_CONFIG_DEF"          ,"rr_config_def.php");
define("C_FILE_CONFIG_DEF_EDIT"     ,"rr_config_def_edit.php");
define("C_FILE_CONFIG_FUNC"         ,"rr_config_func.php");

define("C_PAGE_ERROR"				,"rr_error.php"); 
define("C_PAGE_INDEX"        		,"index.php"); 
define("C_FILE_LOGIN"               ,"usr_login.php");
define("C_PAGE_LOGIN_SIGNIN"        ,"usr_login.php#page-usr-signin");
define("C_PAGE_LOGIN_SIGNUP"        ,"usr_login.php#page-usr-signup");
define("C_PAGE_LOGIN_SIGNOUT"       ,"usr_login.php#page-usr-signout");
define("C_PAGE_LOGIN_FORGOT"        ,"usr_login.php#page-usr-forgot");
define("C_PAGE_LOGIN_RESET"         ,"usr_login.php#page-usr-reset");
define("C_PAGE_LOGIN_PROFILE"       ,"usr_login.php#page-usr-profile");
define("C_PAGE_LOGIN_PENDING"       ,"usr_login.php#page-usr-pending");
define("C_PAGE_LOGIN_BANNED"        ,"usr_login.php#page-usr-locked");
define("C_PAGE_LOGIN_CONFIRM"       ,"usr_login.php#page-usr-confirm");
define("C_PAGE_LOGIN_LANDING"       ,"usr_login.php#page-usr-landing");


define("C_REGEXP_USER_NAME"	        ,"/^[A-Za-z\d_]{4,20}$/");       // A-Z,a-z,0-9 min 4 max 20 chars
define("C_REGEXP_USER_PWD"	        ,"/^[A-Za-z\d_!@#$%]{5,20}$/");  // A-Z,a-z,0-9 _!@#$%  min 5 max 20 chars
define("C_REGEXP_USER_KEY"          ,"/^\w{8}-(\w{4}-){3}\w{12}$/"); // unique guid, https://eval.in/640974
define("C_REGEXP_ASCII"             ,"/^[[:graph:][:space:]]*$/"); // from 32 to 126
define("C_REGEXP_GENERIC_KEY"       ,"/^[A-Za-z\d_\-]+$/"); //letters, numbers, _-
define("C_REGEXP_PHOTO"             ,"~^data:image/\w+;base64,[a-zA-Z0-9+/\\=]*$~"); 
//define("C_REGEXP_PHOTO"             ,"~^data:image/\w+;base64,.*$~"); 
define("C_REGEXP_TEL"               ,"/^\+?[\d ]{3,20}/");                          
define("C_REGEXP_URL"				,"/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i");
define("C_REGEXP_DIR_FILE"          ,'~^[a-zA-Z]:\\(((?![<>:"/\\|?*]).)+((?<![ .])\\)?)*$~'    );  //http://stackoverflow.com/questions/24702677/regular-expression-to-match-a-valid-absolute-windows-directory-containing-spaces


/* user levels
   bit operator 1,2,4,8,16,32,64,128
**/
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ALL"				,"id" => 0xFFFFFF	);	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ANONYMOUS"		,"id" => 0x000000	,"xxx" => "level.anonymous");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST"			,"id" => 0x000001	,"txt" => "level.guest");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_SECRETARY"	    ,"id" => 0x000002	,"txt" => "level.secretary");        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_LIAISON"		    ,"id" => 0x000004	,"txt" => "level.liaison-officer");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ADMIN"			,"id" => 0x000008	,"txt" => "level.admin");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER"			,"id" => 0x0000F0	,"xxx" => "level.player");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_FWD"		,"id" => 0x000010	,"txt" => "level.player-1");  // forwards-avanti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_BCK"		,"id" => 0x000020	,"txt" => "level.player-2");     // backs-trequarti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_LEADER"	,"id" => 0x000040	,"txt" => "level.player-3");    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_SKIPPER"	,"id" => 0x000080	,"txt" => "level.player-4");    

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF"			,"id" => 0x00FF00	,"xxx" => "level.staff");

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_TECNICAL"  ,"id" => 0x000F00	,"xxx" => "level.staff-1");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_MANAGER"	,"id" => 0x000100	,"txt" => "level.manager");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_COACH"		,"id" => 0x000200	,"txt" => "level.coach");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_TRAINER"	,"id" => 0x000400	,"txt" => "level.trainer");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_REFEREE"	,"id" => 0x000800	,"txt" => "level.referee");	

$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_MEDICAL"	,"id" => 0x00F000	,"xxx" => "level.staff-2");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_DOCTOR"	,"id" => 0x001000	,"txt" => "level.doctor");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_PHYSIO"	,"id" => 0x002000	,"txt" => "level.physio");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_4"			,"id" => 0x004000	,"xxx" => "niu");				//niu="** not in use"
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF_8"			,"id" => 0x008000	,"xxx" => "niu");
	                                                                                    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR"			,"id" => 0x0F0000	,"xxx" => "level.staff-3");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CORE"		,"id" => 0x010000	,"txt" => "level.editor-core");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CLIPS"	,"id" => 0x020000	,"txt" => "level.editor-clips");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_STATS"	,"id" => 0x040000	,"txt" => "level.editor-stats");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_TEST"		,"id" => 0x080000	,"txt" => "level.editor-surveys");

/* create define from array
**/
foreach($gsUsrLevel as $d)  
	define($d['def'] ,$d['id']);
unset($d);

?>
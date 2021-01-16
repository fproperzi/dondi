<?php
define("C_FILE_CONFIG_DEF"			,"config_def.php");

if(file_exists(C_FILE_CONFIG_DEF)) include C_FILE_CONFIG_DEF;
require_once ("config_func.php"); 

define("C_ABOUT_GENERAL_TITLE" 		,"Rugby Assistant");
define("C_ABOUT_GENERAL_VERSION"	,"Ver. 3.10");
define("C_ABOUT_GENERAL_GITHUB"	  ,"https://github.com/fproperzi/rugbyassistant");
define("C_ABOUT_GENERAL_BY"			  ,"f.properzi");
define("C_ABOUT_GENERAL_FB"			  ,"http://www.facebook.com/fproperzi");
define("C_ABOUT_GENERAL_LOGO"		  ,"img/ra.png"); 

define("C_LC_DEFAULT"   			    ,"it_IT");
define("C_LC_FOLDER"    			    ,"locale");		// xgettext -k_e --from-code=UTF-8 --omit-header --package-name=RugbyAssistant --package-version=3.10 -s -j -o ./locale/main.pot *.php
define("C_LC_DOMAIN1"    			    ,"main");		// msgfmt main.po -o main-`date +%s`.mo    -> https://www.php.net/manual/en/function.gettext.php
define("C_LC_ENCODING"  			    ,"UTF-8");


define("C_DEFAULT_SITE_NAME"		  ,defined("C_CONFIG_SITE_NAME")		? C_CONFIG_SITE_NAME		  : C_ABOUT_GENERAL_TITLE);
define("C_DEFAULT_SITE_LOGO"		  ,defined("C_CONFIG_SITE_LOGO")		? C_CONFIG_SITE_LOGO		  : C_ABOUT_GENERAL_LOGO);
define("C_DEFAULT_SECRET"			    ,defined("C_CONFIG_SECRET")			  ? C_CONFIG_SECRET			    : "" );					      // important!! must edit config!!
		
define("C_DEFAULT_LOCALE"	        ,defined("C_CONFIG_LOCALE") 		  ? C_CONFIG_LOCALE   		  : C_LC_DEFAULT  );
define("C_DEFAULT_TIME_ZONE"	    ,defined("C_CONFIG_TIME_ZONE")		? C_CONFIG_TIME_ZONE		  : "Europe/Rome" );  	// check for date.... https://eval.in/764533
	
define("C_DEFAULT_CLIPS_PATH"   	,defined("C_CONFIG_CLIPS_PATH") 	? C_CONFIG_CLIPS_PATH		  : "./video");       	// video clips
define("C_DEFAULT_CLIPS_PATTERN"	,defined("C_CONFIG_CLIPS_PATTERN")? C_CONFIG_CLIPS_PATTERN	: "/.+\.mp4$/");    	// video clips
define("C_DEFAULT_ADMIN_LOGIN"		,defined("C_CONFIG_ADMIN_LOGIN") 	? C_CONFIG_ADMIN_LOGIN		: "admin" );        	// root,admin,administrator...
define("C_DEFAULT_ADMIN_EMAIL"		,defined("C_CONFIG_ADMIN_EMAIL")	? C_CONFIG_ADMIN_EMAIL		: "info@rugbyassistant.org");
define("C_DEFAULT_MAIL_ON_ERROR"	,defined("C_CONFIG_MAIL_ON_ERROR")? C_CONFIG_MAIL_ON_ERROR	: true );
define("C_DEFAULT_DB_SQLITE3"     	,defined("C_CONFIG_DB_SQLITE3") 	? C_CONFIG_DB_SQLITE3		  : "db/ra.sqlite3"); 	// database

/* generic set as soon as I can
**/
/* @@@ not in production */ error_reporting(E_ALL); 
/* @@@ not in production */ ini_set('display_errors', 1); 
//session_save_path(getcwd().'/tmp');
session_start(); 
vfInitialize_i18n();// --- locale choice
date_default_timezone_set(C_DEFAULT_TIME_ZONE);  
 
define("C_DEFAULT_VALUE_AI"			  ,-1);				        //default value auto increment in insert (es.: user_id)
define("C_DEFAULT_ERROR_LOG"      ,"db/error_log" );
define("C_DEFAULT_USER_IMG"       ,"img/anonymous.png");
define("C_DEFAULT_ALGORITHM"      ,"sha256");         //'sha256','sha384','sha512'

define("C_DIR_IMG_GENERIC"        ,"img");
define("C_DIR_IMG_OTHER"          ,"img/photo");   
define("C_DIR_IMG_USERS"          ,"img/users");      //is writable?
define("C_FILE_USER_IMG"          ,"img/user.png");

define("CORE_LK_LANG"             ,"language"     );  // lookup core table
define("CORE_LK_PLAYER_ROLE"      ,"player-role"  );
define("CORE_LK_USER_STATUS"      ,"user-status"  );  // 0:ready, 1:pending, 2:banned

define("C_LOGIN_USER_EXPIRE"		  ,"+9 days");  	    // 9 days expire
define("C_LOGIN_COOKIE_TIME_OUT"	, 10);

/* user levels
   bit operator 1,2,4,8,16,32,64,128
**/
$gsUsrLevel = array(
    
      array("def" => "C_LOGIN_USER_LEVEL_ALL"				        ,"id" => 0xFFFFFF		
    ),array("def" => "C_LOGIN_USER_LEVEL_ANONYMOUS"		      ,"id" => 0x000000	,"___" => _e("level.anonymous")
    ),array("def" => "C_LOGIN_USER_LEVEL_GUEST"			        ,"id" => 0x000001	,"txt" => _e("level.guest")
    ),array("def" => "C_LOGIN_USER_LEVEL_SECRETARY"	        ,"id" => 0x000002	,"___" => _e("level.secretary")      
    ),array("def" => "C_LOGIN_USER_LEVEL_LIAISON"		        ,"id" => 0x000004	,"___" => _e("level.liaison-officer")

    ),array("def" => "C_LOGIN_USER_LEVEL_ADMIN"			        ,"id" => 0x000008	,"txt" => _e("level.admin")

    ),array("def" => "C_LOGIN_USER_LEVEL_PLAYERS"			      ,"id" => 0x0000F0	,"___" => _e("level.player")
    ),array("def" => "C_LOGIN_USER_LEVEL_PLAYER_FWD"		    ,"id" => 0x000010	,"txt" => _e("level.player-forward")
    ),array("def" => "C_LOGIN_USER_LEVEL_PLAYER_BCK"		    ,"id" => 0x000020	,"txt" => _e("level.player-back")
    ),array("def" => "C_LOGIN_USER_LEVEL_PLAYER_LEADER"	    ,"id" => 0x000040	,"___" => _e("level.player-3")  
    ),array("def" => "C_LOGIN_USER_LEVEL_PLAYER_SKIPPER"	  ,"id" => 0x000080	,"___" => _e("level.player-4") 
    
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF"			        ,"id" => 0x00FF00	,"___" => _e("level.staff.groups-1-2")
    
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_TECNICAL"    ,"id" => 0x000F00	,"___" => _e("level.staff.group-1")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_MANAGER"	    ,"id" => 0x000100	,"txt" => _e("level.manager")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_COACH"		    ,"id" => 0x000200	,"txt" => _e("level.coach")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_TRAINER"	    ,"id" => 0x000400	,"txt" => _e("level.trainer")	
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_REFEREE"	    ,"id" => 0x000800	,"txt" => _e("level.referee")	
    
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_MEDICAL"	    ,"id" => 0x00F000	,"___" => _e("level.staff.group-2")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_DOCTOR"	    ,"id" => 0x001000	,"txt" => _e("level.doctor")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_PHYSIO"	    ,"id" => 0x002000	,"txt" => _e("level.physio")
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_4"			      ,"id" => 0x004000	,"___" => _e("level.niu")				//niu="** not in use"
    ),array("def" => "C_LOGIN_USER_LEVEL_STAFF_8"			      ,"id" => 0x008000	,"___" => _e("level.niu")
                                                                            
    ),array("def" => "C_LOGIN_USER_LEVEL_EDITOR"			      ,"id" => 0x0F0000	,"___" => _e("level.staff.group-3")
    ),array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CORE"		    ,"id" => 0x010000	,"txt" => _e("level.editor-core")
    ),array("def" => "C_LOGIN_USER_LEVEL_EDITOR_CLIPS"	    ,"id" => 0x020000	,"___" => _e("level.editor-clips")
    ),array("def" => "C_LOGIN_USER_LEVEL_EDITOR_STATS"	    ,"id" => 0x040000	,"___" => _e("level.editor-stats")
    ),array("def" => "C_LOGIN_USER_LEVEL_EDITOR_TEST"		    ,"id" => 0x080000	,"___" => _e("level.editor-surveys")
    )
);

/* create define from array
**/
foreach($gsUsrLevel as $d)  
	define($d['def'] ,$d['id']);
unset($d);

/* router!!
**/
$gaHomeLinks = array(

  array("uri" => ""									,"div" => _e("Clips")					        ,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => ""				          ,"url" => "p_home.php"				        ,"___" => _e("Home")		    		      ,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "index.php"		    ,"url" => "p_home.php"			          ,"___" => _e("Home") 		    	      	,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "home"				      ,"url" => "p_home.php"			          ,"___" => _e("Home") 		    		      ,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "login"			      ,"url" => "p_login.php"			          ,"___" => _e("Login")		    		      ,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "logout"			      ,"url" => "p_login.php"			          ,"___" => _e("Logout")		    		    ,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "help"		    	    ,"url" => "p_help.php"				        ,"___" => _e("i.idx.help")	    		  ,"usr" => C_LOGIN_USER_LEVEL_ALL

),array("uri" => "clipsxteam"		    ,"url" => "p_clipsxteam.php"		      ,"txt" => _e("i.idx.clipsXteam")    	,"usr" => C_LOGIN_USER_LEVEL_ALL 
),array("uri" => "clipsxmatch"	    ,"url" => "p_clipsxmatch.php"	        ,"txt" => _e("i.idx.clipsXmatch")   	,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "clipsxplayer"	    ,"url" => "p_clipsxplayer.php"	      ,"txt" => _e("i.idx.clipsXplayer")   	,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "library"   		    ,"url" => "p_library.php"			        ,"txt" => _e("i.idx.clips_library") 	,"usr" => C_LOGIN_USER_LEVEL_ALL
),array("uri" => "stats"   		      ,"url" => "p_stats.php"			          ,"txt" => _e("i.idx.stats")         	,"usr" => C_LOGIN_USER_LEVEL_ALL

/* links personali 
),array("uri" => ""														,"div" => "Personal links"	,"usr" => C_LOGIN_USER_LEVEL_ALL
*/
),array("uri" => ""									,"div" => _e("i.idx.tools_editors")		,"usr" => C_LOGIN_USER_LEVEL_EDITOR
),array("uri" => "edit_reviews"	    ,"url" => "p_edit_library.php"	      ,"txt" => _e("i.idx.edit_library")		,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CLIPS
),array("uri" => "edit_reviews"	    ,"url" => "p_edit_attributes.php"	    ,"txt" => _e("i.idx.edit_attributes")	,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CLIPS	
),array("uri" => "edit_events"	    ,"url" => "p_edit_events.php"	        ,"txt" => _e("i.idx.edit_events")		  ,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CORE
),array("uri" => "edit_matches"	    ,"url" => "p_edit_matches.php"	      ,"txt" => _e("i.idx.edit_matches")   	,"usr" => C_LOGIN_USER_LEVEL_EDITOR_STATS
),array("uri" => "edit_teams"	      ,"url" => "p_edit_teams.php"	        ,"txt" => _e("i.idx.edit_teams")		  ,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CORE
),array("uri" => "edit_players" 	  ,"url" => "p_edit_players.php"	      ,"txt" => _e("i.idx.edit_players")  	,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CORE
),array("uri" => "edit_referee"	    ,"url" => "p_edit_referee.php"	      ,"txt" => _e("i.idx.edit_referee")		,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CORE
),array("uri" => "edit_tags"	      ,"url" => "p_edit_tags.php"	    	    ,"txt" => _e("i.idx.edit_tags")	    	,"usr" => C_LOGIN_USER_LEVEL_EDITOR_CORE
),array("uri" => "edit_stats"	      ,"url" => "p_edit_stats.php"	        ,"txt" => _e("i.idx.edit_stats")	    ,"usr" => C_LOGIN_USER_LEVEL_EDITOR_STATS

),array("uri" => ""								  ,"div" => _e("i.idx.tools_admin")		  ,"usr" => C_LOGIN_USER_LEVEL_ADMIN
),array("uri" => "users"			      ,"url" => "p_users.php"		    	      ,"txt" => _e("i.idx.edit_users")		  ,"usr" => C_LOGIN_USER_LEVEL_ADMIN
),array("uri" => "share"		        ,"url" => "p_sharing.php"			        ,"txt" => _e("i.idx.edit_sharing") 	 	,"usr" => C_LOGIN_USER_LEVEL_ADMIN
),array("uri" => "dbadmin"		      ,"url" => "p_dbadmin.php"		          ,"txt" => _e("i.idx.edit_DB")	   		  ,"usr" => C_LOGIN_USER_LEVEL_ADMIN
),array("uri" => "config"			      ,"url" => "p_edit_config.php"	        ,"txt" => _e("i.idx.configuration")		,"usr" => C_LOGIN_USER_LEVEL_ADMIN
)
);

if(empty(C_DEFAULT_SECRET)) { 
    include("p_edit_config.php"); // frist installation?
    exit;
}

$sfHomeLinks = function() use($gaHomeLinks) {  // closure! https://stackoverflow.com/questions/1065188/in-php-what-is-a-closure-and-why-does-it-use-the-use-identifier
	$s = '<div data-role="collapsibleset"  data-iconpos="right">';
	//$s = '<ul data-role="listview" data-filter="false">';
	$nextId = 1;
	$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\').'/';

	foreach($gaHomeLinks as $a) {
		if (isUserLevel($a["usr"]) || isUserLevelAdmin()) {
			//if (!empty($a['div'])) $s .= '<li data-role="list-divider">'. $a["div"] .'</li>';
			if (!empty($a['div'])) {
				if($nextId > 1) $s .= '</ul></div>';
				$s .= '<div data-role="collapsible" id="menu-set'.$nextId.'" data-collapsed="'.($nextId > 1?'true':'false').'" data-iconpos="left">';
				$s .= '<h3>'.$a["div"].'</h3><ul data-role="listview" data-filter="false">';
				$nextId++;
			}
			if (!empty($a['txt'])) $s .= '<li><a href="'. $base . $a["uri"] .'" rel="external" data-ajax="false">'. $a["txt"] .'</a></li>';
		}
	}
	if($nextId > 1) $s .= '</ul></div>';
	$s .= '</div>';
	//$s .= '</ul>';
	return $s;
};
/* test 
$a = array('user_id'=>'1','user_name'=>'admin','user_lang'=>'it_IT','user_level'=>C_LOGIN_USER_LEVEL_ADMIN);
if(empty($_SESSION['user_id'])) vfSetSession($a);
**/
if(bfLogme()) 
  $uri = "login";                                   // non logged do login!
else {        
	$dir = dirname($_SERVER["SCRIPT_NAME"])."/";
	$uri = explode('?', $_SERVER['REQUEST_URI'], 2);
	$uri = str_replace($dir,"",$uri[0]);
}

  $url="/404.php";                                  // page not found
  foreach($gaHomeLinks as $a) {
    if(!empty($a['url'])  &&  file_exists($a['url']) && $uri == $a['uri'] ) { 
      if($a['usr'] == C_LOGIN_USER_LEVEL_ALL || isUserLevel($a['usr']) || isUserLevelAdmin()) // è accessibile da questo utente
        $url = $a['url']; 
      else $url = "/403.php";	                                // forbidden: not allowed
      break;
    }
  }



if(true) {
	require __DIR__ ."/".$url;
} else { //test router 
/* -- nginx redirect: /etc/nginx/defaul.d/ra.conf
** location /ra/ { try_files $uri $uri/ /ra/index.php?$args; }
** -- apache redirect: /ra/.htaccess
** RewriteEngine On
** RewriteCond %{REQUEST_FILENAME} !-d
** RewriteCond %{REQUEST_FILENAME} !-f
** RewriteRule ^(.+)$ index.php [QSA,L]
*/
echo "<br>__DIR__:",__DIR__;
echo "<br>uri:",$uri;
echo "<br>url:",$url;
//echo '<br>locale:',$locale;
echo '<br>dir:',dirname($_SERVER["PHP_SELF"]);
echo '<br>base:','http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\').'/';
echo '<pre>_REQUEST:',print_r($_REQUEST,true),'</pre>';
echo '<pre>_SERVER:',print_r($_SERVER,true),'</pre>';
}
?> 

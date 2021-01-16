<?php
/* on error ghange header and view (if set)
**/
function vfGo2Header($sFile,$sErr="") {
	header("Location: $sFile" .(empty($sErr)?"":"?err=$sErr"));
	exit();
}
/* check user level given for current user
**/
function isUserLevel($user_level) {	
	return (!empty($_SESSION[C_LOGIN_USER_LEVEL]) && ($_SESSION[C_LOGIN_USER_LEVEL] & $user_level) > 0);
	//return (($user_level & $_SESSION[C_LOGIN_USER_LEVEL]) == $_SESSION[C_LOGIN_USER_LEVEL]);
}
/* current user is admin?
**/
function isUserLevelAdmin() { 
    return (($_SESSION[C_LOGIN_USER_LEVEL] & C_LOGIN_USER_LEVEL_ADMIN) == C_LOGIN_USER_LEVEL_ADMIN); 
}
/* which user level?
   0x030000 = Manager, Coach
**/
function sfUserLevel($v)       { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt']) && ($d['id'] & $v) ) $a[] = $d['txt']; return join(", ",$a);}  // all names level
function sfUserLevel2($v)      { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['xxx']) && ($d['id'] & $v) ) $a[] = $d['xxx']; elseif ( $d['id']<0x000300  && ($d['id'] & $v) ) $a[] = $d['txt']; return join(", ",$a);}  // all names level
function aofSplitUserLevel($v) { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt']) && ($d['id'] & $v) ) $a[] = strval($d['id']); return $a;}  // flag 2 array
function sfMergeUserLevel($a)  { $v=0;  foreach($a as $i) $v |= intval($i); return $v; }


/* Protects pages to only logged users.                     
   request level to protect                                                        
**/
function page_protect($req_user_level = C_LOGIN_USER_LEVEL_ALL) {

    $req_user_level |= C_LOGIN_USER_LEVEL_ADMIN;  // admin go everywhere

    if ($user_agent != md5($_SERVER['HTTP_USER_AGENT'])) vfLogOut(); // Secure against Session Hijacking by checking user agent 
	
    if (isset($_SESSION[C_LOGIN_USER_ID]) && isset($_SESSION[C_LOGIN_USER_KEY])) {  // validate session with key and time
	
        $user_id  = filter_input(INPUT_SESSION ,C_LOGIN_USER_ID  ,FILTER_VALIDATE_INT);
        $user_key = filter_input(INPUT_SESSION ,C_LOGIN_USER_KEY ,FILTER_SANITIZE_STRING);
        $b4Cookie = false;

        // all ok go on
    }
    elseif (isset($_COOKIE[C_LOGIN_USER_ID]) && isset($_COOKIE[C_LOGIN_USER_KEY]))  {
       
        $user_id  = filter_input(INPUT_COOKIE  ,C_LOGIN_USER_ID  ,FILTER_VALIDATE_INT);
		$user_key = filter_input(INPUT_COOKIE  ,C_LOGIN_USER_KEY ,FILTER_SANITIZE_STRING);
        $b4Cookie = true;
        
    }
    else vfLogOut();
    
    // always check unique user_key and expire
    $rs = mysql_query("SELECT user_name,user_level,user_lang,user_ctime FROM t_users WHERE user_banned=0 AND user_id='$user_id' AND user_key='$user_key'");
    if($rs) list($user_name,$user_level,$user_lang,$user_ctime) = mysql_fetch_row($rs);     // get info for user
    if(empty($user_ctime) || strtotime($user_ctime . C_LOGIN_EXPIRE) < time() ) vfLogOut(); // check expired
    
    if ($b4Cookie) {  // if only cookies, set session
    
        session_regenerate_id(); //against session fixation attacks.
        
        $_SESSION[C_LOGIN_USER_ID    ] = $user_id;
        $_SESSION[C_LOGIN_USER_KEY   ] = $user_key;
        $_SESSION[C_LOGIN_USER_NAME  ] = $user_name;
        $_SESSION[C_LOGIN_USER_LEVEL ] = $user_level;
        $_SESSION[C_LOGIN_USER_LANG  ] = empty($user_lang) ? false : true;
        $_SESSION[C_LOGIN_USER_AGENT ] = md5($_SERVER['HTTP_USER_AGENT']);
    
    }

	if (!isUserLevel($req_user_level)) vfGo2Header(C_FILE_ERROR,C_ERROR_NOT_ALLOWED);
	else @mysql_query("INSERT INTO t_users_access (user_id,dd,count) values ($user_id,now(),1) ON DUPLICATE KEY UPDATE count=count+1"); // my count access
}
/* user Log In 
   @ return false on logged
   @ return error-string on error 
**/
function bfLogIn() {

    $user_name = filter_input (INPUT_POST ,C_LOGIN_USER_NAME     ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_NAME_REGEXP)) );
    $user_pwd  = filter_input (INPUT_POST ,C_LOGIN_USER_PASSWORD ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_PWD_REGEXP)) ); 
	$bRemember = filter_input (INPUT_POST ,C_LOGIN_REMEMBER      ,FILTER_VALIDATE_BOOLEAN);
	
	if(empty($user_name)) return "Invalid user name";
    if(empty($user_pwd))  return "Invalid password";
    
    $user_pwd = sfPwdHash($user_pwd,substr($user_pwd,0,9));
	
	$rs = mysql_query("SELECT user_id,user_level,user_lang,user_banned FROM t_users WHERE user_name='$user_name' and user_pwd='$user_pwd'");
    if($rs) list($user_id,$user_level,$user_lang,$user_banned) = mysql_fetch_row($rs);
	
    if(empty($user_id))       return "Invalid login";
    elseif($user_banned == 1) return "User banned";
	else {

		$user_key   = sfGuidV4();
		$user_ctime = strtotime(C_LOGIN_EXPIRE);
		
		$rs = mysql_query("UPDATE t_users SET user_ctime='$user_ctime',user_key='$user_key' WHERE user_id='$user_id'");
		if(!$rs) return "Error in db write: ".mysql_error();
		
		session_regenerate_id (true); //prevent against session fixation attacks.
		
        $_SESSION[C_LOGIN_USER_ID    ] = $user_id;  
        $_SESSION[C_LOGIN_USER_KEY   ] = $user_key;
		$_SESSION[C_LOGIN_USER_NAME  ] = $user_name;
		$_SESSION[C_LOGIN_USER_LEVEL ] = $user_level;
		$_SESSION[C_LOGIN_USER_LANG  ] = $user_lang;
		$_SESSION[C_LOGIN_USER_AGENT ] = md5($_SERVER['HTTP_USER_AGENT']);
		
		if($bRemember) {
			setcookie(C_LOGIN_USER_NAME	,$user_name	,$user_ctime, "/");        
			setcookie(C_LOGIN_USER_KEY	,$user_key	,$user_ctime, "/");
			setcookie(C_LOGIN_USER_ID	,$user_id	,$user_ctime, "/");
		}
		return false;
	}
}
/* user Log Out 
**/
function vfLogOut() {
	
	if (!empty( $_SESSION[C_LOGIN_USER_ID] ) ) {
		$user_id = $_SESSION[C_LOGIN_USER_ID];
		@mysql_query("UPDATE t_users SET user_ctime=0,user_key='' WHERE user_id='$user_id'");
	}
	
	/** Delete the sessions****************/
	unset($_SESSION[C_LOGIN_USER_ID   ]
		 ,$_SESSION[C_LOGIN_USER_NAME ]
		 ,$_SESSION[C_LOGIN_USER_LEVEL]
		 ,$_SESSION[C_LOGIN_USER_LANG ]
		 ,$_SESSION[C_LOGIN_USER_KEY  ]
		 ,$_SESSION[C_LOGIN_USER_AGENT]
	);
	
	session_unset();
	session_destroy();
	
	/* Delete the cookies*******************/
	setcookie(C_LOGIN_USER_ID       ,null ,-1);
	setcookie(C_LOGIN_USER_NAME     ,null ,-1);
	setcookie(C_LOGIN_USER_KEY      ,null ,-1);
	
	vfGo2Header(C_FILE_LOGIN);
} 
/* header for files
example:

    <!DOCTYPE html> 
    <html><head>
    
    <?= sfHead("clips play",array("js/jquery.cookie.js","js/jquery.fileDownload.js"),array("css/table.css")) ?>
   
    <style>
      // page style
    </style>
    <script type="text/javascript">
      // page script
    </script>
    
    </head><body>

    </body>
    </html>

    <meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link type="text/css" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" rel="stylesheet"  />  
<!-- jquery -->
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<!-- jquery mobile -->
<script type="text/javascript" src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> 
    
**/
function sfHead($sTitle,$ajs=array(),$acss=array()) {
	
                                      $h = '<title>'.htmlentities($sTitle).'</title><meta charset="utf-8" />';
                                      $h .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
     /** jquery css   **/             $h .= '<link type="text/css" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" rel="stylesheet"/>';

    foreach($acss as $v)              $h .= '<link type="text/css" href="'.$v.'" rel="stylesheet"/>';
    
	$g = @constant('C_CONFIG_ANALYTICS');
	
    if(!empty($g))    				  $h .= '<script type="text/javascript" src="//www.google-analytics.com/ga.js" async></script>';
    /** jquery !    **/               $h .= '<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>';
    /** underscore  **/               $h .= '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>';
    /** my tools js **/               $h .= '<script type="text/javascript" src="js/tools.js"></script>';
   
    if(!empty($_SESSION[C_LOGIN_USER_NAME])) $u = $_SESSION[C_LOGIN_USER_NAME];
    else                                     $u = "Anonymous";
    
    if(!empty($g))    				  $h .= '
<script>
var _gaq = _gaq || [],_usr="'.$u.'";
_gaq.push(["_setAccount", "'.$g.'"]);  
_gaq.push(["_trackPageview"]);
_gaq.push(["_setCustomVar",1,"User",_usr,2]);
try { _gaq.push(["_trackEvent",_usr,"page."+document.title,window.location.pathname ]); } catch(err) {}	
</script>';
    
    foreach($ajs  as $v)              $h .= '<script type="text/javascript" src="'.$v.'"></script>';
    /** jquery mobile ! **/           $h .= '<script type="text/javascript" src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>';
	
	return $h;
}

/* cut string at len o a space near the len given 
**/
function sfChopStr($str, $len){
	if (strlen($str) < $len) return $str;
	$str = substr($str, 0, $len);
	if ($spc_pos = strrpos($str, " "))
        $str = substr($str, 0, $spc_pos);
	
	return $str . "...";
}
/* Sort chars in string
**/
function sfSortStr($str) {
	$a = str_split($str);
	sort($a);
	return implode('', $a); 
}
/* encode string ... simple
   codegolf: implode('%',array_map('bin2hex',str_split($a)));
    better! : implode(array_map(function($i){return "%".bin2hex($i);},str_split($a)));
    utils:
    <script language="javascript"> 
        document.write( unescape( '<?= sfEncodeStr( $str ); ?>' ) ); 
    </script>
**/
function sfEncodeStr($s) { 
    for($l = strlen($s), $t = "", $i = 0; $i < $l; $i++) 
        $t .= '%' . bin2hex($s[$i]); 
    return $t; 
} 
/* initials from name: "Progress in Veterinary-Science" => PVS
   http://stackoverflow.com/a/16165234/889949
**/
function sfInitials($string) {
    preg_match_all('/(?<=\b)[A-Z]/', $string, $matches);
    $result = implode('', $matches[0]);
    return  strtoupper($result);

    return  $result;
}
/* acronimus
**/
function sfAcron($s,$n=3) {
  return  substr(str_ireplace(array('a','e','i','o','u',' '), '', $s) ,0,$n);
}
/* is the browser from mobile?
   old: return preg_match('/(blackberry|iphone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))? TRUE : FALSE;
**/
function isMobile() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    return (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)));
}
/* check num of rows from sql                     
**/                                                       
function bfCheckSqlRows($sSql) {
	return (mysql_num_rows(mysql_query($sSql)) >0);
}
/* button for admin 
example:
sfButton4Admin("clips.highlights.edit.php","edit","Edit")
**/
function sfButton4Admin($php,$icon,$txt) {
    return (isUserLevelAdmin() ? '<a href="$php" data-ajax="false" data-role="button" data-icon="$icon">$txt</a>' : '');
}
/* Password and salt generation
   check pwd from db:  if ($pwd4DB === sfPwdHash($pass4Form,substr($pwd4DB,0,9))) 
**/
function sfPwdHash($pwd, $salt = null, $salt_length = 9) {
	
	if ($salt === null) $salt = substr(md5(uniqid(rand(), true)), 0, $salt_length);
	else 		        $salt = substr($salt, 0, $salt_length);
	return $salt . sha1($pwd . $salt);
}
/* generate password
**/
function sfGenPwd3($length = 7){
	$password = "";
	$possible = "0123456789bcdfghjkmnpqrstvwxyz"; //no vowels, no l(confusing I)
	
	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);		
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}	
	}	
	return $password;		
}
/*  http://stackoverflow.com/questions/307486/short-unique-id-in-php
 **/
function sfGenPwd2($l=7){
    return substr(str_shuffle("0123456789bcdfghjkmnpqrstvwxyz"), 0, $l);
}
/* pronunciable password (italian stile ... no 'hkwqj') : zobi345
   accetable entropia: https://eval.in/651878
**/
function sfGenPwd(){
    srand ((double)microtime()*1000000);
    
    $c = "bcdfglmnprstvz";  
    $v = "aeiou";
  
    $pwd = rand(100,999);
    
    for($i=0; $i<4; $i+=2) {
        $pwd .= $v[rand(0, strlen($v)-1)];
        $pwd .= $c[rand(0, strlen($c)-1)];
    }
    return strrev($pwd);
}
/*
  Returns a GUIDv4 string
 
  @param bool $trim
  @return string
**/
function sfGuidV4 ($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        if ($trim === true)
            return trim(com_create_guid(), '{}');
        else
            return com_create_guid();
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace.
              substr($charid,  0,  8).$hyphen.
              substr($charid,  8,  4).$hyphen.
              substr($charid, 12,  4).$hyphen.
              substr($charid, 16,  4).$hyphen.
              substr($charid, 20, 12).
              $rbrace;
    return $guidv4;
}
<?php
/* db connection, prepare, execute: https://phpdelusions.net/pdo/pdo_wrapper
# Table creation
	DB::query("CREATE temporary TABLE pdowrapper (id int auto_increment primary key, name varchar(255))");
# Prepared statement multiple execution
	$stmt = DB::prepare("INSERT INTO pdowrapper VALUES (NULL, ?)");
	foreach (['Sam','Bob','Joe'] as $name)	{
		$stmt->execute([$name]);
	}
# Getting rows in a loop
	$stmt = DB::run("SELECT * FROM pdowrapper");
	while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
	}
# Getting one row 
	$id  = 1;
	$row = DB::run("SELECT * FROM pdowrapper WHERE id=?", [$id])->fetch();
# Getting single field value
	$name = DB::run("SELECT name FROM pdowrapper WHERE id=?", [$id])->fetchColumn();
	var_dump($name);
# Getting array of rows
	$all = DB::run("SELECT name, id FROM pdowrapper")->fetchAll(PDO::FETCH_KEY_PAIR);
# Update
	$new = 'Sue';
	$stmt = DB::run("UPDATE pdowrapper SET name=? WHERE id=?", [$new, $id]);
**/
class DB {
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    public static function instance() {
        if (self::$instance === null) {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
            );
            //$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            //self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
            self::$instance = new PDO("sqlite:".dirname(__FILE__)."/db/ra.sqlite3");
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args) {
        return call_user_func_array(array(self::instance(), $method), $args); //Call the self::instance->method($args) 
    }

    public static function run($sql, $args = []) {
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}
/* work around to pass array in exceptions 
 * throw new raException( 'login.invalid-user-pwd','page.login' )
**/
class raException extends \Exception {
    private $_options;

    public function __construct($message, $goto="", $code = 0, Exception $previous = null)     {
        parent::__construct($message, $code, $previous);
        $this->_options = [
            'message' => $message,
            'goto' => $goto
        ];
    }
    public function getOptions() { return $this->_options; }
}
/* json response for exception or exit
**/
function sfjRes($message,$status=false,$action="") {
    $r = [
         'status' => $status
        ,'action' => $action
    ];
    if(is_array($message))  array_merge($r,$message);
    else                    $r['message'] = $message;
    
    return($r);
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
     /** jquery css **/  //$h .= '<link type="text/css" rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />';
     /** jquery css **/  $h .= '<link type="text/css" rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css" />';
     /** rr     css **/  $h .= '<link type="text/css" rel="stylesheet" href="css/rr.css" />';

    foreach($acss as $v) $h .= '<link type="text/css" href="'.$v.'" rel="stylesheet"/>';
    
   
	$g = @constant('C_CONFIG_ANALYTICS'); 
	
    if(!empty($g))    	 $h .= '<script type="text/javascript" src="//www.google-analytics.com/ga.js" async></script>';
    /** jquery !    **/  $h .= '<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>';
    /** underscore  **/  $h .= '<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>';

    
    if(!empty($_SESSION[C_LOGIN_USER_LANG])) $l = $_SESSION[C_LOGIN_USER_LANG];  //extract langs from lk_core
    else                                     $l = sfPreferedLanguage(C_DEFAULT_i18n_LANG, ["en","fr"], strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
    $l = "?l=". /*urlencode*/(C_DEFAULT_i18n_PATH ."/$l.json") /*."&_=".time()*/;
    
    /** my tools js **/  $h .= '<script type="text/javascript" src="js/rr.js'.$l.'"></script>'; 
        
    //if(@constant('C_CONFIG_LANG_INPROGRESS') == "1") $l .= "?_=".time();  
    //var data = JSON.parse($("#i18n-lang").html());
    
    /** i18n        **/  //$h .= '<script id="i18n-lang" type="application/json" src="i18n/'.$l.'.json?_='.time().'"></script>';
   
    if(!empty($_SESSION[C_LOGIN_USER_NAME])) $u = $_SESSION[C_LOGIN_USER_NAME];
    else                                     $u = "Anonymous"; 
    
    if(!empty($g))    	 $h .= '
<script>
var _gaq = _gaq || [],_usr="'.$u.'";
_gaq.push(["_setAccount", "'.$g.'"]);  
_gaq.push(["_trackPageview"]);
_gaq.push(["_setCustomVar",1,"User",_usr,2]);
try { _gaq.push(["_trackEvent",_usr,"page."+document.title,window.location.pathname ]); } catch(err) {}	
</script>';
    
    foreach($ajs  as $v) $h .= '<script type="text/javascript" src="'.$v.'"></script>';
    /** jqm**/           //$h .= '<script type="text/javascript" src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>';
    /** jqm**/           $h .= '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>';

	return $h;
}
/* or empty or gived regexp
**/
function sfEmptyOrRegExp($r) {return substr_replace($r, "^$|", 1, 0);} 
/* on error ghange header and view (if set)
**/
function vfGo2Header($file,$err="") {
    $err = urlencode($err);
	header("Location: $file" .(empty($err)?"":"?err=$err"));
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
//function sfUserLevel2($v)      { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['xxx']) && ($d['id'] & $v) ) $a[] = $d['xxx']; elseif ( $d['id']<0x000300  && ($d['id'] & $v) ) $a[] = $d['txt']; return join(", ",$a);}  // all names level
function aofSplitUserLevel($v) { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt']) && ($d['id'] & $v) ) $a[] = strval($d['id']); return $a;}  // flag 2 array
function sfMergeUserLevel($a)  { $v=0;  foreach($a as $i) $v |= intval($i); return $v; }


/* Protects pages to only logged users.                     
   request level to protect                                                        
**/
function page_protect($req_user_level = C_LOGIN_USER_LEVEL_ALL) {

    $req_user_level |= C_LOGIN_USER_LEVEL_ADMIN;  // admin go everywhere
    
    /*  $a = ['status' => true, 'message' => "login.logged"]
        $a = ['status' => false, 'message' => $e->getMessage()]
        chech if user is logged 
    **/
    $a = afSignIn();
    
    /* check if user_level is the one requested
    **/
    if ($a['status'] && !isUserLevel($req_user_level)) {
        $a['status']  = false;
        $a['message'] = "login.not-allowed";
    }
    
    if(!$a['status'] && $_POST['ajax'] == 'on') {    // exit with json
        $a['goto'] = C_FILE_ERROR;                  // suggestion page goto
        echo  json_encode($a);
        exit();
    }
    elseif (!$a['status']) {
        //vfGo2Header(C_FILE_ERROR,$a['message'] ));  // exit calling onother page
        header("Location: ". C_FILE_ERROR ."?message=".urlencode($a['message']));
        exit();
    }
}

/*  object to token and viceversa
**/
function o2t($o) { return rtrim(strtr(base64_encode(json_encode((object)$o)),'+/','-_'),'='); }
function t2o($t) { return json_decode(base64_decode(strtr($t,'-_','+/')),true); }
// hash string
function s2h($t) { return hash_hmac(C_DEFAULT_ALGORITHM,$t,C_CONFIG_SECRET,false); }
/*
  $token = [
       o2t($_SESSION)
      ,
**/
/* db query

INSERT OR IGNORE INTO visits VALUES ($ip, 0);
UPDATE visits SET hits = hits + 1 WHERE ip LIKE $ip;




insert or ignore into <table>(<primaryKey>, <column1>, <column2>, ...) values(<primaryKeyValue>, <value1>, <value2>, ...); 
update <table> set <column1>=<value1>, <column2>=<value2>, ... where changes()=0 and <primaryKey>=<primaryKeyValue>;
select case changes() WHEN 0 THEN last_insert_rowid() else <primaryKeyValue> end;


on error: message and goto
goto:
    message to admin
    login
    ok,index
*/

function afSignIn() {
    try {
        
        $action = $_POST['action']; 
        
        if($action === 'login') { // from form
            
            $user_email = filter_input (INPUT_POST ,C_LOGIN_USER_NAME     ,FILTER_VALIDATE_EMAIL  );
            $user_name  = filter_input (INPUT_POST ,C_LOGIN_USER_NAME     ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_NAME_REGEXP)) );
            $user_pwd   = filter_input (INPUT_POST ,C_LOGIN_USER_PASSWORD ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_PWD_REGEXP)) ); 
            $bRemember  = filter_input (INPUT_POST ,C_LOGIN_REMEMBER      ,FILTER_VALIDATE_BOOLEAN);
            
            if(empty($user_name) && empty($user_email) )  throw new Exception( "login.invalid-username" ); //empty = false,"",null,0,"0",array()
            if(empty($user_pwd))                          throw new Exception( "login.invalid-password" ); 
            
            //$user_pwd = sfPwdHash($user_pwd,substr($user_pwd,0,9));

            
            $r = DB::run("SELECT * FROM t_users WHERE user_email=? or user_name=?", [$user_email,$user_name])->fetch();
            
            /*
              $hash = password_hash("rasmuslerdorf", PASSWORD_DEFAULT);
              echo $hash;
              print_r(password_get_info ( $hash ));
            **/
            if(empty($r['user_pwd']))                            throw new Exception( "login.invalid-user-pwd" );
            elseif (!password_verify($user_pwd, $r['user_pwd'])) throw new Exception( "login.invalid-password" );

        } // request.login
          // page protect
        else {
            if (isset($_SESSION[C_LOGIN_USER_ID]) && isset($_SESSION[C_LOGIN_USER_KEY])) {  // validate session with key and time
            
                if (empty($_SESSION[C_LOGIN_USER_AGENT]) || $_SESSION[C_LOGIN_USER_AGENT] != md5($_SERVER['HTTP_USER_AGENT'])) 
                    throw new raException( "login.agent-change", C_PAGE_LOGIN_SIGNIN ); // different agent from last session... Session Hijacking?
                
                if (empty($_SESSION[C_LOGIN_USER_SECRET]) || $_SESSION[C_LOGIN_USER_SECRET] != C_CONFIG_SECRET) 
                    throw new raException( "login.secret-change", C_PAGE_LOGIN_SIGNIN ); // different agent from last session... Session Hijacking?
                
                $action = 'session';
        
                $user_id  = filter_input(INPUT_SESSION ,C_LOGIN_USER_ID  ,FILTER_VALIDATE_INT);
                $user_key = filter_input(INPUT_SESSION ,C_LOGIN_USER_KEY ,FILTER_SANITIZE_STRING);
            }
            elseif (isset($_COOKIE[C_LOGIN_USER_ID]) && isset($_COOKIE[C_LOGIN_USER_KEY]))  {
               
                $action = "cookie";
               
                $user_id  = filter_input(INPUT_COOKIE  ,C_LOGIN_USER_ID  ,FILTER_VALIDATE_INT);
                $user_key = filter_input(INPUT_COOKIE  ,C_LOGIN_USER_KEY ,FILTER_SANITIZE_STRING);
            }
            else throw new raException( "login.not-logged", C_PAGE_LOGIN_SIGNIN );
            
            $r = DB::run("SELECT * FROM t_users WHERE user_id=? AND user_key=?",[$user_id,$user_key])->fetch();
            
        }
        
        $user_id     = $r['user_id'];
        $user_status = $r['user_status'];
        $user_key    = $action == "login" ? sfGuidV4()                : $r['user_key'];
        $user_ctime  = $action == "login" ? strtotime(C_LOGIN_EXPIRE) : $r['user_ctime'];
        
        if(empty($user_id))                       throw new Exception( "login.invalid-user" ); 
        if($user_status == C_USER_STATUS_PENDING) throw new raException( "login.pending-user", C_PAGE_LOGIN_PENDING );
        if($user_status == C_USER_STATUS_BANNED)  throw new raException( "login.banned-user" , C_PAGE_LOGIN_BANNED );
        if(empty($user_ctime) || strtotime($user_ctime . C_LOGIN_EXPIRE) < time() ) // check expired, session+cookie
                                                  throw new Exception( "login.expired" ); 
        switch($action) {
        /****/ case "login":
            // key + ctime 
            DB::run("UPDATE t_users SET user_ctime=?,user_key=? WHERE user_id=?", [$user_ctime,$user_key,$user_id]);
            
            if($bRemember) {  
                setcookie(C_LOGIN_USER_KEY	,$user_key	,$user_ctime, "/");
                setcookie(C_LOGIN_USER_ID	,$user_id	,$user_ctime, "/");
            }

        /****/ case "cookie":
        
            session_regenerate_id(); //against session fixation attacks.
    
            $_SESSION[C_LOGIN_USER_ID    ] = $user_id;
            $_SESSION[C_LOGIN_USER_KEY   ] = $user_key;
            $_SESSION[C_LOGIN_USER_NAME  ] = $r['user_name'];
            $_SESSION[C_LOGIN_USER_LEVEL ] = $r['user_level'];
            $_SESSION[C_LOGIN_USER_LANG  ] = $r['user_lang'];
            $_SESSION[C_LOGIN_USER_AGENT ] = md5($_SERVER['HTTP_USER_AGENT']);
            $_SESSION[C_LOGIN_USER_SECRET] = C_CONFIG_SECRET;
            
        
        break; case "session":
            //nop
        }
        
        DB::run("INSERT INTO t_users_access (user_id,dd,count) values ($user_id,now(),1) ON DUPLICATE KEY UPDATE count=count+1"); // my count access
    
        /**************/      return (['status' => true,  'action'=>$action, 'message' => "login.logged"]);
    } 
    catch (PDOException $e) { return (['status' => false, 'action'=>$action, 'message' => "_dberror", "_dberror" => $e->getMessage()]); }  // PDO error
    catch (Exception $e)    { return (['status' => false, 'action'=>$action, 'message' => $e->getMessage()]);                           }  // trhow error
}
/**
 * Check if a string is serialized: http://stackoverflow.com/questions/1369936/check-to-see-if-a-string-is-serialized
 * @param string $string
 */
function is_serial($string) {
    return (@unserialize($string) !== false || $string == 'b:0;');
}


/* user sign-out 
**/
function afSignOut() {
    $action = "logout";
    try {
        if (!empty( $_SESSION[C_LOGIN_USER_ID] ) ) 
            DB::run("UPDATE t_users SET user_ctime=0,user_key='' WHERE user_id=?",[ $_SESSION[C_LOGIN_USER_ID] ]);

        session_unset();
        session_destroy();
        
        setcookie(C_LOGIN_USER_ID       ,null ,-1); // delete cokie
        setcookie(C_LOGIN_USER_KEY      ,null ,-1);
        
/*
http://stackoverflow.com/questions/3989347/php-why-cant-i-get-rid-of-this-session-id-cookie
$params = session_get_cookie_params();
setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
**/
        
        /*******************/ return (['status' => true,  'action'=>$action, 'message' => "login.logout"]);
    } 
    catch (PDOException $e) { return (['status' => false, 'action'=>$action, 'message' => "_dberror", "_dberror" => $e->getMessage()]); }  // PDO error
    catch (Exception $e)    { return (['status' => false, 'action'=>$action, 'message' => $e->getMessage()]);                           }  // trhow error
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


    
/* https://gist.github.com/humantorch/d255e39a8ab4ea2e7005
   	$available_languages = array("zh-cn", "ca", "es", "fr", "af","nl", "sp", "en");
	$default_language = "en"; // a default language to fall back to in case there's no match
    $lang = sfPreferedLanguage($default_language, $available_languages, strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]));
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

?>
	
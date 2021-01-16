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
            self::$instance = new PDO("sqlite:". __DIR__ ."/". C_DEFAULT_DB_SQLITE3,"","",$opt);
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
/* work around to pass message and goto page in exceptions 
   throw new raException( 'login.invalid-user-pwd','page.login' )
   https://eval.in/776580
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
/* ready to json response for exception or exit
	afjRes(true,$action,"login.logged"); == ['status' => true,  'action'=>$action, 'message' => "login.logged"]
	afjRes(true,$action,['message'=>'login.logged','goto'=>'index.php']);
   
    catch (PDOException $e) { return afjRes(false, $action, "{{err.db}}" . $e->getMessage()); }
    catch (Exception $e)    { return afjRes(false, $action, $e->getMessage()); }  
	catch (Exception $e)    { return afjRes(false, $action, ['message'=>$e->getMessage(),'goto'=>'error.php']); } 

**/
function afjRes($status=false,$action="",$message) {
    $r = [
         'status' => $status  //false bad, true good
        ,'action' => $action
    ];
    if(is_array($message))  array_merge($r,$message);
    else                    $r['message'] = $message;
    
    return($r);
}
/* message and goto, used for afjRes( true,$action,afMsgG2('logged','index.php') )
**/
function afMsgG2 ($message,$goto=C_PAGE_INDEX) {
	return array('message'=>$message,'goto'=>$goto);
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
    /** language    **/  $h .= '<script type="text/javascript" src="i18n/lang.php"></script>'; 
    /** my tools js **/  $h .= '<script type="text/javascript" src="js/rr.js"></script>'; 
        
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
/* utils function for choice inputs
**/
function aofPlayerRole() { return array("prop","hoker","lock","back-row","scrum-half","half-backs","backs"); }
function aofNoYes()      { return array(0=>'_no' ,1=>'_yes');} 
function aofLang()       { $a=[];foreach( glob("i18n/*.json") as $v){ $v=basename($v,".json");$a[$v]=$v; } return $a;}   // get all language files 
function aofPhoto($d)    { $a=[];foreach( glob("$d/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE) as $v ) $a[]=basename($v); return $a;} //get all img from $d dir
function aofUserLevel()  { $a=[];foreach($GLOBALS['gsUsrLevel'] as $d) if(!empty($d['txt'])) $a[$d['id']] = $d['txt']; return $a;}       
function aofTimeZones()  { $a=[];foreach(timezone_abbreviations_list() as $t) foreach($t as $z) if(isset($z['timezone_id'])) $a[$z['timezone_id']]=$z['timezone_id']; return $a;}


/* crud section ------------------------------------------------------------------------------------------------------------------------------------------------
*  every table that use crud.php must have 2 fields: update_by, update_on to track updates
**/
//     --long define--                          --short define--
define ("C_ACTION_NONE"    ,0x000 );      define ( "_N" ,C_ACTION_NONE    );
define ("C_ACTION_LIST"    ,0x001 );      define ( "_L" ,C_ACTION_LIST    );   // fields to list under sets, filter field to obtain list
define ("C_ACTION_ORDER"   ,0x002 );      define ( "_O" ,C_ACTION_ORDER   );   // fields to identify record and order field

define ("C_ACTION_2INSERT" ,0x010 );      define ("_2I" ,C_ACTION_2INSERT );   // prepare to insert: witch fields must for insert (external id)
define ("C_ACTION_2UPDATE" ,0x020 );      define ("_2U" ,C_ACTION_2UPDATE );   // prepare to edit: witch fields to identify record
define ("C_ACTION_2DELETE" ,0x040 );      define ("_2D" ,C_ACTION_2DELETE );   // prepare to delete: witch fields to identify record, just a confirm
define ("C_ACTION_2COPY"   ,0x080 );      define ("_2C" ,C_ACTION_2COPY   );   // prepare to copy: witch fields to identify origin 

define ("C_ACTION_INSERT"  ,0x100 );      define ( "_I" ,C_ACTION_INSERT  );   // mandatory fields 
define ("C_ACTION_UPDATE"  ,0x200 );      define ( "_U" ,C_ACTION_UPDATE  );   // fields to identify record ,mandatory fields 
define ("C_ACTION_DELETE"  ,0x400 );      define ( "_D" ,C_ACTION_DELETE  );   // fields to identify record
define ("C_ACTION_COPY"    ,0x800 );      define ( "_C" ,C_ACTION_COPY    );   // fields to identify record, like insert from 2update 

define ("C_ACTION_2PREPARE",C_ACTION_2UPDATE | C_ACTION_2INSERT | C_ACTION_2COPY | C_ACTION_2DELETE); 

define("C_FORM_REQUIRED"                ,"{{crud.err.required}}");  // between {{}} because to add other informations
define("C_FORM_NOT_VALID"               ,"{{crud.err.not-valid}}");
define("C_FORM_OPTION_NOT_VALID"        ,"{{crud.err.option-not-valid}}");
define("C_FORM_ERROR"                   ,"{{crud.err.error}}" );
define("C_FORM_NO_UNIQUE"               ,"{{crud.err.no-unique}}" );

// save image to C_DIR_IMG_USERS return path or false
function sfCropit2Img($dir,$filename,$v)     {
	$d = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v)); 
    $f = $dir ."/".$filename.".png"; 
    $w777 = file_put_contents($f, $d, LOCK_EX);
	return (empty($w777) ? false : $f);  // $w777 false or 0
}
//from action to action
function nfAction2Action($action) {
    switch($action) {
    /****/ case C_ACTION_2UPDATE: return C_ACTION_UPDATE;  // used in js requested action, filter just for action
    break; case C_ACTION_2INSERT: return C_ACTION_INSERT;
    break; case C_ACTION_2DELETE: return C_ACTION_DELETE;
    break; case C_ACTION_2COPY  : return C_ACTION_INSERT;
    break; case C_ACTION_DELETE : 
           case C_ACTION_UPDATE : 
           case C_ACTION_INSERT : 
           case C_ACTION_COPY   : 
           case C_ACTION_LIST   :
           case C_ACTION_ORDER  :
           default              : return $action;
    }
}
//check if err is set
function bfCheck_Err ($args) {foreach($args as $f) if (isset($f['err'])) return true; return false;}
/* check if post values are ok over $action (insert,update,delete...)
   args is gaUpsert
   if you need moore control over error use $fn but ["filter"=>FILTER_CALLBACK,"options"=>function($v){return $v or false;}]
**/
function bfCheck_POST (&$args,$action) { 

    $r = filter_input_array(INPUT_POST, $args);                                                                //global  $dbg;$dbg['filter_input_array'] = $r;
    foreach($args as $k => &$u) {                                                                              // check all is ok
       
		if(isset($u['h'])) $h = ": ".$u['h']; 
        else               $h = "";
			
			//if ( ($r[$k] === false || is_null($r[$k])) && ($u['a'] & $action) )  $e = C_FORM_REQUIRED .": ". @$u['h'];    // no or invalid value but required for action
			if ( empty($r[$k]) && ($u['a'] & $action) && !empty($u['e']) )       $e = $u['e'];                 // no or invalid value but required for action
		elseif ( empty($r[$k]) && ($u['a'] & $action) )                          $e = C_FORM_REQUIRED;         // no or invalid value but required for action
        elseif ( $action & C_ACTION_2PREPARE | C_ACTION_LIST | C_ACTION_ORDER)   $e = false;                   // no matter field in these actions
        elseif ( is_scalar($r[$k]) && $r[$k] === false )            			 $e = C_FORM_NOT_VALID;        // value but not valid
        elseif ( is_array($r[$k]) && in_array(false,$r[$k],true) )               $e = C_FORM_OPTION_NOT_VALID; // array of values but one not valid
        else																	 $e = false;													 
		
        $u['a'] &= nfAction2Action($action);  // from action to action
        
        if($u['filter'] === FILTER_VALIDATE_REGEXP && $u['t'] !='photo') $u['v'] = addslashes($r[$k]);         // cant force magic_quotes and regexp
        else                                                             $u['v'] = $r[$k];                     // put value eventually filtered in $args
        if ($e != false) $u['err'] = $e;                                                                       // put error in $args
        
        if ($u['t'] =='photo') $dbg['photo_b'] = $k; $dbg['photo_c'] = $r[$k];
     
        unset( $u['filter']                                                                                    // neeed no more, clean for ajax return
              ,$u['flags'] 
              ,$u['options']
        );                                                                
    } $dbg['args'] = $args;
    if( $action & (C_ACTION_UPDATE + C_ACTION_INSERT + C_ACTION_COPY) ) vfTrackCU($args);                      // add fields to track action
    return bfCheck_Err ($args) ;   
}
// track update,create: add field with value to append  in update,create
function vfTrackCU(&$avar, $b=false) {
	if($b ) {
		$avar["update_by"] = array("v" => "'". @$_SESSION[C_LOGIN_USER_ID] ."'" );
		$avar["update_on"] = array("v" => "'". date('YmdHis') . "'" );
	}
	else {    
		$avar["update_by"] = array("v" => @$_SESSION[C_LOGIN_USER_ID] );												
		$avar["update_on"] = array("v" => date('YmdHis') );									
	}
}
/* login section ------------------------------------------------------------------------------------------------------------------------------------------------
**/
// check user level given for current user
function isUserLevel($user_level) {	
	return (!empty($_SESSION[C_LOGIN_USER_LEVEL]) && ($_SESSION[C_LOGIN_USER_LEVEL] & $user_level) > 0);
	//return (($user_level & $_SESSION[C_LOGIN_USER_LEVEL]) == $_SESSION[C_LOGIN_USER_LEVEL]);
}
// current user is admin?
function isUserLevelAdmin() { 
    return (($_SESSION[C_LOGIN_USER_LEVEL] & C_LOGIN_USER_LEVEL_ADMIN) == C_LOGIN_USER_LEVEL_ADMIN); 
}
// which user level?   0x030000 = Manager, Coach
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

/*  token create  
**/
function o2t($o) { return rtrim(strtr(base64_encode(json_encode((object)$o)),'+/','-_'),'='); }  // object to token es.: o2t($_SESSION)
function t2o($t) { return json_decode(base64_decode(strtr($t,'-_','+/')),true); }                // token to object es.: foreach(t2o($t) as $k=>$v) $_SESSION[$k]=$v;
function s2h($t) { return hash_hmac(C_DEFAULT_ALGORITHM,$t,C_CONFIG_SECRET,false); }			 // hash string     
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
        
        if($action === 'login') { // from form = request.login
            
            $user_email = filter_input (INPUT_POST ,C_LOGIN_USER_NAME     ,FILTER_VALIDATE_EMAIL  );
            $user_name  = filter_input (INPUT_POST ,C_LOGIN_USER_NAME     ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_NAME_REGEXP)) );
            $user_pwd   = filter_input (INPUT_POST ,C_LOGIN_USER_PASSWORD ,FILTER_VALIDATE_REGEXP ,array("options"=>array("regexp"=>C_LOGIN_USER_PWD_REGEXP)) ); 
            $bRemember  = filter_input (INPUT_POST ,C_LOGIN_REMEMBER      ,FILTER_VALIDATE_BOOLEAN);
            
            if(empty($user_name) && empty($user_email) )  throw new Exception( "login.invalid-username" ); //empty = false,"",null,0,"0",array()
            if(empty($user_pwd))                          throw new Exception( "login.invalid-password" ); 
            
            $r = DB::run("SELECT * FROM t_users WHERE user_email=? or user_name=?", [$user_email,$user_name])->fetch();
            
            if(empty($user_pwd))                           		  throw new Exception( "login.invalid-user-pwd" );  // from password_hash
            elseif (!password_verify($user_pwd, $r['user_hpwd'])) throw new Exception( "login.invalid-password" );  // https://eval.in/777334 
			
			$r['user_key']   = sfGuidV4();						//sign-in new key/ctime
			$r['user_ctime'] = strtotime(C_LOGIN_EXPIRE);
        } 
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
        $user_key    = $r['user_key'];
        $user_ctime  = $r['user_ctime'];
        
            if(empty($user_id))                       		 throw new raException( "login.invalid-user" ,C_PAGE_LOGIN_LOGIN ); 
        elseif($user_status == C_LOGIN_USER_STATUS_PENDING)  throw new raException( "login.pending-user" ,C_PAGE_LOGIN_PENDING );
		elseif($user_status == C_LOGIN_USER_STATUS_INACTIVE) throw new raException( "login.inactive-user",C_PAGE_LOGIN_INACTIVE );
        elseif($user_status == C_LOGIN_USER_STATUS_BANNED)   throw new raException( "login.banned-user"  ,C_PAGE_LOGIN_BANNED );
        elseif(empty($user_ctime) || strtotime($user_ctime . C_LOGIN_EXPIRE) < time() ) // check expired, session+cookie
															 throw new raException( "login.expired"      ,C_PAGE_LOGIN_LOGIN ); 
        switch($action) {
        /****/ case "login":
            // key + ctime 
            DB::run("UPDATE t_users SET user_ctime=?,user_key=? WHERE user_id=?", [$user_ctime,$user_key,$user_id]);
            
            if($bRemember) {  
                setcookie(C_LOGIN_USER_ID	,$user_id	,$user_ctime, "/");
                setcookie(C_LOGIN_USER_KEY	,$user_key	,$user_ctime, "/");
            }
			vfSetSession($r);

        break; case "cookie":
  
			vfSetSession($r);

        break; case "session":
            //nop
        }
		
		$v = ['user_id' => $user_id, 'dd' => date('Ymd')];

		//mysql: INSERT INTO t_users_access (user_id,dd,cnt) values ($user_id,now(),1) ON DUPLICATE KEY UPDATE cnt=cnt+1	
		$stmt = DB::run("UPDATE t_users_access SET cnt=cnt+1 WHERE user_id=:user_id AND dd=:dd", $v);
		if($stmt->rowCount() == 0 )
			$stmt = DB::run("INSERT INTO t_users_access (user_id,dd,cnt) values (:user_id,:dd,1)", $v);


    
        /**************/      return afjRes(true,  $action, "login.logged");
    } 
    catch (PDOException $e) { return afjRes(false, $action, "{{err.db}}" . $e->getMessage()); }  // PDO error
    catch (Exception    $e) { return afjRes(false, $action, $e->getMessage());                }  // trhow error
	catch (raException  $e) { return afjRes(false, $action, $e->getOptions());                }  // trhow error & goto
}
//set session
function vfSetSession($r) { 
	session_regenerate_id(); //against session fixation attacks.

	$_SESSION[C_LOGIN_USER_ID    ] = $r['user_id'];
	$_SESSION[C_LOGIN_USER_KEY   ] = $r['user_key'];
	$_SESSION[C_LOGIN_USER_NAME  ] = $r['user_name'];
	$_SESSION[C_LOGIN_USER_LEVEL ] = $r['user_level'];
	$_SESSION[C_LOGIN_USER_LANG  ] = $r['user_lang'];
	$_SESSION[C_LOGIN_USER_AGENT ] = md5($_SERVER['HTTP_USER_AGENT']);
	$_SESSION[C_LOGIN_USER_SECRET] = C_CONFIG_SECRET;
}

/* user sign-out 
**/
function afSignOut() {
    $action = "logout";
    try {
        if (!empty( $_SESSION[C_LOGIN_USER_ID] ) ) {
            DB::run("UPDATE t_users SET user_ctime=?,user_key=? WHERE user_id=?",[0,"",$_SESSION[C_LOGIN_USER_ID] ]);
		}
        session_unset();
        session_destroy();
        
        setcookie(C_LOGIN_USER_ID       ,null ,-1); // delete cokie
        setcookie(C_LOGIN_USER_KEY      ,null ,-1);
        
/*
http://stackoverflow.com/questions/3989347/php-why-cant-i-get-rid-of-this-session-id-cookie
$params = session_get_cookie_params();
setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
**/

        /**************/      return afjRes(true,  $action, afMsgG2("login.loggedout",C_PAGE_LOGIN_LANDING) );
    } 
    catch (PDOException $e) { return afjRes(false, $action, "{{err.db}}" . $e->getMessage()); }  // PDO error
    catch (Exception    $e) { return afjRes(false, $action, $e->getMessage());                }  // trhow error
}


/* Generic section ------------------------------------------------------------------------------------------------------------------------------------------------
**/
//  afKeyPow( aofPlayerRole() ) =     [1] => prop, [2] => hoker, [4] => lock, [8] => back-row, [16] => scrum-half ....
function afKeyPow($a) {
  $b = [];
  foreach($a as $k => $v) $b[ pow(2,$k) ]=$v;
  return $b;
}    

// Check if a string is serialized: http://stackoverflow.com/questions/1369936/check-to-see-if-a-string-is-serialized
function is_serial($string) {
    return (@unserialize($string) !== false || $string == 'b:0;');
}
// check if vale or array is in keys of onother array, return value or false used in 
function bfInArray($v,$a) {
    if( is_array($v) or ($v instanceof Traversable) ) {  // array of values to check over array of values
        foreach($v as $k)
            if(!array_key_exists($k, $a)  )
                return false;
        return $v;
    }   
    else if(array_key_exists($v, $a))                   // value to check over array of values
        return $v;
        
    return false; 
}
// or empty or gived regexp
function sfEmptyOrRegExp($r) {return substr_replace($r, "^$|", 1, 0);} 
// on error ghange header and view (if set)
function vfGo2Header($file,$err="") {
    if(!empty($err)) $err = "?err=". urlencode($err);
	header("Location: $file" .$err);
	exit();
}
// cut string at len o a space near the len given 
function sfChopStr($str, $len){
	if (strlen($str) < $len) return $str;
	$str = substr($str, 0, $len);
	if ($spc_pos = strrpos($str, " "))
        $str = substr($str, 0, $spc_pos);
	
	return $str . "...";
}
// Sort chars in string
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
// acronimus
function sfAcron($s,$n=3) {
  return  substr(str_ireplace(array('a','e','i','o','u',' '), '', $s) ,0,$n);
}
// acronimus 
function sfAcronymit($s,$n=3) {
  $a = array_unique( str_split(strtolower($s)."123456789") ); // in case len($s) < $n=3
  $r  = "";
  foreach($a as $k=>$v) 
    if( $k==0 || !in_array($v,['a','e','i','o','u',' ']))
      $r .= $v;
  return substr($r,0,$n);
}
/* is the browser from mobile?
   old: return preg_match('/(blackberry|iphone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))? TRUE : FALSE;
**/
function isMobile() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    return (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)));
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
//https://gist.github.com/thomas-p-wilson/6114385
function array_stats(array $arr) {
    sort($arr);
    
    // Basic data
    $result = array();
    $result['cnt'] = count($arr);
    $result['min'] = min($arr);
    $result['max'] = max($arr);
    $result['sum'] = array_sum($arr);
    $result['avg'] = $result['sum'] / count($arr);
    $result['med'] = $arr[round(count($arr) / 2)];
    $result['rng'] = $arr[$result['cnt'] - 1] - $arr[0];
    // Quartiles
    $result['qt1'] = $arr[round( .25 * ($result['cnt'] + 1)) - 1];
    $result['qt2'] = ($result['cnt'] % 2 == 0) ? (($arr[($result['cnt'] / 2) - 1] + $arr[$result['cnt'] / 2]) / 2) : ($arr[($result['cnt'] + 1) / 2]);
    $result['qt3'] = $arr[round( .75 * ($result['cnt'] + 1)) - 1];
    
    return $result;
}
function vfErrLog($s) {
    $f  = "[%s]";            $a[] = date(DATE_RSS);
    $f .= "_ra:%s";         $a[] = C_CONFIG_SITE_NAME;
    $f .= "_user:%-15s";    $a[] = $_SESSION[C_LOGIN_USER_NAME];
    //$f .= " user-ip: %-15s"; $a[] = $_SERVER["REMOTE_HOST"];
    $f .= "_file:%-50s";    $a[] = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
    $f .= "_error:%s\n";    $a[] = $s;
    
    $l = vsprintf(str_replace("_"," ",$f),$a);
    error_log($l,3,C_DEFAULT_ERROR_LOG);
    if (C_CONFIG_MAIL_ON_ERROR === '1') {
        $l = vsprintf(str_replace("_","\n",$f),$a);
        error_log($l,1,C_CONFIG_ADMIN_EMAIL);
    }
}
 
/** PHP implementation of XXTEA encryption algorithm
* @author Ma Bingyao <andot@ujn.edu.cn>
* @link http://www.coolcode.cn/?action=show&id=128
*/

function int32($n) {
    while ($n >= 2147483648) {
        $n -= 4294967296;
    }
    while ($n <= -2147483649) {
        $n += 4294967296;
    }
    return (int) $n;
}

function long2str($v, $w) {
    $s = '';
    foreach ($v as $val) {
        $s .= pack('V', $val);
    }
    if ($w) {
        return substr($s, 0, end($v));
    }
    return $s;
}

function str2long($s, $w) {
    $v = array_values(unpack('V*', str_pad($s, 4 * ceil(strlen($s) / 4), "\0")));
    if ($w) {
        $v[] = strlen($s);
    }
    return $v;
}

function xxtea_mx($z, $y, $sum, $k) {
    return int32((($z >> 5 & 0x7FFFFFF) ^ $y << 2) + (($y >> 3 & 0x1FFFFFFF) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k ^ $z));
}

/** Cipher
* @param string plain-text password
* @param string
* @return string binary cipher
*/
function encrypt_string($str, $key) {
    if ($str == "") {
        return "";
    }
    $key = array_values(unpack("V*", pack("H*", md5($key))));
    $v = str2long($str, true);
    $n = count($v) - 1;
    $z = $v[$n];
    $y = $v[0];
    $q = floor(6 + 52 / ($n + 1));
    $sum = 0;
    while ($q-- > 0) {
        $sum = int32($sum + 0x9E3779B9);
        $e = $sum >> 2 & 3;
        for ($p=0; $p < $n; $p++) {
            $y = $v[$p + 1];
            $mx = xxtea_mx($z, $y, $sum, $key[$p & 3 ^ $e]);
            $z = int32($v[$p] + $mx);
            $v[$p] = $z;
        }
        $y = $v[0];
        $mx = xxtea_mx($z, $y, $sum, $key[$p & 3 ^ $e]);
        $z = int32($v[$n] + $mx);
        $v[$n] = $z;
    }
    return long2str($v, false);
}

/** Decipher
* @param string binary cipher
* @param string
* @return string plain-text password
*/
function decrypt_string($str, $key) {
    if ($str == "") {
        return "";
    }
    if (!$key) {
        return false;
    }
    $key = array_values(unpack("V*", pack("H*", md5($key))));
    $v = str2long($str, false);
    $n = count($v) - 1;
    $z = $v[$n];
    $y = $v[0];
    $q = floor(6 + 52 / ($n + 1));
    $sum = int32($q * 0x9E3779B9);
    while ($sum) {
        $e = $sum >> 2 & 3;
        for ($p=$n; $p > 0; $p--) {
            $z = $v[$p - 1];
            $mx = xxtea_mx($z, $y, $sum, $key[$p & 3 ^ $e]);
            $y = int32($v[$p] - $mx);
            $v[$p] = $y;
        }
        $z = $v[$n];
        $mx = xxtea_mx($z, $y, $sum, $key[$p & 3 ^ $e]);
        $y = int32($v[0] - $mx);
        $v[0] = $y;
        $sum = int32($sum - 0x9E3779B9);
    }
    return long2str($v, true);
}
/* SQL section ------------------------------------------------------------------------------------------------------------------------------------------------

/* select count(*) from table where ....
   return count or empty
**/
function sfSQL2Cnt($s) {
    $rs = @mysql_query($s);
    $n=""; if($rs) list($n) = mysql_fetch_row($rs);
    return is_null($n) ? "" : (string) $n;
}
/* value for this field is out of table
   used to check linked tables in delete
**/
function bfOut4Table($table,$field,$value) { 
    return empty(sfSQL2Cnt("select count(*) from $table where $field='$value'"));
} 
function bfLink2Table($table,$field,$value) { 
    return !empty(sfSQL2Cnt("select count(*) from $table where $field='$value'"));
}
/* upsert sqlite
	$kv = array(  
		 'user_name'   => 'admin'
		,'user_email'  => 'fproperzi@gmail.com'
		,'user_hpwd'   => '$2y$10$dLm..Mt86Uj7hioHMThInOGXmUM5R8Ssz4v78cKIPcdSx4c37c2vq'
		,'user_level'  => 8
		,'user_ctime'  => time()
		,'user_key'    => 'e34080c6-2278-4fd9-9c00-b421651ae63b'
		,'user_status' => 0
	);
	$row = afSQLiteUpsert("t_users",$kv,['user_name']);  //update or insert
**/
function afSQLiteUpsert($table,$kv,$ids) {

	foreach($kv as $k => $v) {
		$c[] = sprintf("%s" ,$k);
		$m[] = sprintf(":%s",$k);
		if (in_array($k,$ids)) { $w[] = sprintf("%s=:%s",$k,$k); $ww[$k] = $v; }
		else                     $s[] = sprintf("%s=:%s",$k,$k);
	}
	
	$cols = join(",",$c);      
	$vals = join(",",$m);  
	$sset = join(",",$s);
	$wwhere = join(" AND ", $w);
		
	$stmt = DB::run("UPDATE $table SET $sset WHERE $wwhere" ,$kv);	
	if($stmt->rowCount()==0) 
		$stmt = DB::run("INSERT INTO $table ($cols) VALUES ($vals)" ,$kv);
	
	$r = DB::run("SELECT * FROM $table WHERE $wwhere",$ww)->fetch();

	return $r; //return row upsert-ed
} 

function sfSQLErrClean($e) {
    if(false !== stripos($e,"duplicate") ) return substr($e,0,strripos($e, "for")); // Duplicate entry '623-Ball2-xxx (COPY) (COPY)' for key 'set_id_tag_what_tag_how'
    return $e;
}
/**
http://stackoverflow.com/questions/18811644/reorder-a-mysql-table
$extra = "set_id=3"... for a sub-set of ids in table
used in delete to reorder list
*/
function vfSQL2reorder($table,$fld_id,$fld_order,$extra="") {
    $s = "update $table t join (select *, (@rn := @rn + 1) as rn from $table cross join (select @rn := 0) const";
    if (!empty($extra))  $s .= " where $extra";                                
    $s .= " order by $fld_order,$fld_id) t2 on t.$fld_id = t2.$fld_id";
    //if (!empty($extra))  $s .= " and t2.$extra"; 
    $s .= " set t.$fld_order = t2.rn";
    
    @mysql_query($s); global $dbg;$dbg['reorder']=$s;
}
/**
http://stackoverflow.com/questions/812630/how-can-i-reorder-rows-in-sql-database
$extra = "set_id=3"... for a sub-set of ids in table
used in drag-dropo list
tip: In MySQL, you can't modify the same table which you use in the SELECT part.
http://stackoverflow.com/questions/45494/mysql-error-1093-cant-specify-target-table-for-update-in-from-clause
*/
function vfSQL2order_OLD_BAD($table,$fld_id,$fld_id_value,$fld_order,$fld_order_value,$extra="") {
    if (!empty($extra))  $e = " and $extra";
    $s  = "update $table set $fld_order=$fld_order+1 where $fld_order>=$fld_order_value $e"; 
    $s .= " and $fld_order <(select o from (select $fld_order as o from $table where $fld_id='$fld_id_value' $e) t)";
     global $dbg;$dbg['order']=$s;
    if(mysql_query($s)) {
        $s = "update $table set $fld_order=$fld_order_value where $fld_id='$fld_id_value' $e";
        @mysql_query($s);
    }
   $dbg['order'].=$s; $dbg['order_err']=mysql_error();
}
/*
**/
function vfSQL2order($table,$fld_id,$fld_id_value,$fld_order,$fld_order_value,$extra="") {
    if (!empty($extra))  $e = " $extra AND ";
    $s = "update $table t cross join (
              select $fld_order as o2, @rn := $fld_order_value
              ,SIGN($fld_order-@rn) as r2
              ,case when $fld_order>@rn then @rn else $fld_order end as b1
              ,case when @rn<$fld_order then $fld_order else @rn end as b2
              from $table where $e $fld_id='$fld_id_value') t2
          set $fld_order = $fld_order + t2.r2
          where $e $fld_order between t2.b1 and t2.b2";

    global $dbg;$dbg['order']=$s;
    if(mysql_query($s)) {
        $s = "update $table set $fld_order=$fld_order_value where $e $fld_id='$fld_id_value'";
        @mysql_query($s);
    }
   $dbg['order'].="\n".$s; $dbg['order_err']=mysql_error();
}
/** 
max order for insert
$extra = "set_id=3"... for a sub-set of ids in table
*/
function sfSQL2maxOrder($table,$fld_order,$extra="") {
    $s ="select max($fld_order)+1 from $table";
    if (!empty($extra))  $s .= " where $extra";
    
    return sfSQL2Cnt( $s ); global $dbg;$dbg['maxorder']=$s;
}
/* check unique,es.:
   select count(*) from t_users where user_email='$v' and user_id<>'$id_value'
   $table = "t_users", $k = "user_email", $id_name = "user_id"
**/
function isUnique($k,$v,$table,$id_name,$id_value = 0)  { 
    if(empty($id_value)) $id_value = 0;
    $s = "select count(*) from $table where $k='$v' and $id_name<>'$id_value'";
    $rs = mysql_query($s); global $dbg; $dbg['isUnique'] = $s;
    $n=1; if($rs) list($n) = mysql_fetch_row($rs);
    return $n >0 ? false : true;
}



?>
	
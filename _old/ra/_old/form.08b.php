<?php
ini_set('display_errors', 1); 
  
define("DB_HOST"	,"localhost");	// set database host
define("DB_USER"	,"u_mclips"); 	// set database user
define("DB_PASS"	,"nnkk219"); 	// set database password
define("DB_NAME"	,"mclips"); 	// set database name

/* DB connection and select
**/
if(!($link = mysql_connect(DB_HOST, DB_USER, DB_PASS))) vfGo2Header(C_FILE_ERROR, "Couldn't make connection");
if(!($db = mysql_select_db(DB_NAME, $link)))            vfGo2Header(C_FILE_ERROR, "Couldn't select database");
mysql_set_charset('utf8', $link);
setlocale(LC_ALL , "ita");
date_default_timezone_set("Europe/Rome");



/* Password and salt generation
**/
function sfPwdHash($pwd, $salt = null, $salt_length = 9) {
	
	if ($salt === null) $salt = substr(md5(uniqid(rand(), true)), 0, $salt_length);
	else 		        $salt = substr($salt, 0, $salt_length);
	return $salt . sha1($pwd . $salt);
}

/* generate password
**/
function sfGenPwd($length = 7){
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



$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ALL"				,"id" => 0xFFFFFF	);	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ANONYMOUS"		,"id" => 0x000000	,"xxx" => "Anonymous");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_ADMIN"			,"id" => 0x000040	,"txt" => "**Admin**");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_GUEST"			,"id" => 0x000001	,"txt" => "Guest");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER"			,"id" => 0x000300	,"xxx" => "Player");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_FWD"		,"id" => 0x000100	,"txt" => "Player Forwards");  // forward-avanti
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PLAYER_BCK"		,"id" => 0x000200	,"txt" => "Player Backs");  // back-trequarti
	                                                                                    
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR"			,"id" => 0x00FC00	,"xxx" => "Editor");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_KF"		,"id" => 0x000400	,"txt" => "Editor Key Factors");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_TEST"		,"id" => 0x000800	,"txt" => "Editor Surveys");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_JW"		,"id" => 0x001000	,"txt" => "Editor JW Analisys");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_D"		,"id" => 0x002000	,"txt" => "Editor Clips");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_E"		,"id" => 0x004000	,"xxx" => "** not in use");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_EDITOR_F"		,"id" => 0x008000	,"xxx" => "** not in use");
                                                                                        
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_STAFF"			,"id" => 0x1F0000	,"xxx" => "Staff");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_MANAGER"			,"id" => 0x010000	,"txt" => "Manager");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_COACH"			,"id" => 0x020000	,"txt" => "Coach");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_TRAINER"			,"id" => 0x040000	,"txt" => "Trainer");	
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_DOCTOR"			,"id" => 0x080000	,"txt" => "Doctor");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_PHYSIO"			,"id" => 0x100000	,"txt" => "Physio");
$gsUsrLevel[] = array("def" => "C_LOGIN_USER_LEVEL_REFEREE"			,"id" => 0x200000	,"txt" => "Referee");	


define ("C_DIR_IMG_GENERIC" ,"images/photo");
define ("C_DIR_IMG_OTHER"  ,"images/photo"); //is writable?

function aofPlayerRole()       { return array(""=>"","13"=>"Prop","2"=>"Hoker","45"=>"Second Row","678"=>"#8,Flanker","9"=>"Scrum Half","10"=>"Fly Half","34"=>"Backs");}
function aofNoYes()            { return array(0=>'No' ,1=>'Yes');}
function aofItaEng()           { return array(0=>'Ita',1=>'Eng');}
function aofUserLevel()        { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt'])) $a[$d['id']] = $d['txt']; return $a;}        
function sfUserLevel($v)       { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt']) && ($d['id'] & $v) ) $a[] = $d['txt']; return join(", ",$a);}  // all names level
function sfUserLevel2($v)      { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['xxx']) && ($d['id'] & $v) ) $a[] = $d['xxx']; elseif ( $d['id']<0x000300  && ($d['id'] & $v) ) $a[] = $d['txt']; return join(", ",$a);}  // all names level
function aofSplitUserLevel($v) { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt']) && ($d['id'] & $v) ) $a[] = strval($d['id']); return $a;}  // flag 2 array
function sfMergeUserLevel($a)  { $v=0;  foreach($a as $i) $v |= intval($i); return $v; }
function sfCropit2File($i,$v)     {
	$d = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v));
    $f = C_DIR_IMG_OTHER ."/".$i.".png";
    $w777 = file_put_contents($f, $d);
	return $f ;
}
function aofUserPhoto() { 
    $a = glob(dirname(__FILE__)."/". C_DIR_IMG_OTHER ."/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE);
    array_walk($a, function (&$v,$k,$n) { $v = substr($v, $n ); },strlen( dirname(__FILE__))+1);
    return $a;
}
// check if vale or array is in keys of onother array
// return value or false
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
//check if err is set
function bfCheck_Err ($args) {foreach($args as $f) if (isset($f['err'])) return true; return false;}
// check if post values are ok over $action (insert,update,delete)
// args is gaUpsert
function bfCheck_POST (&$args,$action) {

    $r = filter_input_array(INPUT_POST, $args);   global  $dbg;$dbg['r'] = $r;

    foreach($args as $k => &$u) {   // controllo se tutto è ok
       
		
			if ( ($r[$k] === false || is_null($r[$k])) && ($u['a'] & $action) )  $e = C_FORM_REQUESTED;        
        elseif ( is_scalar($r[$k]) && $r[$k] === false )            			 $e = C_FORM_NOT_VALID .": ". $u['h']  ;
        elseif ( is_array($r[$k]) && in_array(false,$r[$k],true) )               $e = C_FORM_OPTION_NOT_VALID;
        else																	 $e = false;													 
		
	    switch($action) {
        /****/ case C_ACTION_EDIT   : $u['a'] &= C_ACTION_UPDATE;  // used in js requested action
        break; case C_ACTION_NEW    : $u['a'] &= C_ACTION_INSERT;
        break; case C_ACTION_UPDATE :
        break; case C_ACTION_INSERT :
        break; case C_ACTION_DELETE :
        break;
        }
        
        $u['v'] = $r[$k];  // put value eventually filtered in $args
        if ($e != false) $u['err'] = $e;    // put error in $args
     
        unset( $u['filter']                 // neeed no more, clean for ajax return
              ,$u['flags'] 
              ,$u['options']
        );
    }
    return bfCheck_Err ($args) ;
}

define ("C_ACTION_INSERT" ,1 );
define ("C_ACTION_UPDATE" ,2 );
define ("C_ACTION_DELETE" ,4 );
define ("C_ACTION_EDIT"   ,8 );
define ("C_ACTION_NEW"    ,16 );

define("C_PLACEOLDER_PWD_UPDATE" 		,"** leave blank to no change");
define("C_PLACEOLDER_PWD_INSERT" 		,"** auto generated if empty");
define("C_FORM_REQUESTED"               ,"Please, this is required. ");
define("C_FORM_NOT_VALID"               ,"Please, this is not valid. ");
define("C_FORM_OPTION_NOT_VALID"        ,"You submit an option not valid. ");
define("C_FORM_ERROR"                   ,"Errors in form input" );
define("C_FORM_EDIT_ERROR"				,"Error in edit request");
define("C_FORM_NO_UNIQUE"               ,"Already taken, not unique" );


$gaUpsert = array(   //aud required for action: 1+2+4  must for insert,update,delete, for regexp empty or pattern: http://stackoverflow.com/questions/3333461/regular-expression-which-matches-a-pattern-or-is-an-empty-string
	  'user_id'		=> 	array("a"=>0+2+4+8						,"t"=>'hidden'	   ,"p"=>""                       						,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR	                                                                          
	),'user_photo'  =>	array("a"=>0+0+0	,"l"=>"Photo"		,"t"=>'photo'	   ,"p"=>""                       ,"o"=>aofUserPhoto()	,"filter"=>FILTER_VALIDATE_REGEXP	    ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"~^$|^data:image/\w+;base64,[a-zA-Z0-9+/\\=]*=$~")												                                        
	),'full_name'	=>	array("a"=>1+2+0	,"l"=>"Full name"	,"t"=>'text'	   ,"p"=>""                       						,"filter"=>FILTER_VALIDATE_REGEXP	    ,"flags"=>FILTER_REQUIRE_SCALAR	,"options"=>array("regexp"=>"/^$|^.{2,50}$/")                                   ,"h"=>"max 50"																                                        
	),'user_name'	=>  array("a"=>1+2+4	,"l"=>"User name"	,"t"=>'text'	   ,"p"=>""                       						,"filter"=>FILTER_VALIDATE_REGEXP	    ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^[a-z\d_]{4,20}$/i")		                      ,"h"=>"At least 4 letters, max 20, numbers or underscores, no spaces"
	),'user_email'	=>  array("a"=>1+2+0	,"l"=>"Email"		,"t"=>'email'	   ,"p"=>"you@example.org"        						,"filter"=>FILTER_VALIDATE_EMAIL	    ,"flags"=>FILTER_REQUIRE_SCALAR      						                                                  
	),'user_pwd'	=>	array("a"=>0+0+0	,"l"=>"Password"	,"t"=>'password'   ,"p"=>C_PLACEOLDER_PWD_INSERT                        ,"filter"=>FILTER_VALIDATE_REGEXP	    ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^$|^[a-z\d_!@#$%]{5,20}$/i")	                  ,"h"=>"At least 5 letters, max 20, numbers or special characters (!@#$%_) or their combination"  
	),'tel' 	    =>	array("a"=>0+0+0	,"l"=>"Tel"			,"t"=>'text'	   ,"p"=>"+39 123456789..."				 				,"filter"=>FILTER_VALIDATE_REGEXP       ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^$|^\+?[\d ]{3,20}/")			                  ,"h"=>"Any international code es. +39, numbers"                        
	),'bLang'		=>  array("a"=>0+0+0	,"l"=>"Language"	,"t"=>'flip'	   ,"p"=>""                       ,"o"=>aofItaEng()		,"filter"=>FILTER_CALLBACK		        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofItaEng() );}							                            
	),'user_level'	=>  array("a"=>1+2+0	,"l"=>"User level"	,"t"=>'checkbox'    ,"p"=>"choose..."             ,"o"=>aofUserLevel()	,"filter"=>FILTER_CALLBACK		        ,"flags"=>FILTER_REQUIRE_ARRAY  ,"options"=>function($v) { return bfInArray($v,aofUserLevel() );}								                                
	),'ruolo' 		=>  array("a"=>0+0+0	,"l"=>"Position"	,"t"=>'select'	   ,"p"=>"choose..."			  ,"o"=>aofPlayerRole() ,"filter"=>FILTER_CALLBACK  		    ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofPlayerRole() );}							                            
	),'approved'	=>  array("a"=>0+0+0	,"l"=>"Approved"	,"t"=>'flip'	   ,"p"=>""                       ,"o"=>aofNoYes()		,"filter"=>FILTER_CALLBACK		        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}							                            
	),'sendEmail'	=>  array("a"=>0+0+0	,"l"=>"Send e-mail"	,"t"=>'flip'	   ,"p"=>""                       ,"o"=>aofNoYes()		,"filter"=>FILTER_CALLBACK		        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}								                            
	)                                                                                                   
);                                                                                                    


// sql in form: "select count(*) from users where user_name='$u'"
function bfinUsers($u,$v,$user_id = 0)  { 
    $rs = mysql_query("select count(*) from users where $u='$v' and user_id<>'$user_id'");
    list($n) = mysql_fetch_row($rs);
	return $n >0 ? TRUE : FALSE;
}

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = array();
	   $dbg = array(); $dbg['post'] = $_POST;
      
    try {
        switch ( $action ) {
        /****/ case 'new':       // prepare array to insert
            
            $gaUpsert['user_pwd']['p'] = C_PLACEOLDER_PWD_INSERT;
			$avar = $gaUpsert;
			
        break; case 'insert':       //call insert with values
			
			if(bfCheck_POST ($gaUpsert,C_ACTION_INSERT)) throw new Exception( C_FORM_ERROR ); // check errors        

            $user_name = $gaUpsert['user_name']['v'];	            
            
            $u = "user_name";  $v = $gaUpsert[$u]['v']; if ( bfinUsers($u,$v) ) $gaUpsert[$u]['err'] = C_FORM_NO_UNIQUE;// if these are changed maust be unique
			$u = "user_email"; $v = $gaUpsert[$u]['v']; if ( bfinUsers($u,$v) ) $gaUpsert[$u]['err'] = C_FORM_NO_UNIQUE;
			
            if (bfCheck_Err ($gaUpsert)) throw new Exception( C_FORM_ERROR );

            $r = array();											// costruisco la query
            foreach($gaUpsert as $n => $f) {
				
				$b = true; 
				$v = $f['v'];
				
				switch($n) {
				/****/ case "user_id"    : $b = false;
				break; case "sendEmail"  : $b = false;
				break; case "user_pwd"   : $v = empty($v) ? sfPwdHash(sfGenPwd()) : sfPwdHash($v);
				break; case "user_level" : $v = sfMergeUserLevel($v);
				break; case "user_photo" : $v = sfCropit2File($user_name,$v);
				break; 
				}   

			    if($b) $r[$n] = $v;				// solo i valorizzati
            }
			$k = join(",",array_keys($r));
            $v = join("','",array_values($r));

            $rs = mysql_query("INSERT INTO users ($k) VALUES ('$v')");

            if ( !$rs ) 			       throw new Exception( $sSql .":: ". mysql_error() );
            $user_id = mysql_insert_id();
            if( empty( $user_id ))         throw new Exception( "Nessun record aggiunto: ". mysql_error() );
			
			$avar = $gaUpsert;
       
        break; case 'edit':      // prepare array to update/delete

            if ( bfCheck_POST($gaUpsert,C_ACTION_EDIT) ) throw new Exception( C_FORM_EDIT_ERROR );
			
			$user_id = $gaUpsert['user_id']['v']; 
			
			$rs = mysql_query("select * from users where user_id='$user_id'" ); 
			if($rs) { 
				$r = mysql_fetch_assoc($rs);
				foreach($gaUpsert as $n => $d) { //load values from existing record to update
                    switch($n) {
                    /****/ case "sendEmail"  : $gaUpsert[$n]['v'] = "0";
                    break; case "user_pwd"   : $gaUpsert[$n]['p'] = C_PLACEOLDER_PWD_UPDATE;  // no value for pwd
                    break; case "user_level" : $gaUpsert[$n]['v'] = aofSplitUserLevel( $r[$n] );
                    break; default           : $gaUpsert[$n]['v'] = $r[$n];
                    }
                }
			}		
			 
			$avar = $gaUpsert;
 
        break; case 'update': 
			
            if(bfCheck_POST ($gaUpsert,C_ACTION_UPDATE)) throw new Exception( C_FORM_ERROR ); // check errors
            
            $user_id = $gaUpsert['user_id']['v'];     
			$user_name = $gaUpsert['user_name']['v'];			
            
            $u = "user_name";  $v = $gaUpsert[$u]['v']; if ( bfinUsers($u,$v,$user_id) ) $gaUpsert[$u]['err'] = C_FORM_NO_UNIQUE;// if these are changed maust be unique
			$u = "user_email"; $v = $gaUpsert[$u]['v']; if ( bfinUsers($u,$v,$user_id) ) $gaUpsert[$u]['err'] = C_FORM_NO_UNIQUE;
			
            if (bfCheck_Err ($gaUpsert)) throw new Exception( C_FORM_ERROR ); // was errors            

            $s = ""; 
			foreach($gaUpsert as $n => $f) {
				
				$b = true; 
				$v = $f['v'];
				
				switch($n) {
				/****/ case "user_id"    : $b = false;
				break; case "sendEmail"  : $b = false;
				break; case "user_pwd"   : if (!empty($v)) $v = sfPwdHash($v);
				break; case "user_level" : $v = sfMergeUserLevel($v);
				break; case "user_photo" : $v = sfCropit2File($user_name,$v);
				break; 
				}
				if($b) $s .= ",$n='$v'";  $dbg[$n] = "'$v'";
			}
                
            $s = "UPDATE users SET ". substr($s,1) ." WHERE user_id='$user_id'";
			$rs = mysql_query($s);	$dbg['sql'] = $s;
            if ( !$rs ) throw new Exception( mysql_error() );
			
			$avar = $gaUpsert;
			//send email
    
        break; case 'delete':
            
            if(bfCheck_POST ($gaUpsert,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors
            
            $u = $gaUpsert['user_name']['v'];
            $i = $gaUpsert['user_id']['v'];
            
            if($u =='admin') 				 throw new Exception( "Cannot delete admin user" ); 
            //$rs = mysql_query("DELETE FROM users WHERE id='$i'");	
            $rs = mysql_query("update users set banned=1, approved=0 WHERE user_id='$i'");	
            if ( !$rs ) 				  	 throw new Exception( mysql_error() );
            if( 0 == mysql_affected_rows() ) throw new Exception( "No record processed width id: '$i" );
        
        break; case 'list':         // list all not deleted
        break; case 'approved':     // list only approved
			$rs = mysql_query("select  user_id,full_name,user_name,user_email,user_photo,user_level from users where approved=1 order by full_name");
			if($rs)while ($r = mysql_fetch_assoc($rs)) {
				$r['user_level'] = sfUserLevel($r['user_level']);
				$avar[] = $r;
			}
			
			
        break; case 'pending':  // list not approved
			$rs = mysql_query("select user_id,full_name,user_name,user_email,user_photo,user_level from users where approved=0 order by full_name");
			if($rs)while ($r = mysql_fetch_assoc($rs)) {
				$r['user_level'] = sfUserLevel($r['user_level']);
				$avar[] = $r;
			}
			
        break; case 'banned':      // list deleted (only for admin)
			$rs = mysql_query("select  user_id,full_name,user_name,user_email,user_photo,user_level from users where banned=1 order by full_name");
			if($rs)while ($r = mysql_fetch_assoc($rs)) {
				$r['user_level'] = sfUserLevel($r['user_level']);
				$avar[] = $r;
			}
				

        break; default :
            throw new Exception( "action not recognized" );
        } // switch ( $_REQUEST['action']) 
        
        echo  json_encode(array(
			 'status'=>true
			,'action'=>$action 
			,'l'=>$avar
			,'dbg' =>$dbg
			,'message'=>'done!'
		)); 
        
    } catch (Exception $e) {
		//$r = array();
		//foreach($aUpsert as $f => $d) $r[$f] = $d['v'];
		echo  json_encode(array(
			 'status'=>false
			,'action'=>$action 
			,'l'=>$avar
			,'dbg' =>$dbg
			,'message'=>$e->getMessage()
			));
	} 
    
    exit;
}
?>

<!DOCTYPE html> 
<html><head>
<title>Login Upsert</title> 
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link type="text/css" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" rel="stylesheet"  />  
<!-- jquery -->
<script type="text/javascript" src="//code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.cropit/0.5.1/jquery.cropit.js"></script>  
<!-- jquery mobile -->
<script type="text/javascript" src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>   
<style>
.error { color:red; }
.img2cropit {width:80px;height:80px; cursor: pointer;}
input.cropit-image-input {  visibility: hidden; }

  /* Hide the number input */
.full-width-slider input {    display: none;}
.full-width-slider .ui-slider-track {
    margin-left: 1px;max-width:100px;
}
.export {
  margin-top: 10px;
}
/* panel scroll */
.ui-panel.ui-panel-open {
    position:fixed;
}
.ui-panel-inner {
    position: absolute;
    top: 1px;
    left: 0;
    right: 0;
    bottom: 0px;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}
.ui-input-text {
    /*
    -webkit-box-shadow: 0 0 12px #ccc;
    -moz-box-shadow: 0 0 12px #ccc;
    box-shadow: 0 0 12px #ccc;
    */
    background-color:#333333; 
}
/*
input:-webkit-autofill {
    -webkit-box-shadow:0 0 0 50px white inset; /* Change the color to your own background color */
}
*/


label.error {
    color: red;
    font-size: 16px;
    font-weight: normal;
    line-height: 1.4;
    margin-top: 0.5em;
    width: 100%;
    float: none;
}

@media screen and (orientation: portrait){
    label.error { margin-left: 0; display: block; }
}

@media screen and (orientation: landscape){
    label.error { display: inline-block; margin-left: 22%; }
}
/*
label > em { color: red; font-weight: bold; padding-right: .25em; }
*/


.nav-glyphish-example .ui-btn { padding-top: 40px !important; }
.nav-glyphish-example .ui-btn:after { width: 30px!important; height: 30px!important; margin-left: -15px !important; box-shadow: none!important; -moz-box-shadow: none!important; -webkit-box-shadow: none!important; -webkit-border-radius: 0 !important; border-radius: 0 !important; }
#chat:after { background:  url("../_assets/img/glyphish-icons/09-chat2.png") 50% 50% no-repeat; background-size: 24px 22px; }
#email:after { background:  url("../_assets/img/glyphish-icons/18-envelope.png") 50% 50% no-repeat; background-size: 24px 16px;  }
#login:after { background:  url("../_assets/img/glyphish-icons/30-key.png") 50% 50% no-repeat;  background-size: 12px 26px; }
#beer:after { background:  url("../_assets/img/glyphish-icons/88-beermug.png") 50% 50% no-repeat;  background-size: 22px 27px; }
#coffee:after { background:  url("../_assets/img/glyphish-icons/100-coffee.png") 50% 50% no-repeat;  background-size: 20px 24px; }
#skull:after { background:  url("../_assets/img/glyphish-icons/21-skull.png") 50% 50% no-repeat;  background-size: 22px 24px; }

</style>
<!-- templates -->
<script type="text/template" id="tmpl-image-cropper">
	<div class="ui-grid-a">
	    <div class="ui-block-a">
			<div id="image-cropper">
				<div class="ui-grid-a"><div class="ui-block-a">
					<div class="cropit-preview"></div>
					<div class="full-width-slider">
						<input type="range" data-mini="true" data-highlight="true" class="cropit-image-zoom-input" />
					</div>
					<input type="file" class="cropit-image-input" />
					<input type="hidden" name="<%= name %>" class="cropit-image-data" />
				</div><div class="ui-block-b">
					<a href="#" class="ui-btn ui-shadow ui-corner-all ui-nodisc-icon ui-icon-back    ui-btn-icon-notext ui-btn-b rotate-cw-btn">CW</a>
					<a href="#" class="ui-btn ui-shadow ui-corner-all ui-nodisc-icon ui-icon-forward ui-btn-icon-notext ui-btn-b rotate-ccw-btn">CCW</a>
				</div></div>	
			</div>
		</div>
	    <div class="ui-block-b">
		    <a href="#"           class="ui-btn ui-btn-inline ui-btn-icon-left ui-icon-camera select-image-btn">To load</a>
			<a href="#<%= key %>" class="ui-btn ui-btn-inline ui-btn-icon-left ui-icon-bars" >Loaded</a>  
		</div>
	</div><!-- /grid-a -->
</script>

<script type="text/template" id="tmpl-image-cropper-panel">
    <div id="<%= key %>" data-role="panel" data-display="overlay" data-position="right" data-theme="b">
        <ul data-role="listview" data-icon="false">
            <li data-icon="delete"><a href="#" data-rel="close">Close</a></li>
        </ul>
        <div class="ui-content">
        <%  _.each(o,function(i){   %>
                <img class="img2cropit" src="<%= i %>">
        <% }); %>
        </div>
    </div> 
</script>

<script type="text/template" id="tmpl-users-list">
	<%  _.each(l,function(i){   %>
		<li data-filtertext="">
			<a href="#page-form" data-action="edit" data-user_id="<%= i.user_id %>" class="ui-btn ui-btn-icon-right ui-icon-carat-r">
				<img src="<%= i.user_photo %>" alt="">
				<h2><%= i.full_name %></h2>
				<p><em><%= i.user_level %></em></p>
				<p>login: <%= i.user_name %>, email: <%= i.user_email %></p>
			</a>
		</li>
	<% }); %>
</script>

<script type="text/template" id="tmpl-pop-dialog">
		<div data-role="popup" id="pop-dialog" data-overlay-theme="b" data-theme="a" style="max-width:400px;" data-dismissible="<%= dismissible %>">
			<div data-role="header">
				<h1 class="msg-head"><%= head %></h1>
			</div>
			<div data-role="content">
				<h3 class="msg-alert"><%= alert %></h3>
				<p class="msg-note"><%= note %></p>
				<br><br>
				<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a> 
				<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-icon-check ui-btn-icon-left" data-rel="back">Ok</a> 				
			</div>
		</div>
</script>
<script type="text/template" id="tmpl-dialog-sure">
    <div data-role="dialog" id="sure" data-title="Are you sure?">
       <div data-role="content">
        <h3 class="sure-1"><%= text1 %></h3>
        <p class="sure-2"><%= text2 %></p>
        <a href="#" class="sure-do" data-role="button" data-theme="b" data-rel="back">Yes</a>
        <a href="#" data-role="button" data-theme="c" data-rel="back">No</a>
      </div>
    </div>
</script>

<script type='text/javascript'>
// buttons [{name:'ok',id:'pop-dialog-ok'},{name:'cancel',id:'pop-dialog-cancel'}]
function vfPopDialog(head,alert,note,buttons) {
	
}
//_.templateSettings = { interpolate: /\{\{\=([\s\S]+?)\}\}/g, escape: /\{\{\-([\s\S]+?)\}\}/g, evaluate: /\{\{([\s\S]+?)\}\}/g };
//var gaUpsert = <?= json_encode($gaUpsert) ?>;

function voidif(v) {if (v==='') return void(0); return v;}
function empty(v) { if(typeof(v)==='undefined'||v===null||v===''||v===false||v===0||v==='0') return true;return false }
// to enhance photo field
function vfPhotoFieldCropitEnhance(v) {
   
    $('#image-cropper').cropit({                    // photo cropit enhancer.... only if you have photo!
        imageState: { src: v  } 
        ,smallImage: 'stretch'
        ,width:80
        ,height:80
    });
    $('.cropit-image-input').parent().hide();       // annoing problem on markup buid by jquery mobile over hidden input
    $('.select-image-btn').click(function() {       // play attention to header [data-tap-toggle="false"] otherwise range-zoom not working
        $('.cropit-image-input').click();
        $('.cropit-image-zoom-input').slider( "enable" );
    });        
    $('.img2cropit').on('click',function(e) {               
        $('#image-cropper').cropit('imageSrc', this.src);   // images loaded to cropit
        $('.ui-panel.ui-panel-open').panel( 'close' );      // close the panel where images loaded
        $('.cropit-image-zoom-input').slider( "enable" );   // some times happen is disable
    });
	$('.rotate-cw-btn').click(function() {					// Handle rotation
		$('#image-cropper').cropit('rotateCW');
	});
	$('.rotate-ccw-btn').click(function() {
		$('#image-cropper').cropit('rotateCCW');
	});
	
}
/* 
**/
function vfFormBuild(formId,action,l) {
    var a=[],n,photoFld;
	
    for (n in l) {
        l[n].name = n;
		if (l[n].t==='photo') photoFld = n;
		
        a.push( sfFormField(l[n]) );
    }
	a.push(sfFormField({n:'action',t:action}));
	$(formId).empty().html( a.join("") ).enhanceWithin();//.trigger('create');
	if(!empty(photoFld)) vfPhotoFieldCropitEnhance( _.escape(l[photoFld].value || l[photoFld].v) ); // there is a photo field?
	//$('input').attr('autocomplete', 'off');
	$(formId +" input[type=submit]").click(function(e) {
		// check if cropit is in form
	    if ( $('.cropit-image-data').length > 0 ) { 
			 var imageData = $('#image-cropper').cropit('export');
			 $('.cropit-image-data').val(imageData);
		}
		vfAjax(formId,$(this).val()); // $(this).val() = insert,update,delete
		
		return false;
	}); 
	$(formId).on("submit", function(){
		
		return false;
	});
}
var isError = function(i,e) { if(e.length) return '<label id="'+i+'-error" class="error">'+e+'</label>';return '';}


function sfFormField(f) {

    var v2s = function(s,i) {if(s===void(0))return ''; return (i ? i+'="'+s+'"':s)}
    var uniqId = function(p) {return v2s(p)+Math.round(new Date().getTime() + (Math.random() * 100));} // used _.uniqId
    var isSelected = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "selected"; return ""; }
    var isChecked = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "checked"; return ""; }
    
    
    var  i = f.name || f.n //uniqId('i')
        ,n = f.name || f.n
        ,t = f.type || f.t || 'hidden'
        ,l = _.escape(f.label || f.l ) + (f.a ? '<em> *</em>' : '')
        ,v = _.isArray(f.value || f.v) ? _.map(f.value || f.v ,function(e) {return _.escape(e);}) : _.escape(f.value || f.v)
        ,p = _.escape(f.placeholder || f.p)
        ,e = _.escape(f.error || f.err || f.e) 
        ,o = f.o || []
        ,r = f.a ? 'requested' : ''
        ,s,w='',j=0;
		

    switch(t) {
    /****/ case 'hidden':   s = '<input type="hidden" name="'+n+'" value="'+v+'">';
    break; case 'text':     
    /****/ case 'email':    
    /****/ case 'password': 
                            s = '<label for="'+i+'">'+l+'</label>';
							//s+= '<input type="'+t+'" name="'+n+'_fakename" style="display:none;">'
							s+= '<input type="'+t+'" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' autocomplete="off">';
                            
    break; case 'submit':   s = '<input type="submit" name="'+n+'" value="'+v+'" data-inline="true">';              
    break; case 'button':   s = '<button data-inline="true">'+v+'</button>';
      
    break; case 'insert':   s = '<label> </label>';
                            s += '<input type="submit" name="action" value="insert" data-icon="check" data-inline="true">';
                            s += '<a href="#" class="ui-btn ui-btn-inline ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a>';
							
    break; case 'update':   s = '<label> </label>';
                            s += '<input type="submit" name="action" value="update" data-icon="check" data-inline="true">';
                            s += '<a href="#" class="ui-btn ui-btn-inline ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a>';
                            s += '<input type="submit" name="action" value="delete" data-icon="delete" data-inline="true">';
	
    break; case 'checkbox': n += '[]'; //name[] for multiple
           case 'radio':        
                            s = '<fieldset data-role="controlgroup"><legend>'+l+'</legend>';
                            
                            for (k in o) { // AAA k is string, in  isChecked(v,k) = v array of string
                                s += '<input type="'+t+'" id="'+i+'-'+j+'" name="'+n+'" value="'+_.escape(k)+'" '+isChecked(v,k)+'>'
                                s += '<label for="'+i+'-'+j+'">'+_.escape(o[k])+'</label>';
                                j++;
                            }
                            s += '</fieldset>';
                            
    break; case 'mselect':  n += '[]'; //name[] for multiple
                            if (w==='') w = 'data-native-menu="false" multiple="multiple" data-overlay-theme="b"';

           case 'select':   if (w==='') w = 'data-native-menu="false" data-overlay-theme="b"';
           case 'flip':     if (w==='') w = 'data-role="flipswitch"';
                            s = '<label for="'+i+'">'+l+'</label><select id="'+i+'" name="'+n+'" '+w+'>';  // data-native-menu="false"
                            if(!empty(p)) s += '<option value="" data-placeholder="true">'+p+'</option>';
                            for (k in o) // AAA k is string, in  isSelected(v,k) = v array of string
                                s += '<option value="'+_.escape(k)+'" '+isSelected(v,k)+'>'+_.escape(o[k])+'</option>';				
                            s += '</select>'; 
    break; case 'photo':
                            s = '<label for="'+i+'">'+l+'</label>';
                            s += _.template($("#tmpl-image-cropper").html())({key:i,name:n});  // form
                            
                            if($( '#'+i ).length > 0) $( '#'+i ).remove();  //recreate every form view (new images...)
                            
                            w = _.template($("#tmpl-image-cropper-panel").html())({key:i,o:o}); // panel
                            $.mobile.pageContainer.append( w );  // to create dinamic panel: https://jqmtricks.wordpress.com/2014/04/13/dynamic-panels/
                            $( '#'+i ).panel().enhanceWithin();
                            
    break; case 'fake':     s = '<input style="display:none" type="text" name="fakeusernameremembered"/>';
							s += '<input style="display:none" type="password" name="fakepasswordremembered"/>';
    }//switch
    if(!empty(e))                s = '<span class="error">'+e+'</span>'+s
    if(t!='hidden' && t!='fake') s = '<div class="ui-field-contain">'+s+'</div>';
    
    return s;
}



function vfAjax(elemId,action,myData) {
	
    //var f = _($(elemId).serializeArray()||[]).reduce(function(a,f){a[f.name]=f.value;return a;},{}); //http://blog.mysema.com/2012/06/form-data-extraction-with-backbonejs.html
	var f; //http://stackoverflow.com/questions/3277655/how-to-convert-jquery-serialize-data-to-json-object/3277710#3277710
	if (action == "insert" || action == "update") 
			f = _.reduce($(elemId).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
	else    f = myData||{};

    $.ajax({
       url : $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
      data : _.extend(f,{action:action}),		//$form.serialize(),
      type : 'post',                  
     async : 'true',
  dataType : 'json',
beforeSend : function() { $.mobile.loading( "show" ); },
  complete : function() { $.mobile.loading( "hide" );  },
     error : function (req,err) { 
        vfPopUp('Error: '+err,'<p>'+ _.escape(req.responseText) +'</p>'); },
   success : function (result) {
        if(result.status) {

            switch(result.action) {
            /****/ case 'new':    vfFormBuild(elemId,'insert',result.l);				
			break; case 'insert': if (result.status) vfFormBuild(elemId,'update',result.l);	
								  else               vfFormBuild(elemId,'insert',result.l);	
            break; case 'edit':	  vfFormBuild(elemId,'update',result.l);
            break; case 'update': vfFormBuild(elemId,'update',result.l);
			break; case 'approved' : 
			       case 'pending' :
				   case 'banned' :
				s = _.template($("#tmpl-users-list").html())({l:result.l,a:result.action});
				$(elemId).empty().html( s ).listview("refresh");
            }

        }
        else {
            vfPopUp("Error",'<p>'+  _.escape(result.message) +'</p>');
            for 
            error = $( "<label>" )
					.attr( "id", elementID + "-error" )
					.addClass( "error" )
					.html( message || "" )
                    .appendTo($(elementID).parent().prev());
        }
    }
		}); // ajax  
}

$( document ).on( "pagecontainerbeforeshow", function ( e, d ) {
	
	switch( d.toPage.jqmData('url') ) {
	/****/ case "page-list-approved": vfAjax('#page-list-approved-ul','approved');
	break; case "page-list-pending" : vfAjax('#page-list-pending-ul' ,'pending');
	break; case "page-list-banned"  : vfAjax('#page-list-banned-ul'  ,'banned');
	break; case "page-form"         :
		if( $.mobile.activeClickedLink ) {
			switch( $.mobile.activeClickedLink.jqmData('action') || "") { // wich anchor was clicked?
			/****/ case "new" :	  vfAjax('#page-form-edit','new');	
			break; case "edit" :  vfAjax('#page-form-edit','edit', {user_id:$.mobile.activeClickedLink.jqmData('user_id')} );	
			break; 
			}
		}
	break;
	}

});
//http://api.jquerymobile.com/popup/
function scale( width, height, padding, border ) {
    var scrWidth = $( window ).width() - 30,
        scrHeight = $( window ).height() - 30,
        ifrPadding = 2 * padding,
        ifrBorder = 2 * border,
        ifrWidth = width + ifrPadding + ifrBorder,
        ifrHeight = height + ifrPadding + ifrBorder,
        h, w;
 
    if ( ifrWidth < scrWidth && ifrHeight < scrHeight ) {
        w = ifrWidth;
        h = ifrHeight;
    } else if ( ( ifrWidth / scrWidth ) > ( ifrHeight / scrHeight ) ) {
        w = scrWidth;
        h = ( scrWidth / ifrWidth ) * ifrHeight;
    } else {
        h = scrHeight;
        w = ( scrHeight / ifrHeight ) * ifrWidth;
    }
 
    return {
        'width': w - ( ifrPadding + ifrBorder ),
        'height': h - ( ifrPadding + ifrBorder )
    };
};
/**
    areYouSure("Are you sure?", "---description---", "Exit", function() {
      // user has confirmed, do stuff
    });
**/
function vfAreYouSure(text1, text2, button, callback) {
    $("#sure .sure-1").text(text1);
    $("#sure .sure-2").text(text2);
    $("#sure .sure-do").text(button).unbind("click.sure").on("click.sure", function() {
        callback(false);
        $(this).off("click.sure");
    });
    $.mobile.changePage("#sure");
}

function vfAreYouSure2(text1, text2, button, callback) {

    var pop = '';
    error = $( "<" + this.settings.errorElement + ">" )
					.attr( "id", elementID + "-error" )
					.addClass( this.settings.errorClass )
					.html( message || "" );

    $('<div data-role="popup" data-theme="a" data-corners="false" style="max-width:400px;">')
    .append(

    $("#sure .sure-1").text(text1);
    $("#sure .sure-2").text(text2);
    $("#sure .sure-do").text(button).unbind("click.sure").on("click.sure", function() {
        callback(false);
        $(this).off("click.sure");
    });
    $.mobile.changePage("#sure");
}


function vfPopUp(header,body,theme) {
	var closebtn = '<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right">Close</a>'
		 ,header = '<div data-role="header"><h2>' + header + '</h2></div>'
		  ,popup = '<div data-role="popup" id="pop-upsert" data-theme="'+(theme?theme:'a')+'" data-corners="false"  style="max-width:400px;"></div>'
		; // dichiarazioni popup

	$( header )		// Create the popup. 
		.appendTo( $( popup ).appendTo( $.mobile.activePage ).popup() )
		.toolbar()
		.before( closebtn )
		.after( body );
	//$( "#pop-upsert").popup( "open" ,"positionTo", "window").trigger('create');
	$( "#pop-upsert")
		.popup( "open" ,{positionTo: 'window'})
		.trigger('create')
		.on("popupafterclose", function () { // Remove the popup after it has been closed to manage DOM size
			$(this).remove(); 
			//$('#page-ex').css('padding-top','44px'); // annoing problem jqm popup
		});
}




/**
 obj = {
    name : 
    label :
    value : 
    placeholder 
    max :
    min :
    requested :
    options : [array]
 }
**/


//$(document).on("pagebeforeshow", "#page-list-approved",function(e,d) {	vfAjax('#page-list-approved-ul','approved'); });
//$(document).on("pagebeforeshow", "#page-list-pending" ,function(e,d) {	vfAjax('#page-list-pending-ul' ,'pending'); });
//$(document).on("pagebeforeshow", "#page-list-banned"  ,function(e,d) {	vfAjax('#page-list-banned-ul'  ,'banned'); });

	
//$( "#page-form" ).on( "pagecontainerbeforeshow" ,function(e,d) {
$(document).on("pagebeforeshow", "#page-form_"         ,function(e,d) {
//$(document).on('pagecreate', function(){
//    $(':mobile-pagecontainer').on("pagecontainerbeforeshow",  function(e) {
	//vfAjax('#myform','new');
	//$(document).on('submit','form.remember',function(){
	//vfAjax('#myform','edit',id);

});

//});
</script>


    </head>
    <body>
		<div id="page-list-approved" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Approved</h1>
				<a href="#page-form" data-action="new" data-icon="edit">New</a>
				<div data-role="navbar" data-mini="true" >
					<ul>
						<li><a href="#page-list-approved" data-icon="check"    class="ui-btn-active ui-state-persist">Approved</a></li>
						<li><a href="#page-list-pending"  data-icon="forbidden">Pending</a></li>
						<li><a href="#page-list-banned"   data-icon="delete"   >Banned</a></li>
					</ul>
				</div>
            </div>
            <div data-role="content" >  
                <ul id="page-list-approved-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-list -->
		<div id="page-list-pending" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Pending</h1>
				<a href="#page-form" data-action="new" data-icon="edit">New</a>
				<div data-role="navbar" data-mini="true" >
					<ul>
						<li><a href="#page-list-approved" data-icon="check"     >Approved</a></li>
						<li><a href="#page-list-pending"  data-icon="forbidden" class="ui-btn-active ui-state-persist">Pending</a></li>
						<li><a href="#page-list-banned"   data-icon="delete"    >Banned</a></li>
					</ul>
				</div>
            </div>
            <div data-role="content" >  
                <ul id="page-list-pending-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-list -->
		<div id="page-list-banned" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Banned</h1>
				<a href="#page-form" data-action="new" data-icon="edit">New</a>
				<div data-role="navbar" data-mini="true" >
					<ul>
						<li><a href="#page-list-approved" data-icon="check"    >Approved</a></li>
						<li><a href="#page-list-pending"  data-icon="forbidden">Pending</a></li>
						<li><a href="#page-list-banned"   data-icon="delete"   class="ui-btn-active ui-state-persist">Banned</a></li>
					</ul>
				</div>
            </div>
            <div data-role="content" >  
                <ul id="page-list-banned-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-list -->
		
        <div id="page-form" data-role="page" data-theme="b">

            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <h1>Edit</h1>
            </div>
            <div data-role="content" >  

                <form id="page-form-edit" data-ajax="false"></form>

            </div>
            
            <div data-role="dialog" id="sure" data-title="Are you sure?">
              <div data-role="content">
                <h3 class="sure-1">???</h3>
                <p class="sure-2">???</p>
                <a href="#" class="sure-do" data-role="button" data-theme="b" data-rel="back">Yes</a>
                <a href="#" data-role="button" data-theme="c" data-rel="back">No</a>
              </div>
            </div><!-- // dialog-sure -->
            
        </div> <!-- //page-form -->

    </body>
</html>
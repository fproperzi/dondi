<?php
/* crud section ------------------------------------------------------------------------------------------------------------------------------------------------
*  every table that use crud.php must have 2 fields: update_by, update_on to track updates
**/
define("C_REGEXP_USER_NAME"	        ,"/^.{2,50}$/");                            // da 2 a 50 chrs
define("C_REGEXP_USER_NAME_OR_NULL" ,"/^$|^.{2,50}$/");                         // vuoto o da 2 a 50 chrs
define("C_REGEXP_USER_LOGIN"        ,"/^[A-Za-z\d_]{4,20}$/");                  // A-Z,a-z,0-9 min 4 max 20 chars
define("C_REGEXP_USER_NICK"         ,"/^[A-Za-z\d_]{4,20}$/");                  // A-Z,a-z,0-9 min 4 max 20 chars
define("C_REGEXP_USER_PWD"	        ,"/^[A-Za-z\d_!@#$%]{5,20}$/");             // A-Z,a-z,0-9 _!@#$%  min 5 max 20 chars
define("C_REGEXP_USER_PWD_OR_NULL"  ,"/^$|^[A-Za-z\d_!@#$%]{5,20}$/");          // empty or A-Z,a-z,0-9 _!@#$%  min 5 max 20 chars
define("C_REGEXP_USER_KEY"          ,"/^\w{8}-(\w{4}-){3}\w{12}$/");            // unique guid, https://eval.in/640974
define("C_REGEXP_ASCII"             ,"/^[[:graph:][:space:]]*$/");              // from 32 to 126
define("C_REGEXP_GENERIC_KEY"       ,"/^[A-Za-z\d_\-]+$/");                     // letters, numbers, _-
define("C_REGEXP_PHOTO"             ,"~^$|^data:image/\w+;base64,[a-zA-Z0-9+/\\=]*$~"); 
//define("C_REGEXP_PHOTO"             ,"~^data:image/\w+;base64,.*$~"); 
define("C_REGEXP_TEL"               ,"/^\+?[\d ]{3,20}/");                          
define("C_REGEXP_URL"				,"/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i");
define("C_REGEXP_DIR_FILE"          ,'~^[a-zA-Z]:\\(((?![<>:"/\\|?*]).)+((?<![ .])\\)?)*$~');  //http://stackoverflow.com/questions/24702677/regular-expression-to-match-a-valid-absolute-windows-directory-containing-spaces

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

/* array definitions crud, chech in crud.php for explanations
       _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action 
       "l" = label
       "h" = tip help
       "e" = error on 
       "t" = input type
       "p" = placeholder
       "d" = default
       "o" = options for input
       
       "filter"   =  form validation and check
       "flags"    =  ...
       "options"  =  ... 
       
**/     

define ("C_ACTION_2PREPARE",C_ACTION_2UPDATE | C_ACTION_2INSERT | C_ACTION_2COPY | C_ACTION_2DELETE); 
                                                                        
                                                                        
define("C_FORM_REQUIRED"                ,_e("e.required")        );              // Valore richiesto
define("C_FORM_REQUIRED_NOT_VALID"      ,_e("e.not-valid")       );              // Valore richiesto ma non valido
define("C_FORM_NOT_VALID"               ,_e("e.not-valid")       );              // Valore non valido
define("C_FORM_OPTION_NOT_VALID"        ,_e("e.not-valid-option"));              // Opzione non valida
define("C_FORM_NO_UNIQUE"               ,_e("e.not-unique")      );              // Valore già presente nel DB
define("C_FORM_ERROR"                   ,_e("e.generic")         );              // Errori nel modulo, controllare prego
                                                                    

// save image to C_DIR_IMG_USERS return path or false
function sfCropit2Img($dir,$filename,$v)     {
	$d = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v)); 
    $f = $dir ."/".$filename.".png"; 
    $w777 = file_put_contents($f, $d, LOCK_EX);
	return (empty($w777) ? false : $f);  // $w777 false or 0
}
//from action to action, use it in a TRY catch
function sfNewActionOrError($action,&$avar) {
    global  $dbg;
    switch($action) {   
    /****/ case "_2insert" : $a = "_insert"; $c = C_ACTION_2INSERT;
    break; case "_2copy"   : $a = "_insert"; $c = C_ACTION_2COPY;
    break; case "_2update" : $a = "_update"; $c = C_ACTION_2UPDATE;
    break; case "_2delete" : $a = "_delete"; $c = C_ACTION_2DELETE;
    break; case "_insert"  : $a = "_insert"; $c = C_ACTION_INSERT ;
    break; case "_update"  : $a = "_update"; $c = C_ACTION_UPDATE ;
    break; case "_delete"  : $a = "_delete"; $c = C_ACTION_DELETE ;
    break; default         : $a = $action; 
    }
    
    if(!empty($c) && bfCheckRequest ($avar ,$c)) throw new Exception( C_FORM_ERROR );   
    return $a;
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
function bfCheckRequest (&$args,$action) { 
    $m = $_SERVER['REQUEST_METHOD'] == "GET" ? INPUT_GET : INPUT_POST;
    $r = filter_input_array($m, $args /* ,true */);     /* false not valid, null not given */
//global $dbg; $dbg['args']=$args;$dbg['r']=$r;
    foreach($args as $k => &$u) {                                                                              // check all is ok
       
        if(isset($u['h'])) $h = ": ".$u['h']; 
        else               $h = "";
            
            if ( ($u['a'] & $action) && is_null($r[$k]) )                     $e = C_FORM_REQUIRED;           // required not gived
        elseif ( ($u['a'] & $action) && empty($_REQUEST[$k]) )                $e = C_FORM_REQUIRED;           // given empty
        elseif ( ($u['a'] & $action) && $r[$k] === false)                     $e = C_FORM_REQUIRED_NOT_VALID; // required but invalid 
        elseif ( $r[$k] === false && !empty($_REQUEST[$k]))                   $e = C_FORM_NOT_VALID;          // not required but invalid
        else                                                                  $e = false;                                                    
        
        $u['a'] &= nfAction2Action($action);  // from action to action
        
        if($u['filter'] === FILTER_VALIDATE_REGEXP && $u['t'] !='photo') $u['v'] = addslashes($r[$k]);         // cant force magic_quotes and regexp
        else                                                             $u['v'] = $r[$k];                     // put value eventually filtered in $args
        if ($e != false) $u['err'] = $e;                                                                       // put error in $args
        
//if ($u['t'] =='photo') $dbg['photo_b'] = $k; $dbg['photo_c'] = $r[$k];
     
        unset( $u['filter']                                                                                    // neeed no more, clean for ajax return
              ,$u['flags'] 
              ,$u['options']
        );                                                                
    } 
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




e


?>
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

define ("C_ACTION_NONE"    ,0x000 );      define ( "_N" ,C_ACTION_NONE    );
define ("C_ACTION_LIST"    ,0x001 );      define ( "_L" ,C_ACTION_LIST    );   // fields to list under sets, filter fiel to obtain list
define ("C_ACTION_ORDER"   ,0x002 );      define ( "_O" ,C_ACTION_ORDER   );   // fileds to identify record and order field

define ("C_ACTION_2INSERT" ,0x010 );      define ("_2I" ,C_ACTION_2INSERT );   // prepare to insert: witch fields must for insert (external id)
define ("C_ACTION_2UPDATE" ,0x020 );      define ("_2U" ,C_ACTION_2UPDATE );   // prepare to edit: witch fields to identify record
define ("C_ACTION_2DELETE" ,0x040 );      define ("_2D" ,C_ACTION_2DELETE );   // prepare to delete: witch fields to identify record, just a confirm
define ("C_ACTION_2COPY"   ,0x080 );      define ("_2C" ,C_ACTION_2COPY   );   // prepare to copy: witch fields to identify origin 

define ("C_ACTION_INSERT"  ,0x100 );      define ( "_I" ,C_ACTION_INSERT  );   // mandatory fields 
define ("C_ACTION_UPDATE"  ,0x200 );      define ( "_U" ,C_ACTION_UPDATE  );   // fields to identify record ,mandatory fields 
define ("C_ACTION_DELETE"  ,0x400 );      define ( "_D" ,C_ACTION_DELETE  );   // fields to identify record
define ("C_ACTION_COPY"    ,0x800 );      define ( "_C" ,C_ACTION_COPY    );   // fields to identify record, like insert from 2update 


define("C_PLACEOLDER_PWD_UPDATE" 		,"** leave blank to no change");
define("C_PLACEOLDER_PWD_INSERT" 		,"** auto generated if empty");
define("C_FORM_REQUESTED"               ,"Please, this is required");
define("C_FORM_NOT_VALID"               ,"Please, this is not valid");
define("C_FORM_OPTION_NOT_VALID"        ,"You submit an option not valid");
define("C_FORM_ERROR"                   ,"Error occur in form input values" );
define("C_FORM_NO_UNIQUE"               ,"Already taken, not unique" );


define ("C_ACTION_2PREPARE",C_ACTION_2UPDATE | C_ACTION_2INSERT | C_ACTION_2COPY | C_ACTION_2DELETE); 

//check if err is set
function bfCheck_Err ($args) {foreach($args as $f) if (isset($f['err'])) return true; return false;}
// check if post values are ok over $action (insert,update,delete...)
// args is gaUpsert
function bfCheck_POST (&$args,$action) {

    $r = filter_input_array(INPUT_POST, $args);   global  $dbg;$dbg['r'] = $r;

    foreach($args as $k => &$u) {   // controllo se tutto è ok
       
		
			if ( ($r[$k] === false || is_null($r[$k])) && ($u['a'] & $action) )  $e = C_FORM_REQUESTED .": ". @$u['h'];    // no or invalid value but required for action
        elseif ( $action & C_ACTION_2PREPARE | C_ACTION_LIST | C_ACTION_ORDER)   $e = false;                               // no matter field in these actions
        elseif ( is_scalar($r[$k]) && $r[$k] === false )            			 $e = C_FORM_NOT_VALID .": ". @$u['h'];    // value but not valid
        elseif ( is_array($r[$k]) && in_array(false,$r[$k],true) )               $e = C_FORM_OPTION_NOT_VALID;             // array of values but one not valid
        else																	 $e = false;													 
		
	    switch($action) {
        /****/ case C_ACTION_2UPDATE: $u['a'] &= C_ACTION_UPDATE;  // used in js requested action, filter just for action
        break; case C_ACTION_2INSERT: $u['a'] &= C_ACTION_INSERT;
        break; case C_ACTION_2DELETE: $u['a'] &= C_ACTION_DELETE;
        break; case C_ACTION_2COPY  : $u['a'] &= C_ACTION_INSERT;
        break; case C_ACTION_DELETE :
        break; case C_ACTION_UPDATE : 
        break; case C_ACTION_INSERT : 
        break; case C_ACTION_COPY   : 
        break;
        }
        
        if($u['filter'] ===FILTER_VALIDATE_REGEXP) $u['v'] = addslashes($r[$k]); // cant force magic_quotes and regexp
        else                                       $u['v'] = $r[$k];  // put value eventually filtered in $args
        if ($e != false) $u['err'] = $e;    // put error in $args
     
        unset( $u['filter']                 // neeed no more, clean for ajax return
              ,$u['flags'] 
              ,$u['options']
        );
    }
    if( $action & (C_ACTION_UPDATE + C_ACTION_INSERT + C_ACTION_COPY) ) vfTrackCU($args); // add fields to track action
    return bfCheck_Err ($args) ;   
}
// track update,create: add field with value to append  in update,create
function vfTrackCU(&$avar, $b=false) {
    if($b ) {              // "INSERT INTO t_stt_sets ($k) select $f from t_stt_tags where tag_id='$copy_tag_id'";
        $avar["update_by"] = array("v" => "'". /* user_id */ 64 ."'");        // where $f = tag_name, tag_points, '0', '2016-12-01 12:10:01'
        $avar["update_on"] = array("v" => "'". date('Y-m-d G:i:s') ."'");
    }
    else {
        $avar["update_by"] = array("v" => 64); // user_id
        $avar["update_on"] = array("v" => date('Y-m-d G:i:s'));
    }
}
// select count(*) fromtabe where ....
// return count or empty
function sfSQL2Cnt($s) {
    $rs = @mysql_query($s);
    $n=""; if($rs) list($n) = mysql_fetch_row($rs);
    return is_null($n) ? "" : $n;
}
// construct update set: $kv=array('name'=>'abc','tel'=>'123) ,return name='abc',tel='123'
//array_walk($kv,function($v,$k,&$s){},$s) not working nor array_walk($kv,function($v,$k,&$s){},&$s)
//http://php.net/manual/en/language.namespaces.php: The 'use' keyword also applies to closure constructs:
function sfSQLUpdate($kv) {
  $s = "";
  array_walk($kv, function($v,$k) use (&$s) { $s .= ",$k='$v'"; });
  return substr($s,1);
}
// list($k,$v) = afSQLInsert  used by: "INSERT INTO t_stt_sets ($k) VALUES ('$v')" 
function afSQLInsert($r) {
   return array( join(",",array_keys($r)) , join("','",array_values($r)) );
}
function sfSQLErrClean($e) {
    if(false !== stripos($e,"duplicate") ) return substr($e,0,strripos($e, "for")); // Duplicate entry '623-Ball2-xxx (COPY) (COPY)' for key 'set_id_tag_what_tag_how'
    return e;
}
/**
http://stackoverflow.com/questions/18811644/reorder-a-mysql-table
$extra = "set_id=3"
*/
function vfSQL2reorder($table,$fld_id,$fld_order,$extra="") {
    $s = "update $table t join (select *, (@rn := @rn + 1) as rn from $table cross join (select @rn := 0) const";
    if (!empty($extra))  $s .= " where $extra";                                
    $s .= " order by $fld_order,$fld_id) t2 on t.$fld_id = t2.$fld_id";
    //if (!empty($extra))  $s .= " and t2.$extra"; 
    $s .= " set t.$fld_order = t2.rn";
    
    @mysql_query($s); global $dbg;$dbg['reorder']=$s;
}

/*
   a => field mandatory for action, l => label   ,t => type  ,p => placeholder   ,filter & flags & option => filter_input_array(INPUT_POST,$avar) ,h => help text

    define ( "_N" ,C_ACTION_NONE    );
    define ( "_L" ,C_ACTION_LIST    );
    define ( "_O" ,C_ACTION_ORDER   );
   
    define ("_2I" ,C_ACTION_2INSERT );
    define ("_2U" ,C_ACTION_2UPDATE );
    define ("_2D" ,C_ACTION_2DELETE );
    define ("_2C" ,C_ACTION_2COPY   );
   
    define ( "_I" ,C_ACTION_INSERT  );
    define ( "_U" ,C_ACTION_UPDATE  );
    define ( "_D" ,C_ACTION_DELETE  );
    define ( "_C" ,C_ACTION_COPY    );

**/                            
$ga_stats_tags = array(        /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
	  'tag_id'		=> 	array("a"=>   _O    +_2U+_2D+_2C   +_U+_D+_C                    ,"t"=>'hidden'	,"p"=>""                    ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
	),'set_id'		=> 	array("a"=>_L   +_2I            +_I+_U      	  	            ,"t"=>'hidden'	,"p"=>""                    ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
	),'tag_what'	=>	array("a"=>                      _I+_U 	     ,"l"=>"What"	    ,"t"=>'text_dl'	,"p"=>"write new or select" ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             ,"h"=>"max 50 chars"																                                        
	),'tag_how'	    =>	array("a"=>                      _I+_U       ,"l"=>"How"	    ,"t"=>'text'	,"p"=>""                    ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             ,"h"=>"max 50 chars"																                                        
	),'tag_note'	=>	array("a"=>_N	                             ,"l"=>"Note"	    ,"t"=>'text'	,"p"=>""                    ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             ,"h"=>"max 50 chars"																                                        
    ),'tag_points'  =>	array("a"=>_N	                             ,"l"=>"Points"		,"t"=>'text'	,"p"=>""                    ,"filter"=>FILTER_VALIDATE_INT	        ,"flags"=>FILTER_REQUIRE_SCALAR 											                                        
    ),'tag_order'    =>	array("a"=>   _O			                 ,"l"=>"Order"		,"t"=>'hidden'	,"p"=>""                    ,"filter"=>FILTER_VALIDATE_INT	        ,"flags"=>FILTER_REQUIRE_SCALAR 											                                        
	)                                                                                                                                                                                                        
);                                                                                                                                                                                                           
                                                                                                                                                                                                             
$ga_stats_sets = array(        /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                                                                                        
	  'set_id'		=> 	array("a"=>	  _O    +_2U+_2D+_2C   +_U+_D                        ,"t"=>'hidden' ,"p"=>""                    ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR	                                                                       
	),'set_name'	=>  array("a"=>                      _I+_U   +_C ,"l"=>"Set of Tags" ,"t"=>'text'	,"p"=>""                    ,"filter"=>FILTER_VALIDATE_REGEXP       ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^.{3,20}$/")  ,"h"=>"At least 3, max 20 letters, numbers, spaces or underscores."
	),'set_note'	=>	array("a"=>_N                                ,"l"=>"Note"    	 ,"t"=>'text'	,"p"=>""                    ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             ,"h"=>"max 255 chars"																                                        
	)                                                                                                   
);  


if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = array();
	   $dbg = array();
 
	 
    try {
        switch ( $action ) {
        /****/ case 'set_list':   
            
            $s = "select s.*, (select count(*) from t_stt_tags where set_id=s.set_id) as cnt from t_stt_sets s order by s.set_name";
            $rs = mysql_query($s);
			if($rs)while ($r = mysql_fetch_assoc($rs)) 
				$avar[] = $r;        
        
        break; case 'set_2insert':    $action = "set_insert";   // prepare array to form insert = new, change state action		   

            $avar = $ga_stats_sets;
            if(bfCheck_POST ($avar ,C_ACTION_2INSERT)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
           
        break; case 'set_2update':                $c = C_ACTION_2UPDATE; $action = "set_update"; // prepare array to form update = edit, set new action
               case 'set_2copy':   if(empty($c)){ $c = C_ACTION_2COPY;   $action = "set_copy"; } // prepare array to form copy = insert with plus 
               case 'set_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "set_delete"; }
            
            $avar = $ga_stats_sets;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
            
            $set_id = $avar['set_id']['v']; //+_2U+_2D+_2C
            
            $rs = mysql_query("select * from t_stt_sets where set_id='$set_id'" ); 
			if($rs) { 
				$r = mysql_fetch_assoc($rs);
				foreach($avar as $n => $d) { //load values from existing record to update
                    switch($n) {
                    /****/ case "set_name": 
                    
                        if ($c === C_ACTION_2COPY) {
                            $avar[$n]['p'] = "give a new name";  // placeholder!
                            $avar[$n]['v'] = $r[$n]." (COPY)";   // prepare a new name
                         } else  
                            $avar[$n]['v'] = $r[$n];
                         
                    break; default: $avar[$n]['v'] = $r[$n];    // get values
                    }
                }
			}		

        break; case 'set_insert':                 $c = C_ACTION_INSERT; 
               case 'set_copy':    if(empty($c))  $c = C_ACTION_COPY;   
            
            $avar = $ga_stats_sets ;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors               
            
            $r = array();											// costruisco la query
            foreach($avar as $n => $f) {
				switch($n) {
                /****/ case "set_id" : $copy_set_id = $f['v']; // get the original but not use in insert!
                break; default       : $r[$n] = $f['v'];
				}
            }
            list($k,$v) = afSQLInsert($r);

            $s = "INSERT INTO t_stt_sets ($k) VALUES ('$v')";
            $rs = mysql_query($s);

            if ( !$rs ) 			         throw new Exception( sfSQLErrClean(mysql_error()));
            
            $avar['set_id']['v'] = $set_id = mysql_insert_id();
            if( empty( $set_id ))            throw new Exception( "No record inserted: ". mysql_error() );
            
            if($c === C_ACTION_COPY) {  // also copy all tags
                
                vfTrackCU($ga_stats_tags, true);   // no bfCheck_POST here, I have to force track             
                
                $r = array();
                foreach($ga_stats_tags as $n => $f) {
                    switch($n) {
                    /****/ case "tag_id":
                    break; case "set_id": $r[$n] = $set_id;
                    break; default:       $r[$n] = $n;
                    }
                }
  
                $k = join(",",array_keys($r));
                $v = join(",",array_values($r));  // watch it! "," not "','"
                
                $s = "INSERT INTO t_stt_tags ($k) select $v from t_stt_tags where set_id='$copy_set_id'";
                $rs = mysql_query($s);       $dbg['sql'] = $s;
                if ( !$rs ) 			     throw new Exception( mysql_error() );
            }
            
        break; case 'set_update': 
            
            $avar = $ga_stats_sets;
            if(bfCheck_POST ($avar,C_ACTION_UPDATE)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $r = array(); 
			foreach($avar as $n => $f) {
				switch($n) {
                /****/ case "set_id" : $set_id = $f['v'];
                break; default       : $r[$n] = $f['v'];  
				}
			}
                
            $s = "UPDATE t_stt_sets SET ". sfSQLUpdate($r) ." WHERE set_id='$set_id'";
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs ) 			     throw new Exception( sfSQLErrClean(mysql_error()) );
			
        break; case 'set_delete':  
        
            $avar = $ga_stats_sets;
            if(bfCheck_POST ($avar,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $set_id = $avar['set_id']['v'];
            
            $s = "select count(*) from t_stt where tag_id in (select tag_id from t_stt_tags where set_id='$set_id')";
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs ) 			     throw new Exception( mysql_error() );
            
            list($n) = mysql_fetch_row($rs);
            
            if($n >0) throw new Exception( "Cant delete, there are stats with this SET!" );
            else {
                $rs = mysql_query("delete from t_stt_tags where set_id='$set_id'");  if ( !$rs ) throw new Exception( mysql_error() );
                $rs = mysql_query("delete from t_stt_sets where set_id='$set_id'");  if ( !$rs ) throw new Exception( mysql_error() );
            }
   
/*------------------------------*/

   
		break; case 'tag_list': 
			// interesting article to order rows
			
			if(bfCheck_POST ($ga_stats_tags,C_ACTION_LIST)) throw new Exception( C_FORM_ERROR );
			
			$set_id = $ga_stats_tags['set_id']['v'];
			
			//$s = "select t.set_id,tag_id,tag_what,tag_how,tag_points,tag_note,tag_order from t_stt_tags where set_id='$set_id' order by tag_what,tag_order,tag_id";
            $s = "select s.set_id,s.set_name,tag_id,tag_what,tag_how,tag_points,tag_note,tag_order 
                  from t_stt_tags t join t_stt_sets s on t.set_id=s.set_id where t.set_id='$set_id' order by tag_what,tag_order,tag_id";
			$rs = mysql_query($s);
			if($rs)while ($r = mysql_fetch_assoc($rs)) {
				$avar[] = $r;
			}
				        		
		break; case 'tag_order': 
            
            $avar = $ga_stats_tags;
			if(bfCheck_POST ($avar ,C_ACTION_ORDER)) throw new Exception( C_FORM_ERROR );
            
			   $tag_id = $avar['tag_id']['v'];
			$tag_order = (int) $avar['tag_order']['v']; $tag_order--;
			
            //to order rows: http://stackoverflow.com/questions/812630/how-can-i-reorder-rows-in-sql-database
			$rs = mysql_query("update t_stt_tags set tag_order=tag_order+1 where tag_order=$tag_order");
			$rs = mysql_query("update t_stt_tags set tag_order=$tag_order where tag_id='$tag_id'");
			$s = "select set_id,tag_id,tag_what,tag_how,tag_points,tag_note,tag_order from t_stt_tags where set_id=(select set_id from t_stt_tags where tag_id='$tag_id') order by tag_what,tag_order,tag_id ";
			$rs = mysql_query($s);$avar=array();
			if($rs)while ($r = mysql_fetch_assoc($rs)) {
				$avar[] = $r;
			}
			
		break; case 'tag_2insert': $action = "tag_insert";
            
            $avar = $ga_stats_tags;
            if(bfCheck_POST ($avar ,C_ACTION_2INSERT)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
			
			$set_id = $avar['set_id']['v'];
		
            foreach($avar as $n => $d) { //load values from existing record to update                   
                switch($n) {
                /****/ case "tag_order"  : // prepare with order last record
                    $s = "select max(tag_order)+1 from t_stt_tags where set_id='$set_id'"; // max order for set then in insert adjust
                    $avar[$n]['v'] = sfSQL2Cnt( $s ); $dbg['max'] = $s; 
                    
				break; case "tag_what"   :                             
					$s = "select distinct tag_what from t_stt_tags where set_id='$set_id' order by tag_what";
					$rs = mysql_query($s); $o = array();
					
					if($rs)while ($r = mysql_fetch_assoc($rs)) {
						$o[] = $r['tag_what'];
					}
					$avar[$n]['o'] = $o;
					
                break; default           : 
                }
            }
		
        break; case 'tag_2update':                $c = C_ACTION_2UPDATE; $action = "tag_update"; // prepare array to form update = edit, set new action
               case 'tag_2copy':   if(empty($c)){ $c = C_ACTION_2COPY;   $action = "tag_copy"; } // prepare array to form copy = insert with plus 
               case 'tag_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "tag_delete"; }
            
            $avar = $ga_stats_tags;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)	
            
            $tag_id = $avar['tag_id']['v']; //+_2U+_2D+_2C
            
            $rs = mysql_query("select * from t_stt_tags where tag_id='$tag_id'" ); 
			if($rs) { 
				$r = mysql_fetch_assoc($rs);
				foreach($avar as $n => $d) { //load values from existing record to update
                    switch($n) {
                    /****/ case "tag_how": 
                    
                        if ($c === C_ACTION_2COPY) {
                            $avar[$n]['p'] = "give a new name";  // placeholder!
                            $avar[$n]['v'] = $r[$n]." (COPY)";   // prepare a new name
                         } else  
                            $avar[$n]['v'] = $r[$n]; 
                    //     case "tag_order":     // gia fatto in 2prepare
                    break; default: $avar[$n]['v'] = $r[$n];    // get values
                    }
                }
			}	
            
        break; case 'tag_insert':                 $c = C_ACTION_INSERT; 
               case 'tag_copy':    if(empty($c))  $c = C_ACTION_COPY;   
            
            $avar = $ga_stats_tags;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors               
            
            $r = array();											// costruisco la query
            foreach($avar as $n => $f) {
				switch($n) {
                /****/ case "tag_id" : $copy_tag_id = $f['v'];  // get the original but not use in insert!
                break; default       : $r[$n] = $f['v'];
				}
            }
            list($k,$v) = afSQLInsert($r);

            $s = "INSERT INTO t_stt_tags ($k) VALUES ('$v')";
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs ) 			     throw new Exception( sfSQLErrClean(mysql_error()) );
            
            $avar['tag_id']['v'] = $tag_id = mysql_insert_id();
            if( empty( $tag_id ))            throw new Exception( "No record inserted: ". mysql_error() );
                       
            vfSQL2reorder ("t_stt_tags","tag_id","tag_order","set_id='$r[set_id]' and tag_what='$r[tag_what]'");  
 
        break; case 'tag_update': 
            
            $avar = $ga_stats_tags;
            if(bfCheck_POST ($avar ,C_ACTION_UPDATE)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $r = array(); 
			foreach($avar as $n => $f) {
				switch($n) {
                /****/ case "tag_id" : $tag_id = $f['v'];  // not to update
                break; default       : $r[$n] = $f['v'];  
				}
			}
                
            $s = "UPDATE t_stt_tags SET ". sfSQLUpdate($r) ." WHERE tag_id='$tag_id'";
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs ) 			     throw new Exception( sfSQLErrClean(mysql_error()) );
            
            //vfSQL2reorder ("t_stt_tags","tag_id","tag_order","set_id='$r[set_id]' and tag_what='$r[tag_what]'");  
			
        break; case 'tag_delete':  
            
            $avar = $ga_stats_tags;
            if(bfCheck_POST ($avar ,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $r = array(); 
			foreach($avar as $n => $f) {
                $r[$n] = $f['v']; 
            }
            
            $s = "select count(*) from t_stt where tag_id='$r[tag_id]')";
            
            if( ((int) sfSQL2Cnt($s)) >0)  throw new Exception( "Cant delete, there are stats with this TAG!" );
            else {
                $rs = mysql_query("delete from t_stt_tags where tag_id='$r[tag_id]'");  
                if ( !$rs ) throw new Exception( mysql_error() );
                
                vfSQL2reorder ("t_stt_tags","tag_id","tag_order","set_id='$r[set_id]' and tag_what='$r[tag_what]'");   

            }          
                  
        break; default :
            throw new Exception( "action not recognized" );
        } // switch ( $_REQUEST['action']) 
        
         $status = true;
        $message = "Done!";  
        
    } catch (Exception $e) {

		 $status = false;
		$message = $e->getMessage();
	} 
    
	echo  json_encode(array(
	  'status' => $status
	,'message' => $message
	, 'action' => $action 
	,      'l' => $avar
	,      'p' => $_POST
	,    'dbg' => $dbg
	));
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
<!-- jquery mobile -->
<script type="text/javascript" src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script> 
<style>
.error { color:red; }
.p-set-copy {margin-top: 2px;}
/* http://demos.jquerymobile.com/1.4.5/listview-collapsible-item-indented/#&ui-state=dialog */
/* Basic settings */
.ui-li-static.ui-collapsible {
padding: 0;
}
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview,
.ui-li-static.ui-collapsible > .ui-collapsible-heading {
margin: 0;
}
.ui-li-static.ui-collapsible > .ui-collapsible-content {
padding-top: 0;
padding-bottom: 0;
padding-right: 0;
border-bottom-width: 0;
}
/* collapse vertical borders */
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview > li.ui-last-child,
.ui-li-static.ui-collapsible.ui-collapsible-collapsed > .ui-collapsible-heading > a.ui-btn {
border-bottom-width: 0;
}
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview > li.ui-first-child,
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview > li.ui-first-child > a.ui-btn,
.ui-li-static.ui-collapsible > .ui-collapsible-heading > a.ui-btn {
border-top-width: 0;
}
/* Remove right borders */
.ui-li-static.ui-collapsible > .ui-collapsible-heading > a.ui-btn,
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview > .ui-li-static,
.ui-li-static.ui-collapsible > .ui-collapsible-content > .ui-listview > li > a.ui-btn,
.ui-li-static.ui-collapsible > .ui-collapsible-content {
border-right-width: 0;
}
/* Remove left borders */
/* Here, we need class ui-listview-outer to identify the outermost listview */
.ui-listview-outer > .ui-li-static.ui-collapsible .ui-li-static.ui-collapsible.ui-collapsible,
.ui-listview-outer > .ui-li-static.ui-collapsible > .ui-collapsible-heading > a.ui-btn,
.ui-li-static.ui-collapsible > .ui-collapsible-content {
border-left-width: 0;
}
</style>
<script>

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
function vfScrollToAnchor(aname){ 
	$('html,body').animate({scrollTop: $("a[name='"+ aname +"']").offset().top},'slow'); 
}
//$.mobile.silentScroll($("#li-log a[name=id"+m+"]").offset().top-$(".ui-content").offset().top);	
//$("li:contains('004')").offset().top
var scrollTo2 = function(c) {
if($(c).length) $.mobile.silentScroll( $(c).offset().top );
//$.mobile.silentScroll( $(c).offset().top-$(".ui-content").offset().top ); 
}
var isMobile = function() {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	return check; 
}
var v2s = function(s,i) {if(s===void(0))return ''; return (i ? i+'="'+s+'"':s)}
var uniqId = function(p) {return v2s(p)+Math.round(new Date().getTime() + (Math.random() * 100));} // used _.uniqId
function voidif(v) {if (v==='') return void(0); return v;}
function empty(v) { if(typeof(v)==='undefined'||v===null||v===''||v===false||v===0||v==='0') return true;return false }
function sfFormField(f) {

    var isSelected = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "selected"; return ""; }
    var isChecked = function(v,k) { if ( (_.isArray(v) && _.contains(v,k)) || (!_.isArray(v) && v==k) ) return "checked"; return ""; }
    var sfButton = function(v,i) { return '<label> </label><div class="ui-grid-a"><div class="ui-block-a"><input type="submit" value="'+v+'" data-icon="'+i+'"></div>'
                                        + '<div class="ui-block-b"><a href="#" class="ui-btn ui-icon-back ui-btn-icon-left" data-rel="back">Cancel</a></div></div>';
                   }

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
	break; case 'text_dl':  // http://demo.agektmr.com/datalist/
							s = '<label for="'+i+'">'+l+'</label>';   
							s+= '<input type="text" id="'+i+'" name="'+n+'" value="'+v+'" placeholder="'+p+'" '+r+' list="'+i+'-list">';
							s+= '<datalist id="'+i+'-list">';
							for (k in o) s += '<option>'+_.escape(o[k])+'</option>';
							s+= '</datalist>';
	
    break; case 'submit':   s = '<input type="submit" name="'+n+'" value="'+v+'" data-inline="true">';              
    break; case 'button':   s = '<button data-inline="true">'+v+'</button>';
      
    break; case 'insert':   s = sfButton('New','plus');  
    break; case 'update':   s = sfButton('Update','edit'); 
	break; case 'delete':   s = sfButton('Delete','delete');   
    break; case 'copy':     s = sfButton('Copy','camera');      
                            
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
/*
    r = {}
    a = ""
**/
function sfBuidForm(r,a) {
	if (r.status) {
		var h=[],n;

		for (n in r.l) {
			r.l[n].name = n;            
			h.push( sfFormField( r.l[n]) );
		}
		h.push( sfFormField({n:'action',v:r.action}) ); //hidden default
		h.push( sfFormField({t:a}) );
	    return '<form method="post">'+ h.join("") +'</form>';
		
	}
	else return "<h1>Error</h1><p>"+ r.message + "</p>";
}

function vfPopUp(header,body,callBack) {
  
    var i = uniqId('i')
       ,popup = '<div data-role="popup" id="'+ i +'" data-theme="a" class="ui-corner-all" style="min-width:300px;max-width:400px;">'
              + '<div data-role="header"><h2>' + header + '</h2>'
              + '<a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>'
              + '</div><div data-role="content" class="ui-content"><p>'+ body +'</p></div></div>';
    
          
    $( popup ).appendTo( $.mobile.activePage )
              .popup().popup("open")
              .trigger('create')   //.enhanceWithin()
              .on("popupafterclose", function () { 
                $(this).remove(); 
              });
              
    if(callBack && typeof callBack === "function") callBack(i);         
              
}


// o.action, r.l, r.a, r.xxx
function vfActionLoad(o) {

    $.ajax({
           url : $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
          data :  o,  // _.extend(o || {},{action:a}), //_.extend(f,{action:action}),		//$form.serialize(),
          type : 'post',                  
         async : 'true',
      dataType : 'json',
    beforeSend : function()  { $.mobile.loading( "show" ); },
      complete : function()  { $.mobile.loading( "hide" ); },
       success : function(r) { vfActionSwitch(o.action,r); },
         error : function(j,t,e)  { 
                    vfActionSwitch(o.action,{"status":false,l:[],"message":"Server error: "+j.responseText.substr(0,j.responseText.indexOf("{"))  }); 
                 } //jqXHR, textStatus, errorThrown
    }); // ajax      
}

function vfActionSwitch(a,r) {
    var s="",c,h,n
	    m = isMobile()?"-mobile":"";	// different template for mobile
	
	switch(a) {
	/****/ case "set_list": 

		if(r.status) s = _.template($("#page-tag-sets-ul-tmpl"+m).html())({l:r.l});
		else         s = "<li><h1>Error</h1><p>"+ r.message + "</p></li>";	

		$("#page-tag-sets-ul").empty().html( s ).listview("refresh");

		$('.p-set-edit'  ).on('click',function(){ vfActionLoad( {action:'set_2update' ,set_id:$(this).jqmData('set_id')}); return false;});
		$('.p-set-copy'  ).on('click',function(){ vfActionLoad( {action:'set_2copy'   ,set_id:$(this).jqmData('set_id')}); return false;});
		$('.p-set-delete').on('click',function(){ vfActionLoad( {action:'set_2delete' ,set_id:$(this).jqmData('set_id')}); return false;});

		$('.p-set-view'  ).on('click',function(){ 
			$('#page-tag-tags .ui-content').empty(); // avoid view last list in memory till reload (for few seconds)
			vfActionLoad( {action:"tag_list"    ,set_id:$(this).jqmData('set_id'),set_name:$(this).jqmData('set_name')});	
		});
		
	break; case "set_2insert":	             c = "insert"; h = "New"; 
		   case "set_2update":  if(empty(c)){c = "update"; h = "Edit"; }
           case "set_2copy"  :  if(empty(c)){c = "copy";   h = "Copy";   s  = '<h3>Do you want to copy this SET and all his TAGS?</h3>';}
           case "set_2delete":  if(empty(c)){c = "delete"; h = "Delete"; s  = '<h3>Do you want to delete this SET and all his TAGS?</h3><p>This action can not be undone.</p>'; }
           
   	/****/ case "tag_2insert":	if(empty(c)){c = "insert"; h = "New";    }
           case "tag_2update":  if(empty(c)){c = "update"; h = "Edit";   }
           case "tag_2copy"  :  if(empty(c)){c = "copy";   h = "Copy";   s  = '<h3>Do you want to copy this TAG </h3>';}
           case "tag_2delete":  if(empty(c)){c = "delete"; h = "Delete"; s  = '<h3>Do you want to delete this TAG?</h3><p>This action can not be undone.</p>'; }
  
		if(r.status) s +=  sfBuidForm(r,c);
		else       	 s  = "<h1>Error</h1><p>"+ r.message + "</p>";
			
		vfPopUp(h,s,  function(popId) {
            
			$('#'+popId+' form').on('submit', function(){
				var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
				vfActionLoad(d);
				return false;
			});
		});
	
	break; case "set_update":   case "tag_update":
	       case "set_insert":   case "tag_insert":
           case "set_copy":     case "tag_copy":
           case "set_delete":   case "tag_delete": 

		if(r.status) {
			$('.ui-popup').popup('close');
            if (a.substr(0,3) === "set") vfActionLoad({action: "set_list"}); // set or tag
            else if (a === "tag_delete2")  $('#t'+r.p.tag_id).remove(); // simple remove NO! must load reorder!
            else {
                vfActionLoad( _.extend(r.p,{action: "tag_list"} ) );
            }
		}
		else { // errors in form
            $(".ui-popup form .error").remove();
            $('<span class="error">'+r.message+'</span>').appendTo($(".ui-popup form")); // form error
            for (n in r.l) 
                if(!empty(r.l[n].err))
                    error = $( "<label>" )
					.attr( "id", n + "-error" )
					.addClass( "error" )
					.html( r.l[n].err )
                    .appendTo($("#"+n).parent().prev()); 
			
		}
	break; case "tag_list":
	       case "tag_order":
		if(r.status) {
			s = _.template($("#page-tag-tags-tmpl"+m).html())({l:r.l});
			$('#page-tag-tags .ui-content').empty().html( s ).trigger("create");
			
            var set_name = r.p.set_name || r.l[0].set_name || ''
                 ,set_id = r.p.set_id   || r.l[0].set_id   || ''
               ,tag_what = r.p.tag_what || ''
                 ,tag_id = r.p.tag_id   || '';
            
			//if(!empty(r.p.set_name)) $('#page-tag-tags .ui-title').html(r.p.set_name);
			//if(!empty(r.p.set_id))   $('.p-tag-new').attr("data-set_id", r.p.set_id)
            $('#page-tag-tags .ui-title').html(set_name);
            $('.p-tag-new').attr("data-set_id", set_id);
			
			$('.p-tag-edit'  ).on('click',function(){ vfActionLoad( {action:'tag_2update' ,tag_id:$(this).jqmData('tag_id')}); return false;});
			$('.p-tag-copy'  ).on('click',function(){ vfActionLoad( {action:'tag_2copy'   ,tag_id:$(this).jqmData('tag_id')}); return false;});
			$('.p-tag-delete').on('click',function(){ vfActionLoad( {action:'tag_2delete' ,tag_id:$(this).jqmData('tag_id')}); return false;});
			$('.p-tag-order' ).on('click',function(){ vfActionLoad( {action:"tag_order"   ,tag_id:$(this).jqmData('tag_id') ,tag_order:$(this).jqmData('tag_order')});	});
			
			$('.p-tag-new' ).off('click')
							 .on('click',function(){ vfActionLoad( {action:"tag_2insert"   ,set_id:$(this).jqmData('set_id')});	});
			
			//$('.ui-collapsible').eq(0).collapsible( "expand" );
			//if(!empty(r.p.tag_id)) $('#t'+r.p.tag_id).parent().parent().collapsible( "expand" );
			if(!empty(tag_what)) $(".ui-collapsible-heading-toggle:contains('"+tag_what+"')").closest('.ui-collapsible').collapsible( "expand" );  // caming from delete
            else if(!empty(tag_id)) $('#t'+tag_id).closest('.ui-collapsible').collapsible( "expand" ); // caming from elsewhere
			
		}
		else $('<h1>Error:</h1><p class="error">'+r.message+'</p>').appendTo($('#page-tag-tags .ui-content').empty());

	} // end switch
}


$(document).on("pagecreate", "#page-tag-sets", function() {

	$('.p-set-new').on('click',function(){ vfActionLoad( {action:'set_2insert'}); });
	vfActionLoad({action:"set_list"});
});

$(document).on("pageshow", "#page-tag-tags", function() {
});

</script>  
<script type="text/template" id="page-tag-sets-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li>
            <a href="#" data-set_id="<%= i.set_id %>" class="p-set-edit">
                <img src="images/313131-0.png">
                <h1><%= i.set_name %></h1>
                <p><%= i.set_note %></p>
				<p class="ui-li-aside"><strong><%= i.cnt %></strong> Tags</p>
                <div class="ui-btn-left ">
                    <button data-set_id="<%= i.set_id %>" class="p-set-copy ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
                    <button data-set_id="<%= i.set_id %>" class="p-set-delete ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
                </div>
            </a>
            <a href="#page-tag-tags" data-set_id="<%= i.set_id %>" data-set_name="<%= i.set_name %>"class="p-set-view" >Tags</a> 
        </li>
	<% }); %>
</script>
<script type="text/template" id="page-tag-sets-ul-tmpl-mobile">
    <%  _.each(l,function(i){   %>
        <li>
            <a href="#" data-set_id="<%= i.set_id %>" class="p-set-edit">
                
                <h1><%= i.set_name %></h1>
                <p><%= i.set_note %></p>
				
				<!--p class="ui-li-aside"><strong><%= i.cnt %></strong> Tags</p-->
                <div class="ui-btn-right ">
                    <button data-set_id="<%= i.set_id %>" class="p-set-copy ui-btn ui-shadow ui-corner-all ui-icon-camera ui-mini ui-btn-inline ui-btn-icon-notext">Copy</button>
                    <br><button data-set_id="<%= i.set_id %>" class="p-set-delete ui-btn ui-shadow ui-corner-all ui-icon-delete ui-mini ui-btn-inline ui-btn-icon-notext">Delete</button>
                </div>
            </a>
            <a href="#page-tag-tags" data-set_id="<%= i.set_id %>" data-set_name="<%= i.set_name %>"class="p-set-view" >Tags</a> 
        </li>
	<% }); %>
</script>
<script type="text/template" id="page-tag-tags-tmpl">
	<ul data-role="listview" class="ui-listview-outer">
    <% var w="",t=0; %>
	<% _.each(l,function(i){   %>
        <% if(w != i.tag_what) { w = i.tag_what; if(t!=0) { %></ul></li><% } t=0; %> 
			<li data-role="collapsible" data-iconpos="left" data-shadow="false" data-corners="false" data-collapsed-icon="carat-r" data-expanded-icon="carat-d">
			<h2><%= i.tag_what %></h2>
			<ul data-role="listview" data-shadow="false" data-inset="true" data-corners="false" data-split-icon="arrow-u" > 
		<% } %>
				<li id="t<%= i.tag_id %>" <% if(t==0) { %> data-icon="false" <% } %> >
					<a href="#" data-tag_id="<%= i.tag_id %>" class="p-tag-edit"  >
						<img src="images/313131-0.png">
						<h1><%= i.tag_how %></h1>
						<p><%= i.tag_note %></p>
						<p class="ui-li-aside"><strong><%= i.tag_points %></strong> Points</p>
						<div class="ui-btn-left ">
							<button data-tag_id="<%= i.tag_id %>" class="p-tag-copy ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
							<button data-tag_id="<%= i.tag_id %>" class="p-tag-delete ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
						</div>
					</a>
					<% if (t!=0) { %> 
					<a href="#" data-tag_id="<%= i.tag_id %>"  data-tag_order="<%= i.tag_order %>" class="p-tag-order">Order: push up</a> 
					<% } t = i.tag_id; %>
				</li>
	<% }); %>
	<% if(t!=0) { %></ul></li><% } %>
	</ul>
</script>
<script type="text/template" id="page-tag-tags-tmpl-mobile">
	<ul data-role="listview" class="ui-listview-outer">
    <% var w="",t=0; %>
	<% _.each(l,function(i){   %>
        <% if(w != i.tag_what) { w = i.tag_what; if(t!=0) { %></ul></li><% } t=0; %> 
			<li data-role="collapsible" data-iconpos="left" data-shadow="false" data-corners="false" data-collapsed-icon="carat-r" data-expanded-icon="carat-d">
			<h2><%= i.tag_what %></h2>
			<ul data-role="listview" data-shadow="false" data-inset="true" data-corners="false" data-split-icon="arrow-u" > 
		<% } %>
				<li id="t<%= i.tag_id %>"  data-icon="false">
					<a href="#" data-tag_id="<%= i.tag_id %>" class="p-tag-edit"  >
						
						<h1><%= i.tag_how %></h1>
						<p><%= i.tag_note %></p>
						
						<div class="ui-btn-right ">
							<button data-tag_id="<%= i.tag_id %>" class="p-tag-copy ui-btn ui-corner-all ui-icon-camera ui-mini ui-btn-inline ui-btn-icon-notext">Copy</button>
							<br><button data-tag_id="<%= i.tag_id %>" class="p-tag-delete ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ui-btn-inline ui-btn-icon-notext">Delete</button>
						</div>
						
					</a>
					<% if (t!=0) { %> 
					<!--a href="#" data-tag_id="<%= i.tag_id %>"  data-tag_order="<%= i.tag_order %>" class="p-tag-order">Order: push up</a--> 
					<% } t = i.tag_id; %>
				</li>
	<% }); %>
	<% if(t!=0) { %></ul></li><% } %>
	</ul>
</script>

</head>
    <body>
    
        <div id="page-tag-sets" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Sets</h1>
				<a href="#" class="p-set-new" data-icon="plus">New</a>
            </div> <!-- /header -->	
            <div data-role="content" >  
                <ul id="page-tag-sets-ul" data-role="listview" data-filter="true" data-split-icon="carat-r"></ul>
            </div> <!-- /content -->	
        </div> <!-- /page-sets -->
        
		<div id="page-tag-tags" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-tag-sets" data-icon="back" data-rel="back">Sets</a>
				<h1>Tags</h1>
				<a href="#" class="p-tag-new" data-set_id="" data-icon="plus">New</a>		
            </div> <!-- /header -->	
            <div data-role="content" >  

            </div> <!-- //content --> 
        </div> <!-- //page-tag-tags -->
		
    </body>
</html>
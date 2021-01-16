<?php

/* crud.php need crud.js in html!!
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

define("C_FORM_REQUIRED"                ,"crud.err.required");
define("C_FORM_NOT_VALID"               ,"crud.err.not-valid");
define("C_FORM_OPTION_NOT_VALID"        ,"crud.err.option-not-valid");
define("C_FORM_ERROR"                   ,"crud.err.error" );
define("C_FORM_NO_UNIQUE"               ,"crud.err.no-unique" );

/* utils function for choice inputs
**/
function aofPlayerRole()       { return array(""=>"","13"=>"Prop","2"=>"Hoker","45"=>"Second Row","678"=>"#8,Flanker","9"=>"Scrum Half","10"=>"Fly Half","34"=>"Backs");}
function aofNoYes()            { return array(0=>'_no' ,1=>'_yes');}
function aofItaEng()           { return array(0=>'Ita',1=>'Eng');}
function aofUserLevel()        { global $gsUsrLevel; $a=array(); foreach($gsUsrLevel as $d) if(!empty($d['txt'])) $a[$d['id']] = $d['txt']; return $a;}        


/* save image to C_DIR_IMG_USERS return path or false
**/
function sfCropit2Img($dir,$filename,$v)     {
	$d = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $v)); 
    $f = $dir ."/".$filename.".png"; 
    $w777 = file_put_contents($f, $d, LOCK_EX);
	return (empty($w777) ? false : $f);  // $w777 false or 0
}
/* images from dir C_DIR_IMG_USERS
**/
function aofUserPhoto() { 
    $a = glob(dirname(__FILE__)."/". C_DIR_IMG_USERS ."/{*.jpg,*.JPG,*.png,*.PNG,*.gif,*.GIF}", GLOB_BRACE);
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
/*
**/
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
// check if post values are ok over $action (insert,update,delete...)
// args is gaUpsert
function bfCheck_POST (&$args,$action) { 

    $r = filter_input_array(INPUT_POST, $args);   //global  $dbg;$dbg['filter_input_array'] = $r;

    foreach($args as $k => &$u) {   // controllo se tutto è ok
       
		if(isset($u['h'])) $h = ": ".$u['h']; 
        else               $h = "";
			
			//if ( ($r[$k] === false || is_null($r[$k])) && ($u['a'] & $action) )  $e = C_FORM_REQUIRED .": ". @$u['h'];    // no or invalid value but required for action
			if ( empty($r[$k]) && ($u['a'] & $action) )                          $e = C_FORM_REQUIRED .$h;     // no or invalid value but required for action
        elseif ( $action & C_ACTION_2PREPARE | C_ACTION_LIST | C_ACTION_ORDER)   $e = false;                   // no matter field in these actions
        elseif ( is_scalar($r[$k]) && $r[$k] === false )            			 $e = C_FORM_NOT_VALID .$h;    // value but not valid
        elseif ( is_array($r[$k]) && in_array(false,$r[$k],true) )               $e = C_FORM_OPTION_NOT_VALID; // array of values but one not valid
        else																	 $e = false;													 
		
        $u['a'] &= nfAction2Action($action);  // from action to action
        
        if($u['filter'] ===FILTER_VALIDATE_REGEXP  && $u['t'] !='photo') $u['v'] = addslashes($r[$k]); // cant force magic_quotes and regexp
        else                                                             $u['v'] = $r[$k];  // put value eventually filtered in $args
        if ($e != false) $u['err'] = $e;    // put error in $args
        
        if ($u['t'] =='photo') $dbg['photo_b'] = $k; $dbg['photo_c'] = $r[$k];
     
        unset( $u['filter']                 // neeed no more, clean for ajax return
              ,$u['flags'] 
              ,$u['options']
        );                                                                
    } $dbg['args'] = $args;
    if( $action & (C_ACTION_UPDATE + C_ACTION_INSERT + C_ACTION_COPY) ) vfTrackCU($args); // add fields to track action
    return bfCheck_Err ($args) ;   
}
// track update,create: add field with value to append  in update,create
function vfTrackCU(&$avar, $b=false) {
    if($b ) {              // "INSERT INTO t_stt_sets ($k) select $f from t_stt_tags where tag_id='$copy_tag_id'";
        $avar["update_by"] = array("v" => "'". /* user_id */ @$_SESSION[C_LOGIN_USER_ID] ."'");        // where $f = tag_name, tag_points, '0', '2016-12-01 12:10:01'
        $avar["update_on"] = array("v" => "'". date('Y-m-d G:i:s') ."'");
    }
    else {
        $avar["update_by"] = array("v" => @$_SESSION[C_LOGIN_USER_ID]); // user_id
        $avar["update_on"] = array("v" => date('Y-m-d G:i:s'));
    }
}
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
// construct update set: $kv=array('name'=>'abc','tel'=>'123) ,return name='abc',tel='123'
//array_walk($kv,function($v,$k,&$s){},$s) not working nor array_walk($kv,function($v,$k,&$s){},&$s)
//http://php.net/manual/en/language.namespaces.php: The 'use' keyword also applies to closure constructs:
function sfSQLUpdate($kv) {
  $s = "";
  array_walk($kv, function($v,$k) use (&$s) { $s .= ",$k='$v'"; });
  return substr($s,1);
}
/* list($k,$v,$u) = 
   insert into table ($k) values ('$v') on duplicate key update $u";
**/
function sfSQLUpsert($table,$kv) {
    $u="";       
    array_walk($kv, function($v,$k) use (&$u) { $u .= ",$k='$v'"; });
    
    $k = join(",",array_keys($kv));      //fields  
    $v = join("','",array_values($kv));  // values (' $v ')
    $u = substr($u,1);                   

    return "insert into $table ($k) values ('$v') on duplicate key update $u";
}  
// list($k,$v) = afSQLInsert  used by: "INSERT INTO t_stt_sets ($k) VALUES ('$v')" 
function afSQLInsert($kv) {
   return array( join(",",array_keys($kv)) , join("','",array_values($kv)) );
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
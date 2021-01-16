<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
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
           
        break; case 'set_2update':                $c = C_ACTION_2UPDATE; $action = "set_update";   // prepare array to form update = edit, set new action
               case 'set_2copy':   if(empty($c)){ $c = C_ACTION_2COPY;   $action = "set_copy";   } // prepare array to form copy = insert with plus 
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
            
            if ( !empty(sfSQL2Cnt($s)) ) throw new Exception( "Cant delete, there are stats with this TAG!" );
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
<?= sfHead("config",array("js/rr_crud.js"),array("css/rr_crud.css")) ?>
<script type='text/javascript'>
function vfActionSwitch(a,r) {
    var c,h,n,s="",b=0
	   ,m = isMobile()?"-mobile":"";  // different template for mobile
	
    if(!r.status) vfResponseErrorView(r);
    else switch(a) {
	/****/ case "set_list": 

		s = _.template($("#page-tag-sets-ul-tmpl"+m).html())({l:r.l});
		$("#page-tag-sets-ul").empty().html( s ).listview("refresh");
	
		$('#page-tag-sets .p-action' ).off('click').on('click',function(){ vfActionLoad( $(this).data,vfActionSwitch ); });

  	break; case "set_2insert":	     case "tag_2insert": if(empty(c)){c = "insert"; h = "New"   ; }
		   case "set_2update":       case "tag_2update": if(empty(c)){c = "update"; h = "Edit"  ; }
           case "set_2copy"  :  b=1; case "tag_2copy"  : if(empty(c)){c = "copy"  ; h = "Copy"  ; s  = "<h3>Do you want to copy this "+(b ? "SET and all his TAGS" : "TAG")+"?</h3>";}
           case "set_2delete":  b=1; case "tag_2delete": if(empty(c)){c = "delete"; h = "Delete"; s  = "<h3>Do you want to delete this "+(b ? "SET and all his TAGS" : "TAG")+"?</h3><p>This action can not be undone.</p>"; }

		s +=  sfBuidForm(r,c);

		vfPopUp(h,s,  function(popId) {
			
			$('#'+popId+' form').on('submit', function(){
				var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
				vfActionLoad(d,vfActionSwitch);
				return false;
			});
		});
	
	break; case "set_update" :  case "tag_update":
	       case "set_insert" :  case "tag_insert":
           case "set_copy"   :  case "tag_copy"  :
           case "set_delete" :  case "tag_delete": 

		$('.ui-popup').popup('close');
		if (a.substr(0,3) === "set")   vfActionLoad({action: "set_list"},vfActionSwitch); // set or tag
		else if (a === "tag_delete2")  $('#t'+r.p.tag_id).remove(); // simple remove NO! must load reorder!
		else                           vfActionLoad( _.extend(r.p,{action: "tag_list"}),vfActionSwitch );

	break; case "tag_list":
	       case "tag_order":
		   
		s = _.template($("#page-tag-tags-tmpl"+m).html())({l:r.l});
		$('#page-tag-tags-ul').empty().html( s ).listview("refresh");
		
		var set_name = r.p.set_name || r.l[0].set_name || ''
			 ,set_id = r.p.set_id   || r.l[0].set_id   || ''
		   ,tag_what = r.p.tag_what || ''
			 ,tag_id = r.p.tag_id   || '';

		$('#page-tag-tags .ui-title').html(set_name);
        $('#page-tag-tags [data-action="tag_2insert"]').jqmData("set_id", set_id);

		$('#page-tag-tags .p-action' ).off('click').on('click',function(){ vfActionLoad( $(this).data,vfActionSwitch ); });

		if(!empty(tag_what)) $(".ui-collapsible-heading-toggle:contains('"+tag_what+"')").closest('.ui-collapsible').collapsible( "expand" );  // caming from delete
		else if(!empty(tag_id)) $('#t'+tag_id).closest('.ui-collapsible').collapsible( "expand" ); // caming from elsewhere

	} // end switch
}


$(document).on("pagecreate", "#page-tag-sets", function() {
	vfActionLoad({action:"set_list"},vfActionSwitch);
});



</script>  
<script type="text/template" id="page-tag-sets-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li>
            <a href="#" data-action="set_2update" data-set_id="<%= i.set_id %>" class="p-action">
                <img src="images/313131-0.png">
                <h1><%= i.set_name %></h1>
                <p><%= i.set_note %></p>
				<p class="ui-li-aside"><strong><%= i.cnt %></strong> Tags</p>
                <div class="ui-btn-left ">
                    <button data-action="set_2copy"   data-set_id="<%= i.set_id %>" class="p-action ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
                    <button data-action="set_2delete" data-set_id="<%= i.set_id %>" class="p-action ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
                </div>
            </a>
            <a href="#page-tag-tags" data-action="tag_list" data-set_id="<%= i.set_id %>" data-set_name="<%= i.set_name %>" class="p-action" >Tags</a> 
        </li>
	<% }); %>
</script> 
<script type="text/template" id="page-tag-sets-ul-tmpl-mobile">
    <%  _.each(l,function(i){   %>
        <li>
            <a href="#" data-action="set_2update" data-set_id="<%= i.set_id %>" class="p-action">
                
                <h1><%= i.set_name %></h1>
                <p><%= i.set_note %></p>
				
                <div class="ui-btn-right ">
                         <button data-action="set_2copy"   data-set_id="<%= i.set_id %>" class="p-action ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
                    <br/><button data-action="set_2delete" data-set_id="<%= i.set_id %>" class="p-action ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
                </div>
            </a>
            <a href="#page-tag-tags" data-action="tag_list" data-set_id="<%= i.set_id %>" data-set_name="<%= i.set_name %>"  class="p-action">Tags</a> 
        </li>
	<% }); %>
</script>
<script type="text/template" id="page-tag-tags-tmpl"> 

    <% var w="",t=0; %>
	<% _.each(l,function(i){   %>
        <% if(w != i.tag_what) { w = i.tag_what; if(t!=0) { %></ul></li><% } t=0; %> 
			<li data-role="collapsible" data-iconpos="left" data-shadow="false" data-corners="false" data-collapsed-icon="carat-r" data-expanded-icon="carat-d">
			<h2><%= i.tag_what %></h2>
			<ul data-role="listview" data-shadow="false" data-inset="true" data-corners="false" data-split-icon="arrow-u" > 
		<% } %>
				<li id="t<%= i.tag_id %>" <% if(t==0) { %> data-icon="false" <% } %> >
					<a href="#" data-action="" data-tag_id="<%= i.tag_id %>" class="p-action"  >
						<img src="images/313131-0.png">
						<h1><%= i.tag_how %></h1>
						<p><%= i.tag_note %></p>
						<p class="ui-li-aside"><strong><%= i.tag_points %></strong> Points</p>
						<div class="ui-btn-left ">
							<button data-action="tag_2copy"   data-tag_id="<%= i.tag_id %>" class="p-action ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
							<button data-action="tag_2delete" data-tag_id="<%= i.tag_id %>" class="p-action ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
						</div>
					</a>
					<% if (t!=0) { %> 
					<a href="#" data-action="tag_order" data-tag_id="<%= i.tag_id %>"  data-tag_order="<%= i.tag_order %>" class="p-action">Order: push up</a> 
					<% } t = i.tag_id; %>
				</li>
	<% }); %>
	<% if(t!=0) { %></ul></li><% } %>
	
</script>
<script type="text/template" id="page-tag-tags-tmpl-mobile">

    <% var w="",t=0; %>
	<% _.each(l,function(i){   %>
        <% if(w != i.tag_what) { w = i.tag_what; if(t!=0) { %></ul></li><% } t=0; %> 
			<li data-role="collapsible" data-iconpos="left" data-shadow="false" data-corners="false" data-collapsed-icon="carat-r" data-expanded-icon="carat-d">
			<h2><%= i.tag_what %></h2>
			<ul data-role="listview" data-shadow="false" data-inset="true" data-corners="false" data-split-icon="arrow-u" > 
		<% } %>
				<li id="t<%= i.tag_id %>"  data-icon="false">
					<a href="#" data-action="" data-tag_id="<%= i.tag_id %>" class="p-action"  >
						
						<h1><%= i.tag_how %></h1>
						<p><%= i.tag_note %></p>
						
						<div class="ui-btn-right ">
							    <button data-action="tag_2copy"   data-tag_id="<%= i.tag_id %>" class="p-action ui-btn ui-corner-all ui-icon-camera ui-btn-icon-left ui-mini ">Copy</button>
							<br><button data-action="tag_2delete" data-tag_id="<%= i.tag_id %>" class="p-action ui-btn ui-corner-all ui-icon-delete ui-btn-icon-left ui-mini ">Delete</button>
						</div>
					</a>
					<% if (t!=0) { %> 
					<!--a href="#" data-tag_id="<%= i.tag_id %>"  data-tag_order="<%= i.tag_order %>" class="p-tag-order">Order: push up</a--> 
					<% } t = i.tag_id; %>
				</li>
	<% }); %>
	<% if(t!=0) { %></ul></li><% } %> 

</script>

</head>
    <body>
    
        <div id="page-tag-sets" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Sets</h1>
				<a href="#" class="p-action" data-action="set_2insert"  data-icon="plus">New</a>
            </div> <!-- /header -->	
            <div data-role="content" >  
                <ul id="page-tag-sets-ul" data-role="listview" data-filter="true" data-split-icon="carat-r"></ul>
            </div> <!-- /content -->	
        </div> <!-- /page-sets -->
        
		<div id="page-tag-tags" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-tag-sets" data-icon="back" data-rel="back">Sets</a>
				<h1>Tags</h1>
				<a href="#" class="p-action p-action-new" data-action="tag_2insert" data-set_id="" data-icon="plus">New</a>		
            </div> <!-- /header -->	
            <div data-role="content" >  
				<ul id="page-tag-tags-ul" data-role="listview" class="ui-listview-outer"></ul>
            </div> <!-- //content --> 
        </div> <!-- //page-tag-tags -->
		
    </body>
</html>
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
	  'tag_id'		=> 	array("a"=>   _O    +_2U+_2D+_2C   +_U+_D    ,"t"=>'hidden'	                                                    ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
	),'set_id'		=> 	array("a"=>_L   +_2I            +_I+_U   +_C ,"t"=>'hidden'	 	  	                                            ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
	),'tag_what'	=>	array("a"=>                      _I+_U 	 +_C ,"t"=>'text_dl' ,"l"=>_e("What"  )	,"p"=>_e("write new or select") ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             																                                        
	),'tag_how'	    =>	array("a"=>                      _I+_U   +_C ,"t"=>'text'	 ,"l"=>_e("How"   )	                                ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             																                                        
	),'tag_note'	=>	array("a"=>_N	                             ,"t"=>'text'	 ,"l"=>_e("Note"  )	                                ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                            																                                        
    ),'tag_points'  =>	array("a"=>_N	                             ,"t"=>'text'	 ,"l"=>_e("Points")	                                ,"filter"=>FILTER_VALIDATE_INT	        ,"flags"=>FILTER_REQUIRE_SCALAR 											                                        
    ),'tag_order'    =>	array("a"=>   _O			                 ,"t"=>'hidden'	 ,"l"=>_e("Order" )	                                ,"filter"=>FILTER_VALIDATE_INT	        ,"flags"=>FILTER_REQUIRE_SCALAR 											                                        
	)                                                                                                                                                                                                        
);                                                                                                                                                                                                           
                                                                                                                                                                                                             
$ga_stats_sets = array(        /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                                                                                        
	  'set_id'		=> 	array("a"=>	  _O    +_2U+_2D+_2C   +_U+_D    ,"t"=>'hidden'                                                     ,"filter"=>FILTER_VALIDATE_INT          ,"flags"=>FILTER_REQUIRE_SCALAR	                                                                       
	),'set_name'	=>  array("a"=>                      _I+_U   +_C ,"t"=>'text'	,"l"=>_e("Set of Tags") ,"h"=>_e("min 3 max 30 chr"),"filter"=>FILTER_VALIDATE_REGEXP       ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^.{3,20}$/")  
	),'set_note'	=>	array("a"=>_N                                ,"t"=>'text'	,"l"=>_e("Note")    	,"h"=>_e("max 255 chr")     ,"filter"=>FILTER_SANITIZE_MAGIC_QUOTES ,"flags"=>FILTER_REQUIRE_SCALAR                                             					                                        
	)                                                                                                   
);  


if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
	   $dbg = array();
  
    try {
        $t         = substr($action,0,4); $a = ; 
        $avar      = ($t == 'set_'? $ga_stats_sets : $ga_stats_tags);
        $newaction = $t . sfNewActionOrError( substr($action,3) ,$avar);    // set_2insert -> _2insert
        
        switch ( $action ) {
        
        /****/ case 'set_list':   
            
           $ = "select s.*, (select count(*) from t_stat_tags where set_id=s.set_id) as cnt from t_stat_sets s where s.set_deleted=0 order by s.set_name";
           $avar = DB::run($s)->fetchAll();
        
        break; case 'set_2insert':      // prepare array to form insert = new, change state action		   
            
        break; case 'set_2update':      // prepare array to form update = edit, set new action
               case 'set_2copy':        // prepare array to form copy = insert with plus 
               case 'set_2delete':                  
     
            $r = DB::run("select * from t_stat_sets where set_id=?",array( $avar['set_id']['v'] ))->fetch(); 
            if(!$r) throw new Exception( _e("Unknown User id" );
            
			foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "set_name":                 
                    if ($c === C_ACTION_2COPY) {
                        $avar[$n]['p'] = _e("give a new name");  // placeholder!
                        $avar[$n]['v'] = $r[$n]." (COPY)";   // prepare a new name
                     } else  
                        $avar[$n]['v'] = $r[$n];
                     
                break; default: $avar[$n]['v'] = $r[$n];    // get values
                }
			}		

        break; case 'set_insert':       // action to upsert/copy
               case 'set_update':       
               case 'set_copy':         
                    
            $kv = array();											
            foreach($avar as $n => $f) {
		        $kv[$n] = $f['v'];
            }
            
            $avar = afSQLUpsert("t_stat_sets",$kv,"set_id");
            if ( !$avar ) 			     throw new Exception( _e("Save Error"));
            
            if($c === C_ACTION_COPY) {  // also copy all tags
                
                //vfTrackCU($ga_stats_tags, true);   // no bfCheck_POST here, I have to force track             
                
                $kv = array(); 
                foreach($ga_stats_tags as $n => $f) {
                    switch($n) {
                    /****/ case "tag_id": // none, is auto increment
                    break; case "set_id": $kv[$n] = $avar['set_id']['v'];
                    break; default:       $kv[$n] = $n;
                    }
                }
  
                $k = join(",",array_keys($kv));
                $v = join(",",array_values($kv));  // watch it! "," not "','"
                
                $r = DB::run("INSERT INTO t_stat_tags ($k) select $v from t_stt_tags where set_id=?" ,$kv['set_id']);
                if(!$r) throw new Exception( _e("Save copy error"));
            }
			
        break; case 'set_delete':  //new list!
        
            $set_id = $avar['set_id']['v'];

            $set_used = DB::run("select count(*) from t_stat_events where tag_id in (select tag_id from t_stat_tags where set_id=?" ,$set_id)->fetchColumn();

                if($$set_used == false) throw new Exception( _e("Error checking if effaceable"));
            elseif($$set_used >0)       throw new Exception( _e("Cant delete, there are stats with this SET!"));
            else {
                $rs = DB::run("delete from t_stat_tags where set_id=?",array($set_id));  if ( !$rs ) throw new Exception(_e("Error in delete tags"));
                $rs = DB::run("delete from t_stat_sets where set_id=?",array($set_id));  if ( !$rs ) throw new Exception(_e("Error in delete set"));
            }
        
           
/*------------------------------*/
        break; case 'tag_list': 

            $s = "select s.set_id,s.set_name,t.* 
                  from t_stat_tags t join t_stat_sets s on t.set_id=s.set_id where t.set_id=? 
                  order by tag_what,tag_order,tag_id";
            $avar   = DB::run($s,  array( $avar['set_id']['v'] ))->fetchAll();
	  
   		
		break; case 'tag_order': 
            
			   $tag_id = $avar['tag_id']['v'];
			$tag_order = (int) $avar['tag_order']['v']; $tag_order--;  // -1 !!
			
            //to order rows: http://stackoverflow.com/questions/812630/how-can-i-reorder-rows-in-sql-database
			$rs = DB::run("update t_stat_tags set tag_order=tag_order+1 where tag_order=?", array($$tag_order));
			$rs = DB::run("update t_stat_tags set tag_order=? where tag_id=?"             , array($tag_order,$tag_id));
			
			$s = "select * from t_stat_tags where set_id=(select set_id from t_stat_tags where tag_id=?) order by tag_what,tag_order,tag_id ";
			$avar = DB::run($s, array($tag_id))->fetchAll();  // all brothers tag of tag
			
		break; case 'tag_2insert':
            
			$set_id   = $avar['set_id']['v'];
	
            foreach($avar as $n => $d) { //load values from existing record to update                   
                switch($n) {
                /****/ case "tag_order": $avar[$n]['v'] = -1;  // prepare tag_order value = 'last record'+1
				break; case "tag_what" : $avar[$n]['o'] = DB::run("select distinct tag_what from t_stat_tags where set_id=? order by tag_what",$set_id)->fetchAll();                        
                }
            }
		
        break; case 'tag_2update': // prepare array to form update = edit, set new action
               case 'tag_2copy':   // prepare array to form copy = insert with plus 
               case 'tag_2delete': 

            $tag_id = $avar['tag_id']['v']; //+_2U+_2D+_2C

            $r = DB::run("select * from t_stat_tags where tag_id=?", $tag_id )->fetch(); 
            if(!$r) throw new Exception( _e("Unknown Tag id" );
            
            $tag_used = DB::run("select count(*) from t_stat_events where tag_id=?" ,$tag_id)->fetchColumn();
            
			foreach($avar as $n => $d) { //load values from existing record to update
                
                switch($n) {
                /****/ case "tag_how":   if ($c === C_ACTION_2COPY) {
                                            $avar[$n]['p'] = "give a new name";  // placeholder!
                                            $avar[$n]['v'] = $r[$n]." (COPY)";   // prepare a new name
                                         } elseif ($c === C_ACTION_2UPDATE && $tag_used > 0) { 
                                            $avar[$n]['t'] = "disabled";  
                                            $avar[$n]['h'] = _e("This tag is already used in stats, untouchable");}
                                         } else {
                                            $avar[$n]['v'] = $r[$n]; 
                                         }
                break; case "tag_order": if ($c === C_ACTION_2COPY)  
                                            $avar[$n]['v'] = -1;
                break; default:          $avar[$n]['v'] = $r[$n];    // get values
                }
            }
            
        break; case 'tag_insert':  
               case 'tag_update': 
               case 'tag_copy':    
            
            $set_id   = $avar['set_id']['v'];
			$tag_what = $avar['tag_what']['v'];
            
            $kv = array(); foreach($avar as $n => $f) {
                switch($n) { 
                /****/ case "tag_order": if($f['v'] == -1) $kv[$n] = DB::("select max(tag_order)+1 from t_stat_tags where set_id=? and tag_what=?",array($set_id,$tag_what))-> fetch();     
		        break; case "tag_id":    $kv[$n] = ($c === C_ACTION_UPDATE ? $f['v'] : null); // copy || insert, no
		        break; default:          $kv[$n] = $f['v'];
                }
            }
            
            $avar = afSQLUpsert("t_stat_sets",$kv,"tag_id");
            if ( !$avar ) throw new Exception( _e("Save Error"));
            
            // vfSQLite2reorder ("t_stat_tags","tag_id","tag_order","set_id=$set_id and tag_what='$tag_what'"); 
			
        break; case 'tag_delete':  // new list with reorder
                    
            $set_id   = $avar['set_id']['v'];
            $tag_id   = $avar['tag_id']['v'];
			$tag_what = $avar['tag_what']['v'];
            
            $tag_used = DB::run("select count(*) from t_stat_events where tag_id=?" ,$tag_id)->fetchColumn();
            
            if ($tag_used>0 ) throw new Exception(  _e("This tag is already used in stats, untouchable") );
            else {
                $r = DB::run("delete from t_stat_tags where tag_id=?",$tag_id);   
                if ( !$r ) throw new Exception(  );
                
                vfSQLite2reorder ("t_stat_tags","tag_id","tag_order","set_id=$set_id and tag_what='$tag_what'");   

            }       
            
                
        break; default :
            throw new Exception( "action not recognized" );
        } // switch ( $_REQUEST['action']) 
        
         $status = true;
        $message = _e("Done!");  
        
    } catch (Exception $e) {

		 $status = false;
		$message = $e->getMessage();
	} 
    
	echo  json_encode(array(
	  'status' => $status
	,'message' => $message
	, 'action' => $newaction 
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
var _warn_tag_copy = '<?= _e("Do you want to copy this TAG?") ?>'
   ,_warn_tag_del  = '<?= _e("Do you want to delete this TAG?") ?>'
   ,_warn_set_copy = '<?= _e("Do you want to copy this SET and all his TAGS?") ?>'
   ,_warn_set_del  = '<?= _e("Do you want to delete this SET and all his TAGS?") ?>'
   ,_warn_undone   = '<?= _e("This action can not be undone.") ?>';
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
           case "set_2copy"  :  b=1; case "tag_2copy"  : if(empty(c)){c = "copy"  ; h = "Copy"  ; s  = "<h3>"+ (b ? _warn_set_copy : _warn_tag_copy)+"</h3>";}
           case "set_2delete":  b=1; case "tag_2delete": if(empty(c)){c = "delete"; h = "Delete"; s  = "<h3>"+ (b ? _warn_set_del  : _warn_tag_del)+"</h3><p>"+_warn_undone+"</p>"; }

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
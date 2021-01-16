<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
//page_protect(C_LOGIN_USER_LEVEL_ADMIN);  //only admin can access

define("C_REGEXP_LKP_NAME" ,"/^[^\x00-\x1F]{1,255}$/");
define("C_HELP_LKP_NAME" ,"max 255 chars");

$ga_sets = array(             /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                                                                                        
      'lkp_id'      => array("a"=>   _O    +_2U+_2D+_2C   +_U+_D                        ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                        
    ),'lkp_set'     => array("a"=>_N                                                    ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_name'    => array("a"=>                      _I+_U   +_C  ,"l"=>"Set lookup" ,"t"=>'text'    ,"p"=>"" ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_LKP_NAME) ,"h"=>C_HELP_LKP_NAME                             
    ),'lkp_order'   => array("a"=>   _O                              ,"l"=>"Order"      ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                     
    )                                                                                                                                              
);                                                                                                                                                 
                                                                                                                                                   
$ga_tags = array(             /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                               
      'lkp_id'      => array("a"=>   _O    +_2U+_2D+_2C   +_U+_D+_C                     ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_set'     => array("a"=>_L+_O+_2I    +_2D    +_I+_U                           ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_name'    => array("a"=>                      _I+_U        ,"l"=>"Name"       ,"t"=>'text'    ,"p"=>"" ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_LKP_NAME) ,"h"=>C_HELP_LKP_NAME                                                                                                                                               
    ),'lkp_order'   => array("a"=>   _O                              ,"l"=>"Order"      ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                     
    )                                                                                                                                                                                                        
);                                                                                                                                                                                                           


$ga_actions = array(
);

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']); 
      $avar = array();
       $dbg = array();

    try { 
        switch ( $action ) {
        /****/ case 'set_order': if(empty($c)) $c = C_ACTION_ORDER;
        /****/ case 'set_list':  if(empty($c)) $c = C_ACTION_LIST; 
        
            if(bfCheck_POST ($ga_sets ,$c)) throw new Exception( C_FORM_ERROR );
            
              $lkp_set = $ga_tags['lkp_set']['v'] = 0;  // === 0
               $lkp_id = $ga_tags['lkp_id']['v'];
            $lkp_order = (int) $ga_tags['lkp_order']['v']; 
            
            if ($c === C_ACTION_ORDER)
                vfSQL2order("t_lookup","lkp_id",$lkp_id,"lkp_order",$lkp_order,"lkp_set='$lkp_set'") 
            
            $s = "select s.*, (select count(*) from t_lookup where lkp_set=s.lkp_id) as cnt from t_lookup s where s.lkp_set=0 order by s.lkp_order,lkp_name";
            $rs = mysql_query($s);
            if($rs)while ($r = mysql_fetch_assoc($rs)) 
                $avar[] = $r;        
        
        break; case 'set_2insert': if(empty($c)){ $c = C_ACTION_2INSERT; $action = "set_insert"; }   // prepare array to form insert = new, change state action         

            $avar = $ga_sets;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
           
        break; case 'set_2update': if(empty($c)){ $c = C_ACTION_2UPDATE; $action = "set_update"; }// prepare array to form update = edit, set new action
               case 'set_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "set_delete"; }
            
            $avar = $ga_sets;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
                      
            $rs = mysql_query("select * from t_lookup where lkp_id='{$avar['lkp_id']['v']}'" ); 
            if(!$rs) throw new Exception( "No data for id '{$avar['lkp_id']['v']}'" );
            
            $r = mysql_fetch_assoc($rs);
            foreach($avar as $n => $d)      //load values from existing record to update
                $avar[$n]['v'] = $r[$n];    // get values                

        break; case 'set_update':  if(empty($c)) $c = C_ACTION_UPDATE; 
               case 'set_insert':  if(empty($c)) $c = C_ACTION_INSERT; 
            
            $avar = $ga_sets ;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors               
            
            $r = array();                                           // costruisco la query
            foreach($avar as $n => $f) 
                $r[$n] = $f['v'];

            $s = sfSQLUpsert("t_lookup",$r); 
            $rs = mysql_query($s);
            if ( !$rs ) 			     throw new Exception( sfSQLErrClean(mysql_error()));
            
        break; case 'set_delete':  if(empty($c)) $c = C_ACTION_DELETE; 
        
            $avar = $ga_sets;
            if(bfCheck_POST ($avar,$c)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $b = true;
            if($b) $b = bfOut4Table("t_lookup","lkp_set",$avar['lkp_id']['v']);  //select count(*) from t_lookup where lkp_set='{$avar['lkp_id']['v']}'
            //if($b) $b = bfOut4Table($table,$field,$value);
            //if($b) $b = bfOut4Table($table,$field,$value);
            
            if(!$b)  throw new Exception( "Cant delete, there are tags with this SET!" );
            else {
                $rs = mysql_query("delete from t_lookup where lkp_id='{$avar['lkp_id']['v']}'");  
                if ( !$rs ) throw new Exception( mysql_error() );
                
                vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$r[lkp_set]'");   
            }
   
/*------------------------------*/ 

        break; case 'tag_order': if(empty($c)) $c = C_ACTION_ORDER;
        /****/ case 'tag_list':  if(empty($c)) $c = C_ACTION_LIST;       
            
            if(bfCheck_POST ($ga_tags ,$c)) throw new Exception( C_FORM_ERROR );
            
              $lkp_set = $ga_tags['lkp_set']['v'];
               $lkp_id = $ga_tags['lkp_id']['v'];
            $lkp_order = (int) $ga_tags['lkp_order']['v']; 
            
            if ($c === C_ACTION_ORDER)
                vfSQL2order("t_lookup","lkp_id",$lkp_id,"lkp_order",$lkp_order,"lkp_set='$lkp_set'") 
           
            $s = "select (select lkp_name from t_lookup where lkp_id=$lkp_set) as lkp_set_name,lkp_set,lkp_id,lkp_name,lkp_order 
                  from t_lookup where lkp_set='$lkp_set' order by lkp_order,lkp_id";
                  
            $rs = mysql_query($s); $dbg['sql'] = $s;
            if($rs)while ($r = mysql_fetch_assoc($rs)) {
                $avar[] = $r;
            }
            
        break; case 'tag_2insert': $action = "tag_insert";
            
            $avar = $ga_tags;
            if(bfCheck_POST ($avar ,C_ACTION_2INSERT)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
            
            $lkp_set = $avar['lkp_set']['v'];
        
            foreach($avar as $n => $d) { //load values from existing record to update                   
                switch($n) {
                /****/ case "lkp_id"     : // no action
                break; case "lkp_order"  : $avar[$n]['v'] = sfSQL2maxOrder("t_lookup","lkp_order","lkp_set='{$avar['lkp_set']['v']}'"); // max order for set then in insert adjust
                break; default  :          // no others hidden or default
                }
            }
        
        break; case 'tag_2update':                $c = C_ACTION_2UPDATE; $action = "tag_update"; // prepare array to form update = edit, set new action
               case 'tag_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "tag_delete"; }
            
            $avar = $ga_tags;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)    
                    
            $rs = mysql_query("select * from t_lookup where lkp_id='{$avar['lkp_id']['v']}'" ); 
            if($rs) { 
                $r = mysql_fetch_assoc($rs);
                foreach($avar as $n => $d) { //load values from existing record to update
                    $avar[$n]['v'] = $r[$n]; 
                }
            }   
            
        break; case 'tag_insert':  if(empty($c))  $c = C_ACTION_INSERT; 
               case 'tag_update':  if(empty($c))  $c = C_ACTION_UPDATE; 
  
            
            $avar = $ga_tags;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors               
            
            $r = array();                                           // costruisco la query
            foreach($avar as $n => $f) {
                switch($n) {
                /****/ case "tag_id" : if ($c === C_ACTION_UPDATE) $r[$n] = $f['v'];  
                break; default       : $r[$n] = $f['v'];
                }
            }
            $s = sfSQLUpsert("t_lookup",$r); 
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs )                  throw new Exception( sfSQLErrClean(mysql_error()) );
            
            vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$r[lkp_set]'");  
            
        break; case 'tag_delete':  
            
            $avar = $ga_tags;
            if(bfCheck_POST ($avar ,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors 
            
            $r = array(); 
            foreach($avar as $n => $f) {
                $r[$n] = $f['v']; 
            }
            
            $b = true; $i = $avar['lkp_id']['v'];
            if($b) $b = bfOut4Table("t_users","user_role",$i);
            
            if(!$b) throw new Exception( "Cant delete, [{$avar['lkp_name']['v']}] is used in another table!" );
            else {
                $rs = mysql_query("delete from t_lookup where lkp_id='$r[lkp_id]'");  
                if ( !$rs ) throw new Exception( mysql_error() );
                    
                vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$r[lkp_set]'");              
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

/* this is a callback from Ajax, aways with status and error/info
**/
function vfActionSwitch(a,r) {
    var s="",c,h,n,b=0;
    
    if(!r.status) vfResponseErrorView(r);
    else switch(a) {
    /****/ case "set_list": 
        
        s = _.template($("#page-tag-sets-ul-tmpl").html())({l:r.l}); 
        $("#page-tag-sets-ul").empty().html( s ).listview("refresh");

        $('#page-tag-sets .p-action' ).off('click')  // return false if not select view tag_list
                                       .on('click',function(){ vfActionLoad( $(this).data(),vfActionSwitch ); if($(this).jqmData('action') != 'tag_list') return false;});            

        
  	break; case "set_2insert":      case "tag_2insert": if(empty(c)){c = "insert"; h = "New"   ; }
		   case "set_2update":      case "tag_2update": if(empty(c)){c = "update"; h = "Edit"  ; }
           case "set_2copy"  : b=1; case "tag_2copy"  : if(empty(c)){c = "copy"  ; h = "Copy"  ; s  = "<h3>Do you want to copy this "+(b ? "SET and all his TAGS" : "TAG")+"?</h3>";}
           case "set_2delete": b=1; case "tag_2delete": if(empty(c)){c = "delete"; h = "Delete"; s  = "<h3>Do you want to delete this "+(b ? "SET and all his TAGS" : "TAG")+"?</h3><p>This action can not be undone.</p>"; }

		s +=  sfBuidForm(r,c);
            
        vfPopUp(h,s,  function(popId) {
            
            $('#'+popId+' form').on('submit', function(){
                var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
                vfActionLoad(d,vfActionSwitch);
                return false;
            });
        });

    break; case "set_update": case "set_insert": case "set_copy": case "set_delete": 
        
        $('.ui-popup').popup('close');
        vfActionLoad({action: "set_list"},vfActionSwitch);
    
    break; case "tag_update": case "tag_insert": case "tag_copy": case "tag_delete":
             
        $('.ui-popup').popup('close');
        vfActionLoad( _.extend(r.p,{action: "tag_list"}),vfActionSwitch );     
 
    break; case "tag_list":
           case "tag_order":

        s = _.template($("#page-tag-tags-ul-tmpl").html())({l:r.l});
        $('#page-tag-tags-ul').empty().html( s ).listview("refresh");
        
        var lkp_set_name = r.p.lkp_set_name || (_.has(r.l,0) && r.l[0].lkp_set_name) || ''  // from post or from response... get it!
                ,lkp_set = r.p.lkp_set      || (_.has(r.l,0) && r.l[0].lkp_set)      || ''
               ,lkp_name = r.p.lkp_name     || ''
                 ,lkp_id = r.p.lkp_id       || '';
        
        $('#page-tag-tags .ui-title').html(lkp_set_name);                             // set page title
        $('#page-tag-tags [data-action="tag_2insert"]').jqmData('lkp_set', lkp_set);  // set id set for new tags
        
        $('#page-tag-tags .p-action').off('click').on('click',function(){ vfActionLoad( $(this).data(),vfActionSwitch ); return false;}); 

    } // end switch
}


$(document).on("pagecreate", "#page-tag-sets", function() {
    vfActionLoad({action:"set_list"},vfActionSwitch);
});

$(document).on("pageshow", "#page-tag-tags", function() {
    //$('#page-tag-tags-ul').empty(); // avoid view last list in memory till reload (for few seconds)
});

</script>  
<script type="text/template" id="page-tag-sets-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li><a href="#page-tag-tags" data-action="tag_list" data-lkp_set="<%= i.lkp_id %>" data-lkp_set_name="<%= i.lkp_name %>" class="p-action">
                <%= i.lkp_name %>
                <!--span class="ui-li-count"><%= i.cnt %></span-->
                <div class="ui-btn-right ui-nodisc-icon">
                    <button data-action="set_2delete" data-lkp_id="<%= i.lkp_id %>" class="p-action ui-btn ui-shadow ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-inline"></button>
                </div>
                
            </a>
            <a href="#" data-action="set_2update" data-lkp_id="<%= i.lkp_id %>"  class="p-action">Edit</a> 
        </li>
    <% }); %>
</script>
<script type="text/template" id="page-tag-tags-ul-tmpl">
    <% var w="",t=0; %>
    <%  _.each(l,function(i){   %>
        <li <% if(t==0) { %> data-icon="false" <% } %> >
            <a href="#" data-action="tag_2update" data-lkp_id="<%= i.lkp_id %>" class="p-action">
                <%= i.lkp_name %>
                <div class="ui-btn-right ui-nodisc-icon">
                    <button data-action="tag_2delete" data-lkp_id="<%= i.lkp_id %>"  data-lkp_set="<%= i.lkp_set %>" class="p-action ui-btn ui-shadow ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-inline"></button>
                </div>
                
            </a>
            <% if (t!=0) { %> 
            <a href="#" data-action="tag_order" data-lkp_id="<%= i.lkp_id %>"  data-lkp_set="<%= i.lkp_set %>" data-lkp_order="<%= i.lkp_order %>" class="p-action">Order: push up</a> 
            <% } t = i.lkp_id; %>
        </li>
    <% }); %>
</script>

</head>
    <body>
    
        <div id="page-tag-sets" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
                <h1>Lookup</h1>
                <a href="#" data-action="set_2insert" data-icon="plus" class="p-action">New</a>
            </div> <!-- /header --> 
            <div data-role="content" >  
                <ul id="page-tag-sets-ul" data-role="listview" data-filter="true" data-split-icon="edit" ></ul>
            </div> <!-- /content -->    
        </div> <!-- /page-sets -->
        
        <div id="page-tag-tags" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-tag-sets" data-icon="back" >Back</a>
                <h1>Items</h1>
                <a href="#" data-action="tag_2insert" data-lkp_set="" data-icon="plus" class="p-action">New</a>       
            </div> <!-- /header --> 
            <div data-role="content" >  
                <ul id="page-tag-tags-ul"  data-role="listview" data-filter="true" data-split-icon="carat-u" ></ul>
            </div> <!-- //content --> 
        </div> <!-- //page-tag-tags -->
        
    </body>
</html>
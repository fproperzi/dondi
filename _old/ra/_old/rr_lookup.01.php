<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
//page_protect(C_LOGIN_USER_LEVEL_ADMIN);  //only admin can access

$ga_sets = array(             /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                                                                                        
      'lkp_id'      => array("a"=>   _O    +_2U+_2D+_2C   +_U+_D                        ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                        
    ),'lkp_set'     => array("a"=>_N                                                    ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_name'    => array("a"=>                      _I+_U   +_C  ,"l"=>"Set lookup" ,"t"=>'text'    ,"p"=>"" ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^[[:graph:][:space:]]{1,255}$/") ,"h"=>"max 255 chars"                             
    ),'lkp_order'   => array("a"=>   _O                              ,"l"=>"Order"      ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                     
    )                                                                                                                                              
);                                                                                                                                                 
                                                                                                                                                   
$ga_tags = array(             /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/                                               
      'lkp_id'      => array("a"=>   _O    +_2U+_2D+_2C   +_U+_D+_C                     ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_set'     => array("a"=>_L+_O+_2I            +_I+_U                           ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'lkp_name'    => array("a"=>                      _I+_U        ,"l"=>"Name"       ,"t"=>'text'    ,"p"=>"" ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^[[:graph:][:space:]]{1,255}$/") ,"h"=>"max 255 chars"                                                                                                                                               
    ),'lkp_order'   => array("a"=>   _O                              ,"l"=>"Order"      ,"t"=>'hidden'  ,"p"=>"" ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                     
    )                                                                                                                                                                                                        
);                                                                                                                                                                                                           

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']); 
      $avar = array();
       $dbg = array();

    try { 
        switch ( $action ) {
        /****/ case 'set_list':   
            
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
            
            $s = sfSQL2Cnt("select count(*) from t_lookup where lkp_set='{$avar['lkp_id']['v']}'");
            if(!empty($s))  throw new Exception( "Cant delete, there are tags with this SET!" );
            else {
                $rs = mysql_query("delete from t_lookup where lkp_id='{$avar['lkp_id']['v']}'");  
                if ( !$rs ) throw new Exception( mysql_error() );
            }
   
/*------------------------------*/ 

        break; case 'tag_order': if(empty($c)) $c = C_ACTION_ORDER;
        /****/ case 'tag_list':  if(empty($c)) $c = C_ACTION_LIST;       
            
            if(bfCheck_POST ($ga_tags ,$c)) throw new Exception( C_FORM_ERROR );
            
              $lkp_set = $ga_tags['lkp_set']['v'];
               $lkp_id = $ga_tags['lkp_id']['v'];
            $lkp_order = (int) $ga_tags['lkp_order']['v']; $lkp_order--;
            
            if ($c === C_ACTION_ORDER) {
                //to order rows: http://stackoverflow.com/questions/812630/how-can-i-reorder-rows-in-sql-database
                $rs = mysql_query("update t_lookup set lkp_order=lkp_order+1 where lkp_order=$lkp_order");
                $rs = mysql_query("update t_lookup set lkp_order=$lkp_order  where lkp_id='$lkp_id'");
            }
           
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
                break; case "lkp_order"  : // prepare with order last record
                    $s = "select max(lpk_order)+1 from t_lookup where lkp_set='{$avar['lkp_set']['v']}'"; // max order for set then in insert adjust
                    $avar[$n]['v'] = sfSQL2Cnt( $s ); 
                break; default           : 
                }
            }
        
        break; case 'tag_2update':                $c = C_ACTION_2UPDATE; $action = "tag_update"; // prepare array to form update = edit, set new action
               case 'tag_2copy':   if(empty($c)){ $c = C_ACTION_2COPY;   $action = "tag_copy"; } // prepare array to form copy = insert with plus 
               case 'tag_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "tag_delete"; }
            
            $avar = $ga_tags;
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
            
            $avar = $ga_tags;
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors               
            
            $r = array();                                           // costruisco la query
            foreach($avar as $n => $f) {
                switch($n) {
                /****/ case "tag_id" : $copy_tag_id = $f['v'];  // get the original but not use in insert!
                break; default       : $r[$n] = $f['v'];
                }
            }
            list($k,$v) = afSQLInsert($r);

            $s = "INSERT INTO t_stt_tags ($k) VALUES ('$v')";
            $rs = mysql_query($s);       $dbg['sql'] = $s;
            if ( !$rs )                  throw new Exception( sfSQLErrClean(mysql_error()) );
            
            $avar['tag_id']['v'] = $tag_id = mysql_insert_id();
            if( empty( $tag_id ))            throw new Exception( "No record inserted: ". mysql_error() );
                       
            vfSQL2reorder ("t_stt_tags","tag_id","tag_order","lkp_set='$r[lkp_set]' and tag_what='$r[tag_what]'");  
 
        break; case 'tag_update': 
            
            $avar = $ga_tags;
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
            if ( !$rs )                  throw new Exception( sfSQLErrClean(mysql_error()) );
            
            //vfSQL2reorder ("t_stt_tags","tag_id","tag_order","lkp_set='$r[lkp_set]' and tag_what='$r[tag_what]'");  
            
        break; case 'tag_delete':  
            
            $avar = $ga_tags;
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
                
                vfSQL2reorder ("t_stt_tags","tag_id","tag_order","lkp_set='$r[lkp_set]' and tag_what='$r[tag_what]'");   

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
<style>
.p-set-copy {margin-top: 2px;}
</style>
<script type='text/javascript'>


function vfActionSwitch(a,r) {
    var s="",c,h,n;

    switch(a) {
    /****/ case "set_list": 

        if(r.status) {
        
            s = _.template($("#page-tag-sets-ul-tmpl").html())({l:r.l}); 
            $("#page-tag-sets-ul").empty().html( s ).listview("refresh");

            $('.p-set-edit'  ).on('click',function(){ vfActionLoad( {action:'set_2update' ,lkp_id:$(this).jqmData('lkp_id')},vfActionSwitch); return false;});
            $('.p-set-copy'  ).on('click',function(){ vfActionLoad( {action:'set_2copy'   ,lkp_id:$(this).jqmData('lkp_id')},vfActionSwitch); return false;});
            $('.p-set-delete').on('click',function(){ vfActionLoad( {action:'set_2delete' ,lkp_id:$(this).jqmData('lkp_id')},vfActionSwitch); return false;});

            $('.p-set-view'  ).on('click',function(){ 
                $('#page-tag-tags-ul').empty(); // avoid view last list in memory till reload (for few seconds)
                vfActionLoad( {action:"tag_list"    ,lkp_set:$(this).jqmData('lkp_set'),lkp_set_name:$(this).jqmData('lkp_set_name')},vfActionSwitch);   
            });
            
        } 
        else $("#page-tag-sets-ul").empty().html( '<li><h1>Error</h1><p class="error">'+ r.message + '</p></li>' ).listview("refresh"); 
        
    break; case "set_2insert":               c = "insert"; h = "New"; 
           case "set_2update":  if(empty(c)){c = "update"; h = "Edit"; }
           case "set_2copy"  :  if(empty(c)){c = "copy";   h = "Copy";   s  = '<h3>Do you want to copy this SET and all his TAGS?</h3>';}
           case "set_2delete":  if(empty(c)){c = "delete"; h = "Delete"; s  = '<h3>Do you want to delete this SET and all his TAGS?</h3><p>This action can not be undone.</p>'; }
           
    /****/ case "tag_2insert":  if(empty(c)){c = "insert"; h = "New";    }
           case "tag_2update":  if(empty(c)){c = "update"; h = "Edit";   }
           case "tag_2copy"  :  if(empty(c)){c = "copy";   h = "Copy";   s  = '<h3>Do you want to copy this TAG </h3>';}
           case "tag_2delete":  if(empty(c)){c = "delete"; h = "Delete"; s  = '<h3>Do you want to delete this TAG?</h3><p>This action can not be undone.</p>'; }
  
        if(r.status) s +=  sfBuidForm(r,c);
        else         s  = "<h1>Error</h1><p>"+ r.message + "</p>";
            
        vfPopUp(h,s,  function(popId) {
            
            $('#'+popId+' form').on('submit', function(){
                var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
                vfActionLoad(d,vfActionSwitch);
                return false;
            });
        });
    
    break; case "set_update":   case "tag_update":
           case "set_insert":   case "tag_insert":
           case "set_copy":     case "tag_copy":
           case "set_delete":   case "tag_delete": 

        if(r.status) {
            $('.ui-popup').popup('close');
            if (a.substr(0,3) === "set") vfActionLoad({action: "set_list"},vfActionSwitch); // set or tag
            else if (a === "tag_delete2")  $('#t'+r.p.tag_id).remove(); // simple remove NO! must load reorder!
            else {
                vfActionLoad( _.extend(r.p,{action: "tag_list"}),vfActionSwitch );
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
            s = _.template($("#page-tag-tags-ul-tmpl").html())({l:r.l});
            $('#page-tag-tags-ul').empty().html( s ).listview("refresh");
            
            var lkp_set_name = r.p.lkp_set_name || r.l[0].lkp_set_name || ''
                    ,lkp_set = r.p.lkp_set      || r.l[0].lkp_set      || ''
                   ,lkp_name = r.p.lkp_name     || ''
                     ,lkp_id = r.p.lkp_id       || '';
            
            $('#page-tag-tags .ui-title').html(lkp_set_name);  // set page title
            $('.p-tag-new').jqmData('lkp_set', lkp_set);     // set id set for new tags
            
            $('.p-tag-edit'  ).on('click',function(){ vfActionLoad( _.extend({action:'tag_2update'} ,$(this).data()) ,vfActionSwitch ); return false;});
            $('.p-tag-delete').on('click',function(){ vfActionLoad( _.extend({action:'tag_2delete'} ,$(this).data()) ,vfActionSwitch ); return false;});
            $('.p-tag-order' ).on('click',function(){ vfActionLoad( _.extend({action:"tag_order"  } ,$(this).data()) ,vfActionSwitch ); return false;});
            
            $('.p-tag-new'  ).off('click')
                              .on('click',function(){ vfActionLoad( _.extend({action:"tag_2insert"} ,$(this).data()) ,vfActionSwitch ); return false;});
        }
        else //$("#page-tag-tags-ul").empty().html( '<li><h1>Error</h1><p class="error">'+ r.message + '</p></li>' ).listview("refresh"); 
          vfPopUp("Error",'<p class="error">'+ r.message + '</p>');

    } // end switch
}


$(document).on("pagecreate", "#page-tag-sets", function() {

    $('.p-set-new').on('click',function(){ vfActionLoad( {action:'set_2insert'},vfActionSwitch); });
    vfActionLoad({action:"set_list"},vfActionSwitch);
});

$(document).on("pageshow", "#page-tag-tags", function() {
});

</script>  
<script type="text/template" id="page-tag-sets-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li><a href="#page-tag-tags" data-lkp_set="<%= i.lkp_id %>" data-lkp_set_name="<%= i.lkp_name %>" class="p-set-view">
                <%= i.lkp_name %>
                <span class="ui-li-count"><%= i.cnt %></span>
            </a>
            <a href="#" data-lkp_id="<%= i.lkp_id %>"  class="p-set-edit" >Edit</a> 
        </li>
    <% }); %>
</script>
<script type="text/template" id="page-tag-tags-ul-tmpl">
    
    <% var w="",t=0; %>
    <%  _.each(l,function(i){   %>
        <li <% if(t==0) { %> data-icon="false" <% } %> >
            <a href="#" data-lkp_id="<%= i.lkp_id %>" class="p-tag-edit">
                <%= i.lkp_name %>
            </a>
            <% if (t!=0) { %> 
            <a href="#" data-lkp_id="<%= i.lkp_id %>"  data-lkp_set="<%= i.lkp_set %>" data-lkp_order="<%= i.lkp_order %>" class="p-tag-order">Order: push up</a> 
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
                <a href="#" class="p-set-new" data-icon="plus">New</a>
            </div> <!-- /header --> 
            <div data-role="content" >  
                <ul id="page-tag-sets-ul" data-role="listview" data-filter="true" data-split-icon="edit" ></ul>
            </div> <!-- /content -->    
        </div> <!-- /page-sets -->
        
        <div id="page-tag-tags" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-tag-sets" data-icon="back" >Back</a>
                <h1>Items</h1>
                <a href="#" class="p-tag-new" data-lkp_set="" data-icon="plus">New</a>       
            </div> <!-- /header --> 
            <div data-role="content" >  
                <ul id="page-tag-tags-ul"  data-role="listview" data-filter="true" data-split-icon="carat-u" ></ul>
            </div> <!-- //content --> 
        </div> <!-- //page-tag-tags -->
        
    </body>
</html>
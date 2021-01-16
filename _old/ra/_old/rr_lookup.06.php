<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
//page_protect(C_LOGIN_USER_LEVEL_ADMIN);  //only admin can access

define("C_REGEXP_LKP_NAME" ,"/^[^\p{Cc}]{1,128}$/");        define("C_HELP_LKP_NAME"   ,"max 128 chars");
define("C_REGEXP_LKP_TEXT" ,"/^[^\p{Cc}]{1,255}$/");        define("C_HELP_LKP_TEXT"   ,"max 255 chars");


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
      'set_order'   => array("a" => ""           ,"c" => C_ACTION_ORDER     ,"r" =>  &$ga_sets
    ),'set_list'    => array("a" => ""           ,"c" => C_ACTION_LIST      ,"r" =>  &$ga_sets
    ),'set_2insert' => array("a" => "set_insert" ,"c" => C_ACTION_2INSERT   ,"r" =>  &$ga_sets
    ),'set_2update' => array("a" => "set_update" ,"c" => C_ACTION_2UPDATE   ,"r" =>  &$ga_sets
    ),'set_2delete' => array("a" => "set_delete" ,"c" => C_ACTION_2DELETE   ,"r" =>  &$ga_sets
    ),'set_insert'  => array("a" => ""           ,"c" => C_ACTION_INSERT    ,"r" =>  &$ga_sets
    ),'set_update'  => array("a" => ""           ,"c" => C_ACTION_UPDATE    ,"r" =>  &$ga_sets
    ),'set_delete'  => array("a" => ""           ,"c" => C_ACTION_DELETE    ,"r" =>  &$ga_sets
                                                                                     
    ),'tag_order'   => array("a" => ""           ,"c" => C_ACTION_ORDER     ,"r" =>  &$ga_tags
    ),'tag_list'    => array("a" => ""           ,"c" => C_ACTION_LIST      ,"r" =>  &$ga_tags
    ),'tag_2insert' => array("a" => "tag_insert" ,"c" => C_ACTION_2INSERT   ,"r" =>  &$ga_tags
    ),'tag_2update' => array("a" => "tag_update" ,"c" => C_ACTION_2UPDATE   ,"r" =>  &$ga_tags
    ),'tag_2delete' => array("a" => "tag_delete" ,"c" => C_ACTION_2DELETE   ,"r" =>  &$ga_tags
    ),'tag_insert'  => array("a" => ""           ,"c" => C_ACTION_INSERT    ,"r" =>  &$ga_tags
    ),'tag_update'  => array("a" => ""           ,"c" => C_ACTION_UPDATE    ,"r" =>  &$ga_tags
    ),'tag_delete'  => array("a" => ""           ,"c" => C_ACTION_DELETE    ,"r" =>  &$ga_tags
    )
);

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']); 
      $avar = $ga_actions[$action]['r'];
         $c = $ga_actions[$action]['c'];
         $a = $ga_actions[$action]['a'];
       $dbg = array();

    try { 
        
        if(bfCheck_POST ( $avar,$c)) throw new Exception( C_FORM_ERROR );
        foreach($avar as $n => $f)  // create global variables from post: https://eval.in/658522
            $$n = $f['v'];          // $lkp_id, $lkp_set, $lkp_name, $lkp_order
        
        if(empty($lkp_set)) $lkp_set = 0;
        
        switch ( $action ) {
        /****/ case 'set_order':        case 'tag_order':        
                          
            vfSQL2order("t_lookup","lkp_id",$lkp_id,"lkp_order",$lkp_order,"lkp_set='$lkp_set'");
            //vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$lkp_set'");    
            
        /****/ case 'set_list':         case 'tag_list': 

            $s = "select (select lkp_name from t_lookup where lkp_id=$lkp_set) as lkp_set_name
                  ,(select count(*) from t_lookup where lkp_set=l.lkp_id) as cnt
                  ,lkp_set,lkp_id,lkp_name,lkp_order 
                  from t_lookup l where lkp_set='$lkp_set' order by lkp_order,lkp_id";
            
            $rs = mysql_query($s); $avar = array();   $dbg['sql'] = $s;
            if($rs)while ($r = mysql_fetch_assoc($rs)) 
                $avar[] = $r;        
        
        break; case 'set_2insert':      case 'tag_2insert': 
                   
            foreach($avar as $n => $f) { //load values from existing record to update                   
                switch($n) {
                /****/ case "lkp_id"     : // no action
                break; case "lkp_order"  : $avar[$n]['v'] = sfSQL2maxOrder("t_lookup","lkp_order","lkp_set='$lkp_set'"); // max order for set then in insert adjust
                break; default  :          // no others hidden or default
                }
            }
        
        break; case 'set_2update':      case 'tag_2update':
               case 'set_2delete':      case 'tag_2delete':

            $rs = mysql_query("select * from t_lookup where lkp_id='$lkp_id'" ); 
            if(!$rs) throw new Exception( "No data for id '$lkp_id'" );
            
            $r = mysql_fetch_assoc($rs);
            foreach($avar as $n => $d)      //load values from existing record to update
                $avar[$n]['v'] = $r[$n];    // get values                

        break; case 'set_insert':       case 'tag_insert': 
               case 'set_update':       case 'tag_update':         
            
            $r = array();                                           // costruisco la query
            foreach($avar as $n => $f) {
                switch($n) {
                /****/ case "tag_id" : if ($c === C_ACTION_UPDATE) $r[$n] = $f['v'];  
                break; default       : $r[$n] = $f['v'];
                }
            }

            $s = sfSQLUpsert("t_lookup",$r); 
            $rs = mysql_query($s);
            if ( !$rs )           throw new Exception( sfSQLErrClean(mysql_error()));
            
            vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$lkp_set'");                           
            
        break; case 'set_delete':       case 'tag_delete':
            
            $s = "Cant delete, [$lkp_name] linked to '%s' table!";
            
            $t = "t_lookup"; if( bfLink2Table($t,"lkp_set",$lkp_id) ) throw new Exception(  sprintf($s,$t) );
            $t = "t_users";  if( bfLink2Table($t,"user_role",$lkp_id) ) throw new Exception( sprintf($s,$t) );
            
            $rs = mysql_query("delete from t_lookup where lkp_id='$lkp_id'");  
            if ( !$rs ) throw new Exception( mysql_error() );
                
            vfSQL2reorder ("t_lookup","lkp_id","lkp_order","lkp_set='$lkp_set'");       
                                                 
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
    , 'action' => empty($a) ? $action : $a
    ,      'l' => $avar
    ,      'p' => $_POST
    ,    'dbg' => $dbg
    ));
    exit;
}
?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("config",array("js/rr_crud.js","//cdnjs.cloudflare.com/ajax/libs/slipjs/2.0.0/slip.min.js"),array("css/rr_crud.css")) ?>
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

        $('#page-tag-sets .p-action' ).off('click')  // return false if not select view tag_list  href="#"
                                       .on('click',function(){ vfActionLoad( $(this).data(),vfActionSwitch );if($(this).attr("href").length==1) return false;});            

        
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
           case "tag_order":  //console.log(r.dbg.order);

        s = _.template($("#page-tag-tags-ul-tmpl").html())({l:r.l});
        $('#page-tag-tags-ul').empty().html( s ).listview("refresh");

        
        var lkp_set_name = r.p.lkp_set_name || (_.has(r.l,0) && r.l[0].lkp_set_name) || ''  // from post or from response... get it!
                ,lkp_set = r.p.lkp_set      || (_.has(r.l,0) && r.l[0].lkp_set)      || ''
               ,lkp_name = r.p.lkp_name     || ''
                 ,lkp_id = r.p.lkp_id       || '';
        
        $('#page-tag-tags .ui-title').html(lkp_set_name);                             // set page title
        $('#page-tag-tags [data-action="tag_2insert"]').jqmData('lkp_set', lkp_set);  // set id set for new tags
        
        $('#page-tag-tags .p-action').off('click')
									 .on('click',function(){vfActionLoad( $(this).data(),vfActionSwitch );if($(this).attr("href").length==1) return false;}); 

    } // end switch
}


$(document).on("pagecreate", "#page-tag-sets", function() {
    vfActionLoad({action:"set_list"},vfActionSwitch); 
	vfSlipInit("#page-tag-sets-ul","lkp_order",vfActionSwitch)
});
$(document).on("pagecreate", "#page-tag-tags", function() {
    
    Slip('#page-tag-tags-ul');
    
    $('#page-tag-tags-ul')[0].addEventListener('slip:beforewait', function(e){
        if (e.target.classList.contains('slip') ) 
            e.preventDefault();
    }, false);
    $('#page-tag-tags-ul')[0].addEventListener('slip:reorder', function(e){
        e.target.parentNode.insertBefore(e.target, e.detail.insertBefore);
        console.log("move "+$(e.target).text().trim()+" from "+$(e.target).jqmData('lkp_order')+ " -> "+(e.detail.spliceIndex+1));
        $(e.target).jqmData('lkp_order',e.detail.spliceIndex+1);
        vfActionLoad( $(e.target).data(),vfActionSwitch );
        
        return false;
    }, false);
});

$(document).on("pageshow", "#page-tag-tags", function() {

});

</script>  
<script type="text/template" id="page-tag-sets-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li data-action="set_order" data-lkp_id="<%= i.lkp_id %>" data-lkp_order="<%= i.lkp_order %>">
			<a href="#page-tag-tags" data-action="tag_list" data-lkp_set="<%= i.lkp_id %>" data-lkp_set_name="<%= i.lkp_name %>" class="p-action">
                <%= i.lkp_name %>               
            </a>
            <a href="#" class="slip">slip</a> 
        </li>
    <% }); %>
</script>
<script type="text/template" id="page-tag-tags-ul-tmpl">
    <%  _.each(l,function(i){   %>
        <li data-action="tag_order" data-lkp_id="<%= i.lkp_id %>"  data-lkp_set="<%= i.lkp_set %>" data-lkp_order="<%= i.lkp_order %>" >
            <a href="#" data-action="tag_2update" data-lkp_id="<%= i.lkp_id %>" class="p-action">
                <%= i.lkp_name %>         
                <div class="ui-btn-right ui-nodisc-icon">
                    <button data-action="tag_2delete"  data-lkp_id="<%= i.lkp_id %>"  data-lkp_set="<%= i.lkp_set %>" class="p-action ui-btn ui-shadow ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-inline"></button>                
                </div>
            </a>
            <a href="#" class="slip">slip</a> 
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
                <ul id="page-tag-sets-ul" data-role="listview" data-filter="true" data-split-icon="bars" ></ul>
            </div> <!-- /content -->    
        </div> <!-- /page-sets -->
        
        <div id="page-tag-tags" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-tag-sets" data-icon="back" >Back</a>
                <h1>Items</h1>
                <a href="#" data-action="tag_2insert" data-lkp_set="" data-icon="plus" class="p-action">New</a>       
            </div> <!-- /header --> 
            <div data-role="content" >  
                <ul id="page-tag-tags-ul"  data-role="listview" data-filter="true" data-split-icon="bars" ></ul>
            </div> <!-- //content --> 
        </div> <!-- //page-tag-tags -->
        
    </body>
</html>
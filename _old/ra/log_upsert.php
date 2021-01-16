<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
//page_protect(C_LOGIN_USER_LEVEL_ADMIN);  //only admin can access

$gaUpsert = array(               /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
      'user_id'       =>  array("a"=>_L       +_2U+_2D       +_U+_D                            ,"t"=>'hidden'     ,"p"=>""                                             ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'user_photo'    =>  array("a"=>_L                                    ,"l"=>"Photo"       ,"t"=>'photo'      ,"p"=>""                       ,"o"=>aofUserPhoto()  ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_PHOTO))                                                                                      
    ),'user_fullname' =>  array("a"=>_L                   +_I+_U           ,"l"=>"Full name"   ,"t"=>'text'       ,"p"=>""                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^$|^.{2,50}$/")                                 ,"h"=>"max 50"                                                                                                      
    ),'user_name'     =>  array("a"=>_L                                    ,"l"=>"User name"   ,"t"=>'text'       ,"p"=>""                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_NAME)                                    ,"h"=>C_REGEXP_NAME_HELP
    ),'user_email'    =>  array("a"=>_L                   +_I+_U           ,"l"=>"Email"       ,"t"=>'email'      ,"p"=>"you@example.org"                              ,"filter"=>FILTER_VALIDATE_EMAIL  ,"flags"=>FILTER_REQUIRE_SCALAR                                                                               
    ),'user_pwd'      =>  array("a"=>_N                   +_I              ,"l"=>"Password"    ,"t"=>'password'   ,"p"=>C_PLACEOLDER_PWD_INSERT                        ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_PWD))                    ,"h"=>C_REGEXP_PWD_HELP  
    ),'user_tel'      =>  array("a"=>_N                                    ,"l"=>"Tel"         ,"t"=>'text'       ,"p"=>"+39 123456789..."                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_TEL))                    ,"h"=>C_REGEXP_TEL_HELP                       
    ),'user_lang'     =>  array("a"=>_N                                    ,"l"=>"Language"    ,"t"=>'flip'       ,"p"=>""                       ,"o"=>aofItaEng()     ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofItaEng() );}                                                      
    ),'user_level'    =>  array("a"=>_L                   +_I+_U           ,"l"=>"User level"  ,"t"=>'checkbox'   ,"p"=>"choose..."              ,"o"=>aofUserLevel()  ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_ARRAY  ,"options"=>function($v) { return bfInArray($v,aofUserLevel() );}                                                               
    ),'user_role'     =>  array("a"=>_N                                    ,"l"=>"Position"    ,"t"=>'select'     ,"p"=>"choose..."              ,"o"=>aofPlayerRole() ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofPlayerRole() );}                                                      
    ),'user_banned'   =>  array("a"=>_N                                    ,"l"=>"Approved"    ,"t"=>'flip'       ,"p"=>""                       ,"o"=>aofNoYes()      ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}                                                       
    ),'sendEmail'     =>  array("a"=>_N                                    ,"l"=>"Send e-mail" ,"t"=>'flip'       ,"p"=>""                       ,"o"=>aofNoYes()      ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}                                                           
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
       $dbg = array(); 
      
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

            $r = array();                                           // costruisco la query
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

                if($b) $r[$n] = $v;             // solo i valorizzati
            }
            $k = join(",",array_keys($r));
            $v = join("','",array_values($r));

            $rs = mysql_query("INSERT INTO users ($k) VALUES ('$v')");

            if ( !$rs )                    throw new Exception( $sSql .":: ". mysql_error() );
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
            $rs = mysql_query($s);  $dbg['sql'] = $s;
            if ( !$rs ) throw new Exception( mysql_error() );
            
            $avar = $gaUpsert;
            //send email
    
        break; case 'delete':
            
            if(bfCheck_POST ($gaUpsert,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors
            
            $u = $gaUpsert['user_name']['v'];
            $i = $gaUpsert['user_id']['v'];
            
            if($u =='admin')                 throw new Exception( "Cannot delete admin user" ); 
            //$rs = mysql_query("DELETE FROM users WHERE id='$i'"); 
            $rs = mysql_query("update users set banned=1, approved=0 WHERE user_id='$i'");  
            if ( !$rs )                      throw new Exception( mysql_error() );
            if( 0 == mysql_affected_rows() ) throw new Exception( "No record processed width id: '$i" );
        
        break; case 'list':         // list all not deleted
        break; case 'banned':   if(empty($bBanned)) $bBanned = 1;     // list deleted (only for admin)
               case 'approved': if(empty($bBanned)) $bBanned = 0;     // list only approved
            $rs = mysql_query("select  user_id,user_fullname,user_name,user_email,user_photo,user_level from t_users where user_banned=$bBanned order by user_fullname");
            if($rs)while ($r = mysql_fetch_assoc($rs)) {
                $r['user_level'] = sfUserLevel($r['user_level']);  //readable user level for list
                $avar[] = $r;
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
    ,      'r' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;
}
?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("config"
          ,array("js/rr_crud.js"
                ,"//cdn.jsdelivr.net/jquery.cropit/0.5.1/jquery.cropit.js"
                )
          ,array("css/rr_crud.css")
          );
?>
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
            <a href="#page-lg-form" data-action="edit" data-user_id="<%= i.user_id %>" class="ui-btn ui-btn-icon-right ui-icon-carat-r">
                <img src="<%= i.user_photo %>" alt="">
                <h2><%= i.full_name %></h2>
                <p><em><%= i.user_level %></em></p>
                <p>login: <%= i.user_name %>, email: <%= i.user_email %></p>
            </a>
        </li>
    <% }); %>
</script>


<script type='text/javascript'>

function vfActionSwitch(a,r) {
    var s="",c,h,n;	
	
	switch(a) {  
	/****/ case "_2update":  

		if(r.status) s +=  sfBuidForm(r,"update");
		else       	 s  = "<h1>Error</h1><p>"+ r.message + "</p>";
			
		$('#page-lg-config-define .ui-content').empty().html(s).trigger('create');
        $('#page-lg-config-define form').on('submit', function(){
            var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
            vfActionLoad(d,vfActionSwitch);
            return false;
        });
		
	
	break; case "_update":   
		
		$("form .error").remove();
		 
		if(r.status) {
			vfPopUp('Update','<h3>Done</h3><br/><a href="index.php" class="ui-btn ui-btn-b ui-btn-inline ui-icon-check ui-btn-icon-left ui-shadow ui-corner-all">ok</a>');
		}
		else { // errors in form
           
            $('<div class="error ui-field-contain"><label></label>'+r.message+'</div>').appendTo($(".ui-content form")); // form error
            for (n in r.l) 
                if(!empty(r.l[n].err))
                    error = $( "<span>" )
					.attr( "id", n + "-error" )
					.addClass( "error" )
					.html( r.l[n].err )
                    .insertAfter($("#"+n))
					//.appendTo($("#"+n).parent().prev()); 		
		}
	} // end switch
}


$( document ).on( "pagecontainerbeforeshow", function ( e, d ) {
    
    switch( d.toPage.jqmData('url') ) {
    /****/ case "page-lg-list-approved": vfAjax('#page-lg-list-approved-ul','approved');
    break; case "page-lg-list-pending" : vfAjax('#page-lg-list-pending-ul' ,'pending');
    break; case "page-lg-list-banned"  : vfAjax('#page-lg-list-banned-ul'  ,'banned');
    break; case "page-lg-form"         :
        if( $.mobile.activeClickedLink ) {
            switch( $.mobile.activeClickedLink.jqmData('action') || "") { // wich anchor was clicked?
            /****/ case "new" :   vfAjax('#page-lg-form-edit','new');  
            break; case "edit" :  vfAjax('#page-lg-form-edit','edit', {user_id:$.mobile.activeClickedLink.jqmData('user_id')} );   
            break; 
            }
        }
    break;
    }

});


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

function vfAjax11(elemId,action,myData) {
    
    //var f = _($(elemId).serializeArray()||[]).reduce(function(a,f){a[f.name]=f.value;return a;},{}); //http://blog.mysema.com/2012/06/form-data-extraction-with-backbonejs.html
    var f; //http://stackoverflow.com/questions/3277655/how-to-convert-jquery-serialize-data-to-json-object/3277710#3277710
    if (action == "insert" || action == "update") 
            f = _.reduce($(elemId).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
    else    f = myData||{};

    $.ajax({
       url : $.mobile.path.getDocumentUrl(), //$.mobile.path.getDocumentBase(true).pathname,
      data : _.extend(f,{action:action}),       //$form.serialize(),
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
            break; case 'edit':   vfFormBuild(elemId,'update',result.l);
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


//$(document).on("pagebeforeshow", "#page-list-approved",function(e,d) {    vfAjax('#page-list-approved-ul','approved'); });
//$(document).on("pagebeforeshow", "#page-list-pending" ,function(e,d) {    vfAjax('#page-list-pending-ul' ,'pending'); });
//$(document).on("pagebeforeshow", "#page-list-banned"  ,function(e,d) {    vfAjax('#page-list-banned-ul'  ,'banned'); });

    
//$( "#page-form" ).on( "pagecontainerbeforeshow" ,function(e,d) {
$(document).on("pagebeforeshow", "#page-lg-form_"         ,function(e,d) {
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
        <div id="page-lg-list-approved" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
                <h1>Approved</h1>
                <a href="#page-lg-form" data-action="new" data-icon="edit">New</a>
                <div data-role="navbar" data-mini="true" >
                    <ul>
                        <li><a href="#page-lg-list-approved" data-icon="check"    class="ui-btn-active ui-state-persist">Approved</a></li>
                        <li><a href="#page-lg-list-banned"   data-icon="delete"   >Banned</a></li>
                    </ul>
                </div>
            </div>
            <div data-role="content" >  
                <ul id="page-lg-list-approved-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-lg-list -->
        <div id="page-lg-list-banned" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
                <h1>Banned</h1>
                <a href="#page-lg-form" data-action="new" data-icon="edit">New</a>
                <div data-role="navbar" data-mini="true" >
                    <ul>
                        <li><a href="#page-lg-list-approved" data-icon="check"    >Approved</a></li>
                        <li><a href="#page-lg-list-banned"   data-icon="delete"   class="ui-btn-active ui-state-persist">Banned</a></li>
                    </ul>
                </div>
            </div>
            <div data-role="content" >  
                <ul id="page-lg-list-banned-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-lg-list -->
        
        <div id="page-lg-form" data-role="page" data-theme="b">

            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <h1>Edit</h1>
            </div>
            <div data-role="content" >  

                <form id="page-lg-form-edit" data-ajax="false"></form>

            </div>
            
            <div data-role="dialog" id="sure" data-title="Are you sure?">
              <div data-role="content">
                <h3 class="sure-1">???</h3>
                <p class="sure-2">???</p>
                <a href="#" class="sure-do" data-role="button" data-theme="b" data-rel="back">Yes</a>
                <a href="#" data-role="button" data-theme="c" data-rel="back">No</a>
              </div>
            </div><!-- // dialog-sure -->
            
        </div> <!-- //page-lg-form -->

    </body>
</html>
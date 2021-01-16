<?php
require_once ("rr_config.php");
require_once ("rr_crud.php");
//page_protect(C_LOGIN_USER_LEVEL_ADMIN);  //only admin can access

$gaUpsert = array(               /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
      'user_id'       =>  array("a"=>_L       +_2U+_2D       +_U+_D                            ,"t"=>'hidden'     ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
    ),'user_photo'    =>  array("a"=>_L                                    ,"l"=>"Photo"       ,"t"=>'photo'      ,"p"=>""                      ,"d" => C_DEFAULT_USER_IMG       ,"o"=>aofUserPhoto()  ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_PHOTO))                                                                                      
    ),'user_banned'   =>  array("a"=>_N                                    ,"l"=>"Banned"      ,"t"=>'flip'       ,"p"=>""                                                       ,"o"=>aofNoYes()      ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}                                                       
    ),'user_fullname' =>  array("a"=>_L                   +_I+_U           ,"l"=>"Full name"   ,"t"=>'text'       ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>"/^$|^.{2,50}$/")                                 ,"h"=>"min 2, max 50 chars"                                                                                                      
    ),'user_name'     =>  array("a"=>_L                   +_I+_U           ,"l"=>"User name"   ,"t"=>'text'       ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_NAME)                                    ,"h"=>C_REGEXP_NAME_HELP
    ),'user_email'    =>  array("a"=>_L                   +_I+_U           ,"l"=>"Email"       ,"t"=>'email'      ,"p"=>"you@example.org"                                                              ,"filter"=>FILTER_VALIDATE_EMAIL  ,"flags"=>FILTER_REQUIRE_SCALAR                                                                               
    ),'user_pwd'      =>  array("a"=>_N                                    ,"l"=>"Password"    ,"t"=>'password'   ,"p"=>C_PLACEOLDER_PWD_INSERT                                                        ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_PWD))                    ,"h"=>C_REGEXP_PWD_HELP  
    ),'user_tel'      =>  array("a"=>_N                                    ,"l"=>"Tel"         ,"t"=>'text'       ,"p"=>"+39 123456789..."                                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>sfEmptyOrRegExp(C_REGEXP_TEL))                    ,"h"=>C_REGEXP_TEL_HELP                       
    ),'user_lang'     =>  array("a"=>_N                                    ,"l"=>"Language"    ,"t"=>'flip'       ,"p"=>""                      ,"d" => 0                        ,"o"=>aofItaEng()     ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofItaEng() );}                                                      
    ),'user_level'    =>  array("a"=>_L                   +_I+_U           ,"l"=>"User level"  ,"t"=>'mselect'    ,"p"=>"..."                   ,"d" => C_LOGIN_USER_LEVEL_GUEST ,"o"=>aofUserLevel()  ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_ARRAY  ,"options"=>function($v) { return bfInArray($v,aofUserLevel() );}                                                               
    ),'user_role'     =>  array("a"=>_N                                    ,"l"=>"Position"    ,"t"=>'select'     ,"p"=>"..."                                                    ,"o"=>aofPlayerRole() ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofPlayerRole() );}                                                      
    ),'sendEmail'     =>  array("a"=>_N                                    ,"l"=>"Send e-mail" ,"t"=>'flip'       ,"p"=>""                      ,"d" => 0                        ,"o"=>aofNoYes()      ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return bfInArray($v,aofNoYes() );}                                                           
    )                                                                                                   
);                                                                                                     
/* mailing
**/
function bfMailAccessInfo($user_email,$user_fullname,$user_name,$user_pwd) {

	$m = array();
	$m[] = "$user_fullname";
	$m[] = "";
	$m[] = "Welcome to Rugby Assistant, here your login details:";
	$m[] = "";
	$m[] = "User Name: $user_name";
	$m[] = "Password: $user_pwd";
	$m[] = "";
	$m[] = "*****LOGIN LINK*****";
	$m[] = "http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\')."/".C_FILE_LOGIN;
	$m[] = "";
	$m[] = "";
	$m[] = "Thank You\r\nAdministrator\r\n".$_SERVER['HTTP_HOST'];
	$m[] = "______________________________________________________";
	$m[] = "THIS IS AN AUTOMATED RESPONSE";
	$m[] = "***DO NOT RESPOND TO THIS EMAIL***";
	
	$from  = "From: \"Rugby Assistant, User Registration\" <auto-reply@".$_SERVER['HTTP_HOST'].">";
	$from .= "\r\nX-Mailer: PHP/" . phpversion();
	
	return mail($user_email, "Login Details",join("\r\n",$m), $from);
}

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = $gaUpsert;
       $dbg = array(); 
   $message = "Done!";
      
    try {
        switch ( $action ) {
        
        /****/ case 'list':         // list all not deleted
        break; case '_list_banned':   if(empty($bBanned)) $bBanned = 1;     // list deleted (only for admin)
               case '_list_approved': if(empty($bBanned)) $bBanned = 0;     // list only approved
            
            $avar = array();
            $rs = mysql_query("select  user_id,user_fullname,user_name,user_email,user_photo,user_level from t_users where user_banned=$bBanned order by user_fullname");
            if($rs)while ($r = mysql_fetch_assoc($rs)) {
                $r['user_level'] = sfUserLevel($r['user_level']);  //readable user level for list
                $avar[] = $r;
            }   
        
        break; case '_2insert':    $action = "_insert";   // prepare array to form insert = new, change state action		   

            if(bfCheck_POST ($avar ,C_ACTION_2INSERT)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
            foreach($avar as $n => $f)  {       //load values from existing record to update 
                
                switch($n) {
                /****/ case "sendEmail"  : $avar[$n]['v'] = "1";
                break; case "user_email" : $avar[$n]['v'] = uniqId("fake_")."@ra.org";// fake mail
                break; case "user_pwd"   : $avar[$n]['p'] = C_PLACEOLDER_PWD_INSERT;  // no value for pwd
                                           $avar[$n]['v'] = sfGenPwd();               // suggestion for password
                break; case "user_level" : $avar[$n]['v'] = aofSplitUserLevel( C_LOGIN_USER_LEVEL_GUEST );  // guest initial state 
                break; case "user_photo" : $avar[$n]['v'] = C_DEFAULT_USER_IMG;       // generic image
                break; 
                }
			}	
           
        break; case '_2update':                $c = C_ACTION_2UPDATE; $action = "_update"; // prepare array to form update = edit, set new action
               case '_2delete': if(empty($c)){ $c = C_ACTION_2DELETE; $action = "_delete"; }
               //case '_2copy':   if(empty($c)){ $c = C_ACTION_2COPY;   $action = "set_copy"; } // prepare array to form copy = insert with plus 
            
            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
                       
            $rs = mysql_query("select * from t_users where user_id='{$avar['user_id']['v']}'" ); 
			if(!$rs)                     throw new Exception( sfSQLErrClean(mysql_error()) );
            $r = mysql_fetch_assoc($rs);
             
            foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "sendEmail"  : $avar[$n]['v'] = "0";
                break; case "user_pwd"   : $avar[$n]['p'] = C_PLACEOLDER_PWD_UPDATE;  // no value for pwd
                break; case "user_level" : $avar[$n]['v'] = aofSplitUserLevel( $r[$n] );
                break; default           : $avar[$n]['v'] = $r[$n];
                }
			}		

        break; case '_insert':                 $c = C_ACTION_INSERT; 
               case '_update':  if(empty($c))  $c = C_ACTION_UPDATE; 

            if(bfCheck_POST ($avar ,$c)) throw new Exception( C_FORM_ERROR ); // check errors       

            $r = array();											// costruisco la query
            foreach($avar as $n => &$f) {      
                switch($n) {
                /****/ case "user_id"    : if ($c === C_ACTION_UPDATE) $r[$n] = $f['v']; 
                break; case "user_level" : $r[$n] = sfMergeUserLevel($f['v']);
                break; case "user_photo" : $r[$n] = sfCropit2Img(C_DIR_IMG_USERS,$avar['user_name']['v'],$f['v']);
                                           if(false === $r[$n]) throw new Exception( "Errors in write photo in ".C_DIR_IMG_USERS .", try to resize image" );
                break; case "user_pwd"   : 
					
					if ( empty($f['v']) ) {					// password is not given
						switch ($c) {
						/****/ case C_ACTION_INSERT :  
						
							$f['v'] = sfGenPwd();			// create new pwd and force send
							$avar['sendEmail']['v'] = 1;
							
						break; case C_ACTION_UPDATE : 
							if($avar['sendEmail']['v'] == 1) // send email with new password automatically
								$f['v'] = sfGenPwd();        // if sendMailbut forgot real mail
						}
					}
                    $r[$n] = sfPwdHash($f['v']);
                    
                break; case "sendEmail"  : //nop
                                           
                break; case "user_name"  : // unique email and name isUnique($k,$v,$table,$id_name,$id_value = 0) 
                       case "user_email" : if ( !isUnique($n,$f['v'],"t_users","user_id",$avar['user_id']['v']) ) $avar[$n]['err'] = C_FORM_NO_UNIQUE; 
                
                /****/ default :           $r[$n] = $f['v']; 
                break; 
                }   
            }
            
            if (bfCheck_Err ($avar))     throw new Exception( C_FORM_ERROR );  // last check on is unique 
 
            $s = sfSQLUpsert("t_users",$r); $dbg['sql'] = $s;
            $rs = mysql_query($s);
            if ( !$rs ) 			     throw new Exception( sfSQLErrClean(mysql_error()));
            
			if ( $avar['sendEmail']['v'] ) {//$avar['user_email']['v']
/* @@@ */		$b = bfMailAccessInfo("fproperzi@gmail.com",$avar['user_fullname']['v'],$avar['user_name']['v'],$avar['user_pwd']['v']);
				if($b) $message .= "<p>Mail with access data sent to {$avar['user_email']['v']}</p>";
				else   $message .= "<p>but Failed to send mail with access data</p>";
			}	
            
        break; case '_delete':
            
            if(bfCheck_POST ($avar,C_ACTION_DELETE)) throw new Exception( C_FORM_ERROR ); // check errors
            
            $user_name = $avar['user_name']['v'];
            $user_id   = $avar['user_id']['v'];
            
            if($user_name === C_CONFIG_ADMIN_NAME)   throw new Exception( "Cannot delete admin user!" ); 

			function bfOut4Table($table,$user_id) { 
/* @@@ */		return false; 
				return empty(sfSQL2Cnt("select count(*) from $table where user_id='$user_id'"));
			}
			
            //check in medica
            $b = true; //true = delete, false = banned with message
			if($b) $b = bfOut4Table("t_medica"   ,$user_id); // check if user is present in others tables
			if($b) $b = bfOut4Table("t_ex_prgms" ,$user_id); 
			if($b) $b = bfOut4Table("t_access"   ,$user_id); 
			
			if($b) $rs = mysql_query("delete from t_users where user_id='$user_id'"); 			
			else { 
				   $rs = mysql_query("update t_users set user_banned='1' WHERE user_id='$user_id'"); 
				   $message .= "<p>But user [$user_name] is banned not deleted because is linked in other tables in DB</p>";
			}
            if ( !$rs )                      throw new Exception( mysql_error() );
			//if( 0 == mysql_affected_rows() ) throw new Exception( "No record processed width id: '$user_id'" );

        break; default :
            throw new Exception( "action not recognized" );
        } // switch ( $_REQUEST['action']) 
        
         $status = true;
          
        
    } catch (Exception $e) {

         $status = false;
        $message = $e->getMessage();
    } 
    
    echo  json_encode(array(
      'status' => $status
    ,'message' => $message
    , 'action' => $action 
    ,      'l' => $avar
    ,'request' => $_REQUEST
    ,    'dbg' => $dbg
    ,      'e' => error_get_last()
    ));
    exit;
}
?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("config",array("js/rr_crud.js","//cdn.jsdelivr.net/jquery.cropit/0.5.1/jquery.cropit.js"),array("css/rr_crud.css")) ?>
<script type='text/javascript'>
function vfActionSwitch(a,r) {
    var s="",c,h,n;	
	var idPform = "#page-lg-form"
	   ,idPlist = "#page-lg-list";
	
	switch(a) {  
	/****/ case "_list_approved":
           case "_list_banned":
        
		if(r.status) {
            s = _.template($("#tmpl-users-list").html())({l:r.l,a:r.action});
            $('#page-lg-list-ul').empty().html( s ).listview("refresh");
			$('.p-lg-edit').on('click',function(){ vfActionLoad( {action:'_2update',user_id:$(this).jqmData('user_id')} ,vfActionSwitch); });
			
		}
    
    break; case "_2insert": if(empty(c)){c = "insert"; h = "New";  $('.p-lg-delete').addClass('ui-screen-hidden');}  
           case "_2update": if(empty(c)){c = "update"; h = "Edit"; $('.p-lg-delete').removeClass('ui-screen-hidden');}  

		if(r.status) s +=  sfBuidForm(r,c);
		else       	 s  = "<h1>Error</h1><p>"+ r.message + "</p>";
			
		$(idPform+' .ui-content').empty().html(s).trigger('create');
        
        for (n in r.l) { // there is a photo field?
            if (r.l[n].t==='photo')
                vfPhotoFieldCropitEnhance( _.escape(r.l[n].value || r.l[n].v) ); 
        } 
        
        $(idPform+' form').on('submit', function(){

            if ( $('.cropit-image-data').length > 0 ) { // check if cropit is in form and get value
                 var imageData = $('#image-cropper').cropit('export');
                 $('.cropit-image-data').val(imageData);
                 //console.log ("imageData="+imageData.length);
            }
            // serilize all data input
            var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
            vfActionLoad(d,vfActionSwitch);
            return false;
        });
		
	
    break; case "_2delete":   

        // blue list selected + pop confirmation = delete + reload list || cancel + return list
        s  = '<h3>Do you want to delete this User?</h3><p>This action can not be undone.</p>';
		s += sfjqmButton(idPlist,"check yesdelete","Yes, delete");						// href,css,text,more
		s += sfjqmButton(idPform,"back"           ,"Cancel");
		//s += sfjqmButton(idPform,"back","Cancel");
		
        vfPopUp('Warning',s,function(popId){
			$(".yesdelete").click(function() {
				$("#"+popId).popup('close');
				$(idPform+" form input[name='action']").val("_delete").submit();
				return false;
			});	
		});   
    
	break; case "_insert": if(empty(h)) { h = "Edit";   c = idPlist }
	       case "_update": if(empty(h)) { h = "New";    c = idPform }      
           case "_delete": if(empty(h)) { h = "Delete"; c = idPlist }   
		
		$(idPform+" .error").remove();
		  
		if(r.status) {
			s  = '<h3>'+r.message+'</h3><br/>';
			s += sfjqmButton(c,"check","Ok, got it");
			
			vfPopUp(h,s); 
			
		}
		else { // errors in form
            vfPopUp('Error','<h3 class="error">'+r.message+'<br/></h3><br/>');
            //$('<div class="error ui-field-contain"><label></label>'+r.message+'</div>').appendTo($("#page-lg-form form")); // form error
            var i,e;
            for (n in r.l) {
                if(!empty(r.l[n].err)) {
                    e = $( "<span>" ).addClass( "error" ).html( r.l[n].err );
                    switch (r.l[n].t) {
                    /****/ case 'mselect'  : 
                           case 'select'   :
                           case 'flip'     : i = $("select[name='"+n+"[]']");                 e.insertAfter(i);
                    break; case 'checkbox' : 
                           case 'radio'    : i = $("input[name='"+n+"[]']").first().parent(); e.insertBefore(i);
                                             //i = $("input[name='"+n+"[]']").last().parent(); e.insertAfter(i);
                    break; default         : i = $("#"+n);                                    e.insertAfter(i);
                    }
                    
                    
					//.attr( "id", n + "-error" ) 
                }
            }
					//.appendTo($("#"+n).parent().prev()); 		
		}
	} // end switch
}

$(document).on("pagebeforeshow", "#page-lg-list", function() {
    vfActionLoad( {action:'_list_approved'} ,vfActionSwitch);  // inital state
});
$(document).on("pagecreate", "#page-lg-list", function() {
    $('.p-lg-new'     ).on('click',function(){ vfActionLoad( {action:'_2insert'      } ,vfActionSwitch); });
    $('.p-lg-banned'  ).on('click',function(){ vfActionLoad( {action:'_list_banned'  } ,vfActionSwitch); });
    $('.p-lg-approved').on('click',function(){ vfActionLoad( {action:'_list_approved'} ,vfActionSwitch); });
});
$(document).on("pagecreate", "#page-lg-form", function() {
    $('.p-lg-delete'  ).on('click',function(){ vfActionSwitch( '_2delete' ); });
});


</script>
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
            <a href="#page-lg-form" data-user_id="<%= i.user_id %>" class="p-lg-edit ui-btn ui-btn-icon-right ui-icon-carat-r">
                <img src="<%= i.user_photo + uniqId("?") %>" alt="">
                <h2><%= i.user_fullname %></h2>
                <p><em><%= i.user_level %></em></p>
                <p>login: <%= i.user_name %>, email: <%= i.user_email %></p>
            </a>
        </li>
    <% }); %>
</script>

    </head>
    <body>
        <div id="page-lg-list" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
                <h1>Users</h1>
                <a href="#page-lg-form" class="p-lg-new" data-icon="plus">New</a>
                <div data-role="navbar" data-mini="true" >
                    <ul>
                        <li><a href="#" data-icon="check"  class="p-lg-approved ui-btn-active ui-state-persist">Approved</a></li>
                        <li><a href="#" data-icon="delete" class="p-lg-banned">Banned</a></li>
                    </ul>
                </div>
            </div>
            <div data-role="content" >  
                <ul id="page-lg-list-ul" data-role="listview" data-filter="true"></ul>
            </div>
        </div> <!-- //page-lg-list -->
        
        <div id="page-lg-form" data-role="page" data-theme="b">

            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#page-lg-list"  data-icon="back">List</a>
                <h1>Edit</h1>
                <a href="#" class="p-lg-delete" data-icon="delete">Delete</a>
            </div>
            <div data-role="content" >  
            </div>           
        </div> <!-- //page-lg-form -->

    </body>
</html>
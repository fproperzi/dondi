<?php
define("C_PLACEHOLDER_PWD_UPDATE" 		,_e("usr.p.live_empty"));    //lascia vuota per non cambiare
define("C_PLACEHOLDER_PWD_INSERT" 		,_e("usr.p.self_generate")); //auto-generata se vuota
define("C_PLACEHOLDER_MAIL"             ,_e("usr.p.valid_mail"));


/*
"usr.h.enabled" ,"h"=>"Utente abilitato ad accedere"                                                   
"usr.h.name"    ,"h"=>"Nome e cognome: minimo 4 caratteri max 30, lettere, numeri, spazi. No accentate o caratteri strani "
"usr.h.login"   ,"h"=>"minimo 2, max 50 caratteri"                                                                             
"usr.h.pwd"     ,"h"=>"minimo 5 max 20 lettere, caratteri strani (_!@#$%), numeri. No accentate e spazi"  
"usr.h.email"   ,"h"=>"Una mail dell'utente valida"
"usr.h.language","h"=>"Lingua dell'utente"                                                    
"usr.h.level"   ,"h"=>""                                                             
"usr.h.sendmail","h"=>"Vuoi spedirgli una mail con i dati di accesso?"                                                     

**/
                                                                                                                                                                                                                                                                                            
$gaUsers = array(        /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
  'user_id'     =>array("a"=>_L       +_2U+_2D       +_U+_D    ,"t"=>'hidden'                                                                                                    ,"filter"=>FILTER_VALIDATE_INT    ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
),'user_photo'  =>array("a"=>_N                                ,"t"=>"photo"	,"l"=>_e("usr.l.photo"   ) ,"h"=>_e("usr.h.photo"   )                                            ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_PHOTO)   
),'user_enabled'=>array("a"=>_N                                ,"t"=>'flip'     ,"l"=>_e("usr.l.enabled" ) ,"h"=>_e("usr.h.enabled" )                       ,"o"=>aofNoYes()     ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkKey(aofNoYes())     
),'user_name'   =>array("a"=>_L                   +_I+_U       ,"t"=>'text'     ,"l"=>_e("usr.l.name"    ) ,"h"=>_e("usr.h.name"    )                                            ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_NAME)                                                 
),'user_login'  =>array("a"=>_L                   +_I+_U       ,"t"=>'text'     ,"l"=>_e("usr.l.login"   ) ,"h"=>_e("usr.h.login"   )                                            ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_LOGIN)                                                                         
),'user_pwd'    =>array("a"=>_N                                ,"t"=>'password' ,"l"=>_e("usr.l.pwd"     ) ,"h"=>_e("usr.h.pwd"     ) ,"p"=>C_PLACEHOLDER_PWD_INSERT             ,"filter"=>FILTER_VALIDATE_REGEXP ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_PWD_OR_NULL)                               
),'user_email'  =>array("a"=>_L                   +_I+_U       ,"t"=>'email'    ,"l"=>_e("usr.l.email"   ) ,"h"=>_e("usr.h.email"   ) ,"p"=>C_PLACEHOLDER_MAIL                   ,"filter"=>FILTER_VALIDATE_EMAIL  ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                                  
),'user_level'  =>array("a"=>_L                   +_I+_U       ,"t"=>'checkbox' ,"l"=>_e("usr.l.level"   ) ,"h"=>_e("usr.h.level"   )                       ,"o"=>aofUserLevel() ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_ARRAY  ,"options"=>afFilterChkKey(aofUserLevel())
),'user_lang'   =>array("a"=>_L                   +_I+_U       ,"t"=>'select'   ,"l"=>_e("usr.l.lang"    ) ,"h"=>_e("usr.h.lang"    )                       ,"o"=>afLanguages()  ,"filter"=>FILTER_CALLBACK                                        ,"options"=>function($v) { return bfInArray($v,afLanguages());}
),'sendEmail'   =>array("a"=>_N                                ,"t"=>'flip'     ,"l"=>_e("usr.l.sendmail") ,"h"=>_e("usr.h.sendmail")                       ,"o"=>aofNoYes()     ,"filter"=>FILTER_CALLBACK        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkKey(aofNoYes())     
));   
/* pronunciable password (italian stile ... no 'hkwqj') : zobi345
   accetable entropia: https://eval.in/651878
**/
function sfGenPwd(){
    srand ((double)microtime()*1000000);
    
    $c = "bcdfglmnprstvz";  // consonanti da utilizzare
    $v = "aeiou";
  
    $pwd = rand(100,999);  //3 decimali 4 lettere
    
    for($i=0; $i<4; $i+=2) {
        $pwd .= $v[rand(0, strlen($v)-1)];
        $pwd .= $c[rand(0, strlen($c)-1)];
    }
    return strrev($pwd);
}                                                                                                 

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = $gaUsers;
       $dbg = array(); 
            
    try {
        $newaction = sfNewActionOrError($action,$avar);
        switch ( $action ) {
        
        /****/ case '_list':         // list all not deleted
            
            $avar = DB::run("select user_id,user_name,user_login,user_enabled,user_level,user_email,user_photo from t_users order by user_name")->fetchAll();
            foreach($avar as $n => &$f) $f['user_level'] = sfUserLevel($f['user_level']);
    
        break; case '_2insert':    // prepare array to form insert = new, change state action		   

            foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "user_id"       : unset($avar[$n]);  // key auto increment no here!
                break; case "user_enabled"  : $avar[$n]['v'] = "1";
                break; case "user_email"    : $avar[$n]['v'] = uniqId("fake_")."@ra.org";
                break; case "user_pwd"      : $avar[$n]['p'] = C_PLACEHOLDER_PWD_INSERT;  // no value for pwd
                break; case "user_level"    : $avar[$n]['v'] = aofSplitUserLevel( C_LOGIN_USER_LEVEL_GUEST );  // guest initial state 
                break; case "user_photo"    : $avar[$n]['v'] = C_FILE_USER_IMG; 
                break; case "user_lang"     : $avar[$n]['v'] = C_DEFAULT_LOCALE; 
                break; case "sendEmail"     : $avar[$n]['v'] = "1";
                break; 
                }
			}	
           
        break; case '_2update': 
               case '_2delete': 
               case '_2copy':   
                                   
            $r = DB::run("select * from t_users where user_id=?",array( $avar['user_id']['v'] ))->fetch(); 
			if(!$r) throw new Exception( _e("Unknown User id" ));
            
            foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "sendEmail"  : $avar[$n]['v'] = "0";
                break; case "user_pwd"   : $avar[$n]['p'] = C_PLACEHOLDER_PWD_UPDATE;  // no value for pwd
                break; case "user_level" : $avar[$n]['v'] = aofSplitUserLevel( $r[$n] );
                break; default           : $avar[$n]['v'] = $r[$n];
                }
			}		
            
        break; case '_update': 
               case '_insert':        
            
            $kv = array(); $pwd = sfGenPwd(); $bmail = false;
            
            foreach($avar as $n => &$f)  { 
                switch($n) {
                /****/ case "sendEmail"  : $bmail = !empty($f['v']);
                break; case "user_level" : $kv[$n] = sfMergeUserLevel($f['v']);
                break; case "user_pwd"   : if($action == "_update" && empty($f['v']) && $avar['sendEmail']['v'] == 1) {
                                               $f['v'] = $pwd;  // send email with new password automatically
                                           } 
                                           elseif($action == "_insert" && empty($f['v']))  {
                                               $f['v'] = $pwd;  // force pwd if not set
                                           }
                                           if(!empty( $f['v'])) $kv[$n] = sfEncrypt($f['v']);       //encypt new pwd
               // break; case "user_id"    : if($a === "_update") $kv[$n] = $f['v'];
                break; default           : $kv[$n] = $f['v']; 
                }
			}
			$dbg['kv'] = $kv;
            $row = afSQLUpsert("t_users", $kv, array("user_id") );      // _insert: user_id is null -> no upsert only insert
           
            if(!$row) throw new Exception( _e("Save failed, try again.") );
            $avar = $row;
            $avar['user_level'] = sfUserLevel($avar['user_level']);
            
            
            if($row && $bmail /* && $row['user_enabled'] == 1 */) {
                $row['user_pwd']   = sfDecrypt($row['user_pwd']); 
                $row['user_level'] = sfUserLevel($row['user_level']);  // da 0x02 a player... = qualcosa di leggibile

                $b = bfMailLoginInfo($row);

				if($b) $message = '<br>'.sprintf(_e("Saved, Mail to %s sent, please check."),$row['user_email']);          // La password &egrave; stata inviata, prego controlli la sua e-mail
				else   $message = '<br>'.sprintf(_e("Saved but Mail to %s failed.")         ,$row['user_email']);          // Fallito l'invio mail con i dati di accesso, riprovi.";
            }
            else $message = _e("Saved");
            
        break; case '_delete':

            $kv = array();
            foreach($avar as $k => $v) $kv[$k] = $v['v'];
            $avar = $kv;
            
            $rs = DB::run("select user_login from t_users where user_id=?", array($avar['user_id']))->fetchColumn();
            if($rs == C_DEFAULT_ADMIN_LOGIN) throw new Exception( _e("Can not delete Admin.") );
            
            $rs = DB::run("delete from t_users where user_id=?", array($avar['user_id']));
            
            $message = _e("Deleted");

        break; default :
            throw new Exception( _e("Unrecognized action.") );
        } // switch ( $_REQUEST['action']) 
        
        $status = true;
        if (empty($message)) $message = _e("Done!");  
          
        
    } catch (Exception $e) { 

         $status = false;
        $message = $e->getMessage();
    } 
    
    echo  json_encode(array(
      'status' => $status
    ,'message' => $message
    , 'action' => @$newaction 
    ,      'l' => $avar  // list: all records, 2update 2insert...: 1 readble record
    ,'request' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;

}

?>
<!DOCTYPE html> 
<html><head>
<?= sfHead(_e("Users"),array("//cdnjs.cloudflare.com/ajax/libs/cropit/0.5.1/jquery.cropit.min.js")); ?>

</head>
<body>
<div id="p-users-list" data-role="page" data-theme="b">
    <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
        <a href="./" data-direction="reverse" data-icon="home" data-iconpos="left" data-ajax="false"><?= _e("Home") ?></a>
        <h1>Utenti</h1>
        <a href="#p-users-form?action=_2insert" data-icon="plus" data-iconpos="right"><?= _e("New") ?></a>
    </div>
    <div data-role="content" >  
        <ul data-role="listview" data-split-icon="delete" data-filter="true"></ul>
    </div>
    <div data-role="footer" data-position="fixed" data-tap-toggle="false">
        <div data-role="navbar">
            <ul>
                <li><a href="#" data-icon="check"     data-enabled="1" class="b-pending"><?= _e("Enabled") ?></a></li>
                <li><a href="#" data-icon="forbidden" data-enabled="0" class="b-pending"><?= _e("Disabled") ?></a></li>
            </ul>
        </div>
    </div>

</div> <!-- //page-lg-list -->

<div id="p-users-form" data-role="page" data-theme="b">

    <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
        <a href="#p-users-list?action=_list" data-direction="reverse" data-icon="carat-l" data-iconpos="left" ><?= _e("List") ?></a>
        <h1></h1>
        <a href="#" data-action="_2upsert" class="b-action" data-icon="check" data-iconpos="right"><?= _e("Save") ?></a>
    </div>
    <div data-role="content" > </div>           

</div> <!-- //page-lg-form -->
<script type="text/template" id="tmpl_users_list">
	<% _.each(jutt, function(i){   %>
		<li><a href="<%=jpag%>?action=_2update&user_id=<%=i.user_id %>">
		<img src="<%=i.user_photo %>">
		<h2><%-i.user_name %></h2>
		<p><%-i.user_login %>, <%-i.user_email %></p>
		<p><%-i.user_level %></p>
		</a><a href="#" data-action="_2delete" data-user_id="<%=i.user_id %>" class="b-action">
		</a></li>
	<% }); %>
</script>
<script type='text/javascript'>

function vfActionSwitch(a,r) {
    var s="",c,h,i,j,n,g;	
	var idPagForm   = '#p-users-form'
	   ,idPagList   = '#p-users-list'
       ,mdata       = 'data_users'
	   ,mtmpl       = 'tmpl_users_list';
	var fld_id      = 'user_id'
	   ,fld_orderby = 'user_name'
	   ,fld_filter  = 'user_enabled'
	   ,fld_mapo    = ['user_level'];  // map options -> no:  'user_enabled'
	   
	var vfListModify = function (u) { // to change list without reload. U=upsert=true
        if(!bfRspOK(r)) return;
		var d = r.l;
		if(_.isObject(r.l) && _.isObject(r.l[fld_id])) 
		    d = _.reduce(r.l,function (a,v,k){a[k]= _.isObject(v.o)&&_.includes(fld_mapo,k)? _.values(_.pick(v.o,v.v)).join() : v.v;return a;},{});
        
        _.remove($.mobile[mdata], (v) => v[fld_id] == d[fld_id] );                                           // delete record
        if (u) {                                                                     					// add new modifyed
            $.mobile[mdata].push(d);                                                					//was: _.sortBy($.mobile[list],orderby);
            $.mobile[mdata]=_.sortBy($.mobile[mdata],function (i) { return i[fld_orderby].toLowerCase(); }); 
        } 
		vfActionSwitch( '_pending'); //reload list
		// $(idPagList +' .b-pending:jqmData(enabled="'+e+'")').trigger("click");      // reload list
    }
    

    switch(a) {
	/****/ case "_list":
	
        if(!bfRspOK(r)) return vfRspKoPop(r,'{{./:home:<?= _e("Home") ?>:}}')
        $.mobile[mdata] = r.l;            // data
        $(idPagList +' .b-pending:jqmData(enabled="1")').trigger("click"); //forzo quelli ad 1
		
    break; case "b-action": 
		if(!_.isElement(r)) return; else i = _.clone( $(r).data() );
		if(i.action == '_2delete') vfActionSwitch('_2delete', i );
		else {
		    vfPhotoFieldExport();       // export photo field
		    vfActionLoad( $(idPagForm +' form').serializeArray(),vfActionSwitch);
		}
	
	break; case "_2delete": vfPopUpDeleteConfirm( r,vfActionSwitch);
    break; case "_2insert": if(empty(h)) h = '<?= _e("New")    ?>';
           case "_2update": if(empty(h)) h = '<?= _e("Update") ?>';

	    $(idPagForm +' .ui-title').html(h);
		$(idPagForm +' .ui-content').empty().html(/**/sfBuidForm(r)/**/).trigger('create');
		
		vfPhotoFieldEnhance(r,80,80);
		
        // $(idPagForm +' form').on('submit', function(){
        //     vfActionLoad($(this).serializeArray(),vfActionSwitch);  //var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
        //     return false;
        // });
		
    break; case "_delete" : vfActionErrorOk(r,idPagList,'<?= _e("Delete") ?>',idPagList); vfListModify(false);
   	break; case "_insert" : vfActionErrorOk(r,idPagForm,'<?= _e("New")    ?>',idPagList); vfListModify(true);
	break; case "_update" : vfActionErrorOk(r,idPagForm,'<?= _e("Update") ?>',idPagForm); vfListModify(true);

    break; case "_pending": 
		if(_.isElement(r)) {
			$(idPagList+' .b-pending').removeClass("ui-state-persist ui-btn-active"); 
			$(r).addClass( "ui-state-persist ui-btn-active" );
		} 
		i = $(idPagList+' .b-pending.ui-btn-active').jqmData('enabled');
		j = {
			 jutt : _.filter( $.mobile[mdata], function(j){return j[fld_filter] == i;})    // strict version: {user_enabled:parseInt(i)})
			,jpag : idPagForm
		}
		if($.mobile[mtmpl] === void(0)) $.mobile[mtmpl] = _.template( $('#'+mtmpl).html() );
		$(idPagList+' .ui-content ul').empty().html( $.mobile[mtmpl](j) ).listview("refresh");
		$(idPagList+' .b-action').off('click').on('click',function(e,d){vfActionSwitch( 'b-action', this );});
		//$.mobile.silentScroll(0);
	
	} // end switch
}
$.mobile.document.on("pagecreate" ,"#p-users-list" ,function(a,b,c) { 
    vfActionLoad( {action:'_list'}  ,vfActionSwitch); });
$.mobile.document.on("pageshow"   ,"#p-users-form" ,function() { vfActionLoad($.mobile.pageData  ,vfActionSwitch); });
$.mobile.document.on("click" ,"#p-users-list .b-pending" ,function(){ 
    vfActionSwitch( '_pending', this ); });
$.mobile.document.on("click","#p-users-form .b-action"  ,function(){ vfActionSwitch( 'b-action', this ); });

</script>
</body>
</html>
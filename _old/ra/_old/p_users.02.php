<?php
define("C_PLACEHOLDER_PWD_UPDATE" 		,_e("** live empty to not change"));    //lascia vuota per non cambiare
define("C_PLACEHOLDER_PWD_INSERT" 		,_e("** self-generate if empty"));      //auto-generata se vuota
define("C_PLACEHOLDER_MAIL"             ,_e("tua@mail.qui"))

/*
"usr.h.enabled" ,"h"=>"Utente abilitato ad accedere"                                                   
"usr.h.name"    ,"h"=>"Nome e cognome: minimo 4 caratteri max 30, lettere, numeri, spazi. No accentate o caratteri strani "
"usr.h.login"   ,"h"=>"minimo 2, max 50 caratteri"                                                                             
"usr.h.pwd"     ,"h"=>"minimo 5 max 20 lettere, caratteri strani (_!@#$%), numeri. No accentate,spazi"  
"usr.h.email"   ,"h"=>"Una mail dell'utente valida"
"usr.h.language","h"=>"Lingua dell'utente"                                                    
"usr.h.level"   ,"h"=>""                                                             
"usr.h.sendmail","h"=>"Vuoi spedirgli una mail con i dati di accesso?"                                                     

**/
                                                                                                                                                                                                                                                                                            
$gaUpsert = array(         /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
  'user_id'       =>array("a"=>_L       +_2U+_2D       +_U+_D    ,"t"=>'hidden'                                                     ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_INT     ,"flags"=>FILTER_REQUIRE_SCALAR                                                                           
),'user_enabled'  =>array("a"=>_N                                ,"t"=>'flip'        ,"l"=>"usr.l.enabled"  ,"h"=>"usr.h.enabled"   ,"p"=>""                      ,"d" => 1                         ,"o"=>aofNoYes()     ,"filter"=>FILTER_CALLBACK        ,"options"=>afFilterChkKey(aofNoYes())     
),'user_name'     =>array("a"=>_L                   +_I+_U       ,"t"=>'text'        ,"l"=>"usr.l.name"     ,"h"=>"usr.h.name"      ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"options"=>afFilterChkRgx(C_REGEXP_USER_NAME)                                                 
),'user_login'    =>array("a"=>_L                                ,"t"=>'text'        ,"l"=>"usr.l.login"    ,"h"=>"usr.h.login"     ,"p"=>""                                                                             ,"filter"=>FILTER_VALIDATE_REGEXP ,"options"=>afFilterChkRgx(C_REGEXP_USER_LOGIN)                                                                         
),'user_pwd'      =>array("a"=>_N                                ,"t"=>'password'    ,"l"=>"usr.l.pwd"      ,"h"=>"usr.h.pwd"       ,"p"=>C_PLACEHOLDER_PWD_INSERT                                                       ,"filter"=>FILTER_VALIDATE_REGEXP ,"options"=>afFilterChkRgx(C_REGEXP_USER_PWD_OR_NULL)                               
),'user_email'    =>array("a"=>_L                   +_I+_U       ,"t"=>'email'       ,"l"=>"usr.l.email"    ,"h"=>"usr.h.email"     ,"p"=>C_PLACEHOLDER_MAIL                                                             ,"filter"=>FILTER_VALIDATE_EMAIL   ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                                 
),'user_level'    =>array("a"=>_L                   +_I+_U       ,"t"=>'checkbox'    ,"l"=>"usr.l.level"    ,"h"=>"usr.h.level"     ,"p"=>"..."                   ,"d" => C_LOGIN_USER_LEVEL_GUEST ,"o"=>aofUserLevel()  ,"filter"=>FILTER_CALLBACK        ,"options"=>afFilterChkKey(aofUserLevel())
),'sendEmail'     =>array("a"=>_N                                ,"t"=>'flip'        ,"l"=>"usr.l.sendmail" ,"h"=>"usr.h.sendmail"  ,"p"=>""                      ,"d" => 0                        ,"o"=>aofNoYes()      ,"filter"=>FILTER_CALLBACK        ,"options"=>afFilterChkKey(aofNoYes())     
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
      $avar = $gaUpsert;
       $dbg = array(); 
            
    try {
        $newaction = sfNewActionOrError($action,$avar);
        switch ( $action ) {
        
        /****/ case '_list':         // list all not deleted
/*
            $avar = array();
            $rs = DB::run("select user_id,user_name,user_nick,user_enabled,user_level from iq_utenti order by user_name");
            if($rs) while ($r = $rs->fetch(PDO::FETCH_ASSOC)) {
                $r['user_level'] = sfUserLevel($r['user_level']);  //readable user level for list
                $avar[] = $r;
            }
            */
            
            $avar = DB::run("select user_id,user_name,user_login,user_enabled,user_level from t_users order by user_name")->fetchAll();
            foreach($avar as $n => &$f) $f['user_level'] = sfUserLevel($f['user_level']);
  
        
        break; case '_2insert':    // prepare array to form insert = new, change state action		   

            foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "sendEmail"  : $avar[$n]['v'] = "1";
                break; case "user_email" : $avar[$n]['v'] = uniqId("fake_")."@ra.org";
                break; case "user_pwd"   : $avar[$n]['p'] = C_PLACEOLDER_PWD_INSERT;  // no value for pwd
                break; case "user_level" : $avar[$n]['v'] = aofSplitUserLevel( C_LOGIN_USER_LEVEL_GUEST );  // guest initial state 
                break; 
                }
			}	
           
        break; case '_2update': 
               case '_2delete': 
               case '_2copy':   
                                   
            $r = DB::run("select * from t_users where user_id=?",array( $avar['user_id']['v'] ))->fetch(); 
			if(!$r) throw new Exception( "utente non riconosciuto" );
            
            foreach($avar as $n => $f)  {       //load values from existing record to update
                switch($n) {
                /****/ case "sendEmail"  : $avar[$n]['v'] = "0";
                break; case "user_pwd"   : $avar[$n]['p'] = C_PLACEOLDER_PWD_UPDATE;  // no value for pwd
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
                break; default           : $kv[$n] = $f['v']; 
                }
			}
            $row = afSQLUpsert("t_users", $kv, array("user_id") );      // _insert: user_id is null -> no upsert only insert
            
            if(!$row) throw new Exception( _e("impossibile salvare") );
            
            $message = _e("salvato");
            
            if($row && $bmail /* && $row['user_enabled'] == 1 */) {
                $row['user_pwd']   = sfDecrypt($row['user_pwd']); 
                $row['user_level'] = sfUserLevel($row['user_level']);  // da 0x02 a player... = qualcosa di leggibile

                $b = bfMailLoginInfo($row);

				if($b) $message .= '<br>'._e("e.login.mail_sent");          // La password &egrave; stata inviata, prego controlli la sua e-mail
				else   $message .= '<br>'._e("e.login.mail_fail");          // Fallito l'invio mail con i dati di accesso, riprovi.";
            }
            
        break; case '_delete':

            $kv = array();
            foreach($avar as $k => $v) $kv[$k] = $v['v'];
            $avar = $kv;
            
            $rs = DB::run("delete from t_users where user_id=?", array($avar['user_id']));
            
            $dbg['del']=print_r($rs,true);
            $message = _e("eliminato");

        break; default :
            throw new Exception( _e("unrecognized action") );
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
    , 'action' => $newaction 
    ,      'l' => $avar  // list: all records, 2update 2insert...: 1 readble record
    ,'request' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;

}

?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("Users"); ?>
<script type='text/javascript'>

function vfActionSwitch(a,r) {
    var s="",c,h,n,g;	
	var idPagForm = "#page-user-form"
	   ,idPagList = "#page-user-list";
    
	var vfListModify = function (u) { // to change list without reload. U=upsert=true
        var id = 'user_id', orderby = 'user_name', list='_list_users_data';
        if(!r || !r.status) return;
        var e=r.l.user_enabled ,b={}; b[id]=r.l[id].toString();
        $.mobile[list] = _.without($.mobile[list], _.findWhere($.mobile[list], b));  // delete record
        if (u) {                                                                     // add new modifyed
            $.mobile[list].push(r.l);                                                //was: _.sortBy($.mobile[list],orderby);
            $.mobile[list]=_.sortBy($.mobile[list],function (i) { return i[orderby].toLowerCase(); }); 
        } 
        $(idPagList +' .b-pending:jqmData(enabled="'+e+'")').trigger("click");      // reload list
    }
	if(r && !r.status) vfPopUp('error',r.message);
    switch(a) {
	/****/ case "_list":
        
        $.mobile._list_users_data = r.l;                                                  // data

        $(idPagList +' .b-pending:jqmData(enabled="1")').trigger("click"); //forzo quelli ad 1
    
    break; case "_2insert": if(empty(c)){c = "insert"; h = "Nuovo";     $(idPagForm +' .b-delete').addClass('ui-screen-hidden');}  
           case "_2update": if(empty(c)){c = "update"; h = "Modifica";  $(idPagForm +' .b-delete').removeClass('ui-screen-hidden');}  

		$(idPagForm +' .ui-content').empty().html(/**/sfBuidForm(r,c)/**/).trigger('create');
        $(idPagForm +' form').on('submit', function(){
            //var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
            vfActionLoad($(this).serializeArray(),vfActionSwitch);
            return false;
        });
		
	
    break; case "_2delete": vfPopUpDeleteConfirm(idPagList,idPagForm);
   	break; case "_insert" : vfActionErrorOk(r,idPagForm,"Nuovo"    ,idPagList); vfListModify(true);
	break; case "_update" : vfActionErrorOk(r,idPagForm,"Modifica" ,idPagForm); vfListModify(true);
    break; case "_delete" : vfActionErrorOk(r,idPagForm,"Cancella" ,idPagList); vfListModify(false);

        
    // cancella v annulla/ignora
        

	} // end switch
}

$.mobile.document.on("pageshow"   ,"#page-user-form" ,function() { vfActionLoad($.mobile.pageData  ,vfActionSwitch); });
$.mobile.document.on("pagecreate" ,"#page-user-list" ,function() { vfActionLoad( {action:'_list'}  ,vfActionSwitch); });

$.mobile.document.on("click", "#page-user-form .b-delete"  ,function(){ vfActionSwitch( '_2delete' ); });
$.mobile.document.on("click", "#page-user-list .b-pending" ,function(){ 
    $('#page-user-list .b-pending').removeClass("ui-state-persist ui-btn-active"); $( this ).addClass( "ui-state-persist ui-btn-active" );
    var j,i,h=[]
        ,e=$(this).jqmData('enabled')
        ,d=$.mobile._list_users_data;
    for(i in d) {
        j=d[i];if(j.user_enabled == e)  {
            h.push('<li><a href="#page-user-form" data-action="_2update" data-user_id="', j.user_id,'">');
            h.push( '<h2>',j.user_name,'</h2><p><b>',j.user_login||'','</b></p><p>',j.user_level||'','</p></a></li>');
        }
    } 
    $('#page-user-list-ul').empty().html( h.join('') ).listview("refresh");
	$.mobile.silentScroll(0);
});

</script>
</head>
<body>
<div id="page-user-list" data-role="page" data-theme="a">
    <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
        <a href="home" data-ajax="false" data-icon="home">Home</a>
        <h1>Utenti</h1>
        <a href="#page-user-form?action=_2insert" data-icon="plus" data-iconpos="right">Nuovo</a>
    </div>
    <div data-role="content" >  
        <ul data-role="listview" data-filter="true" id="page-user-list-ul"></ul>
    </div>
    <div data-role="footer" data-position="fixed" data-tap-toggle="false">
        <div data-role="navbar">
            <ul>
                <li><a href="#" data-icon="check"     data-enabled="1" class="b-pending">Abilitati</a></li>
                <li><a href="#" data-icon="forbidden" data-enabled="0" class="b-pending">Non Abilitati</a></li>
            </ul>
        </div>
    </div>
</div> <!-- //page-lg-list -->

<div id="page-user-form" data-role="page" data-theme="a">
    <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
        <a href="#page-user-list?action=_list"  data-icon="back">List</a>
        <h1>Modifica</h1>
        <a href="#" class="b-delete" data-icon="delete" data-iconpos="right" style="backgroung-color:red">Cancella</a>
    </div>
    <div data-role="content" > </div>           
</div> <!-- //page-lg-form -->
</body>
</html>
<?php
            
                                                                                                                                                                                                                                                                                                                
$ga_config = array( // --                              input.type     label                        help                          placehoder   default                        options (_o pass by gettext)
  "C_CONFIG_SITE_LOGO"      => array("a"=>_N      ,"t"=>"photo"	    ,"l"=>_e("l.cfg.site-logo")         ,"h"=>_e("h.cfg.site-logo")                                 ,"p"=>""    ,"d"=>C_DEFAULT_SITE_LOGO                                  ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_PHOTO)               
),"C_CONFIG_SITE_NAME"      => array("a"=>  _U    ,"t"=>"text"      ,"l"=>_e("l.cfg.site-name")         ,"h"=>_e("h.cfg.site-name")                                 ,"p"=>""    ,"d"=>C_ABOUT_GENERAL_TITLE                                ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                 
),"C_CONFIG_SITE_SHORT"     => array("a"=>  _U    ,"t"=>"text"      ,"l"=>_e("l.cfg.site-short-name")   ,"h"=>_e("h.cfg.site-short-name")                           ,"p"=>""    ,"d"=>basename(__DIR__)                                    ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                 
),"C_CONFIG_SECRET"         => array("a"=>  _U    ,"t"=>"text"      ,"l"=>_e("l.cfg.secret-key")        ,"h"=>_e("h.cfg.secret-key")                                ,"p"=>""    ,"d"=>sfGuidV4()                                           ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                                                                                                                                                                                                                                                                                                                                                 /* is seated by application              */          
),"C_CONFIG_PUBLIC"         => array("a"=>  _U    ,"t"=>"text"      ,"l"=>_e("l.cfg.public-key")        ,"h"=>_e("h.cfg.public-key")                                ,"p"=>""    ,"d"=>sfGuidV4()                                           ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                                                                                                                                                                                                                                                                                                                                                 /* is seated by application              */          
),"C_CONFIG_LOCALE"         => array("a"=>  _U    ,"t"=>"select"    ,"l"=>_e("l.cfg.locale")            ,"h"=>_e("h.cfg.locale")         ,"e"=>"e.cfg.locale"        ,"p"=>""   ,"d"=>C_DEFAULT_LOCALE          ,"o"=>afLanguages()        ,"filter"=>FILTER_CALLBACK                                         ,"options"=>function($v) { return bfInArray($v,afLanguages());}                                          
),"C_CONFIG_TIMEZONE"       => array("a"=>  _U    ,"t"=>"select"    ,"l"=>_e("l.cfg.time-zone")         ,"h"=>_e("h.cfg.timezone")                                  ,"p"=>""    ,"d"=>C_DEFAULT_TIME_ZONE       ,"o"=>aofTimeZones()       ,"filter"=>FILTER_CALLBACK                                         ,"options"=>function($v) { return bfInArray($v,aofTimeZones());}                        
),"user_name"               => array("a"=>  _U    ,"t"=>"text" 	    ,"l"=>_e("l.cfg.name")              ,"h"=>_e("h.cfg.name")                                      ,"p"=>""    ,"d"=>""                                                   ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_NAME)	                                                             	
),"C_CONFIG_ADMIN_LOGIN"    => array("a"=>  _U    ,"t"=>"hidden"                                                                                                                ,"d"=>C_DEFAULT_ADMIN_LOGIN                                ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_LOGIN)                  
),"C_CONFIG_ADMIN_EMAIL"    => array("a"=>  _U    ,"t"=>"text" 	    ,"l"=>_e("l.cfg.email")             ,"h"=>_e("h.cfg.email")                                     ,"p"=>""    ,"d"=>""                                                   ,"filter"=>FILTER_VALIDATE_EMAIL   ,"flags"=>FILTER_REQUIRE_SCALAR 	                                                             	
),"C_CONFIG_ADMIN_PWD"      => array("a"=>  _U    ,"t"=>"password"  ,"l"=>_e("l.cfg.pwd")	            ,"h"=>_e("h.cfg.pwd")                                       ,"p"=>""    ,"d"=>""                                                   ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_PWD)           		
),"C_CONFIG_MAIL_ON_ERROR"  => array("a"=>_N      ,"t"=>"flip"      ,"l"=>_e("l.cfg.mail-on-err")       ,"h"=>_e("h.cfg.email-on-err")                              ,"p"=>""    ,"d"=>C_DEFAULT_MAIL_ON_ERROR   ,"o"=>aofNoYes()           ,"filter"=>FILTER_VALIDATE_BOOLEAN ,"flags"=>FILTER_REQUIRE_SCALAR 	                                                             	
),"C_CONFIG_ANALYTICS"      => array("a"=>_N      ,"t"=>"text"      ,"l"=>_e("l.cfg.analytics-key")     ,"h"=>_e("h.cfg.analytics-key")                             ,"p"=>""    ,"d"=>""                                                   ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                  	
),"C_CONFIG_YOUTUBE_KEY"    => array("a"=>_N      ,"t"=>"text" 	    ,"l"=>_e("l.cfg.youtube-key")       ,"h"=>_e("h.cfg.youtube-key")                               ,"p"=>""    ,"d"=>""                                                   ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                   	
),"C_CONFIG_BUILD_PATH"     => array("a"=>_N      ,"t"=>"text"      ,"l"=>_e("l.cfg.clips-path")	    ,"h"=>_e("h.cfg.clips-path")     ,"e"=>"e.cfg.dir-no-valid" ,"p"=>""    ,"d"=>C_DEFAULT_CLIPS_PATH                                 ,"filter"=>FILTER_CALLBACK                                         ,"options"=>function($v) { return (is_dir($v)? $v : false);}   					
),"C_CONFIG_BUILD_PATTERN"  => array("a"=>_N      ,"t"=>"text" 	    ,"l"=>_e("l.cfg.clips-pattern")     ,"h"=>_e("h.cfg.clips-pattern")                             ,"p"=>""    ,"d"=>C_DEFAULT_CLIPS_PATTERN                              ,"filter"=>FILTER_VALIDATE_REGEXP  ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_ASCII)                   	
),"check_dir"               => array("a"=>_N      ,"t"=>"checklist" ,"l"=>_e("l.cfg.host-check")        ,"h"=>_e("h.cfg.host-check")                                ,"p"=>""    ,"d"=>""                        ,"o"=>aoDefCheck()         ,"filter"=>FILTER_CALLBACK                                         ,"options"=>function($v) { return bfInArray($v,aoDefCheck());}  	                                                   
),"C_CONFIG_DB_SQLITE3"     => array("a"=>  _U    ,"t"=>"hidden"                                                                         ,"e"=>"e.cfg.sqlite"                    ,"d"=>C_DEFAULT_DB_SQLITE3                                 ,"filter"=>FILTER_CALLBACK                                         ,"options"=>function($v) { return (is_file($v)? $v : false);}          	
));   
@touch(C_FILE_CONFIG_DEF); //if ds not exist wl be created

function sfCheckWrite($s) {
    
    if(empty(realpath($s))) $s = dirname($s);                       // file not exist: get his dir
    $w = is_dir($s) ? is_writable($s) : is_writable(dirname($s));   //directory writable??
    return "./$s ... ". ($w && is_writable($s) ? _e("i.cfg.writable") : _e("i.cfg.not-writable")) ;
}     
function aoDefCheck() {
    return [
         C_FILE_CONFIG_DEF              => sfCheckWrite(C_FILE_CONFIG_DEF) 
        ,dirname(C_DEFAULT_DB_SQLITE3)  => sfCheckWrite(dirname(C_DEFAULT_DB_SQLITE3)) 
        ,C_DEFAULT_DB_SQLITE3           => sfCheckWrite(C_DEFAULT_DB_SQLITE3) 
        //,C_DIR_IMG_OTHER                => sfCheckWrite(C_DIR_IMG_OTHER)  
        //,C_DIR_IMG_USERS                => sfCheckWrite(C_DIR_IMG_USERS)   
    ];
}
function afCheck_dir() {
    $a = array();
    foreach( aoDefCheck() as $k => $v ) {
            if(is_dir($k) && is_writable($k))               $a[] = $k;
        elseif(is_writable(dirname($k)) && is_writable($k)) $a[] = $k;
    }
    return $a;
}

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = $ga_config;
	   $dbg = array();

    try {
        $newaction = sfNewActionOrError($action,$avar);
        switch ( $action ) {
        /***/ case '_2update':// prepare array to form update = edit, set new action

            foreach($avar as $n => $f) { //load values from existing record to update
                switch($n) {
                /****/ case "C_CONFIG_ADMIN_PWD":  $avar[$n]['v'] = "";              // password admin always to set
                break; case 'check_dir':           $avar[$n]['v'] = afCheck_dir();   // checkdir writable
                break; default:
                        if (defined($n))           $avar[$n]['v'] = constant($n);    // get value already set
                        else                       $avar[$n]['v'] = $avar[$n]['d'];  // get default
                break;
                }
            }	
           
        break; case '_update':  

            $s = "<?php";       //string to write in config_def.php
			foreach($avar as $n => $f) {  // not &$f -> by ref 'password_hash' sully update
                $b = true;
				switch($n) {
                /****/ case 'check_dir':           $b = false; //not to define  
                break; case 'user_name':           $b = false;
                break; case "C_CONFIG_ADMIN_PWD":  $f['v'] = password_hash($f['v'], PASSWORD_DEFAULT);   //hash password https://www.php.net/manual/en/function.password-hash.php              
                //break; case "C_CONFIG_DB_SQLITE3": if(!is_file($f['v']))              throw new Exception($f['v'] .": {{err.no-sqlite}}"); 
				//break; case "C_CONFIG_BUILD_PATH": if(!is_dir(__DIR__ ."/". $f['v'])) throw new Exception("err.dir-no-valid");  
				break;
                }
                if($b) {
					$s .= "\ndefine('$n' ,'$f[v]');";       // for the file 
					if(!defined($n)) define($n ,$f['v']);   // for the next statments
				}
            }
            $s .= "\n?>";
			
            if(!is_writable(C_FILE_CONFIG_DEF))            throw new Exception(sprintf(_e("file %s (%s) no write permission"),C_FILE_CONFIG_DEF ,decoct(@fileperms(C_FILE_CONFIG_DEF) & 0777) ));
            if(!@file_put_contents (C_FILE_CONFIG_DEF,$s)) throw new Exception(sprintf(_e("file %s, write error: %s")        ,C_FILE_CONFIG_DEF ,str_replace("file_put_contents","",error_get_last()['message']) ));		
            //if (!function_exists('sqlite_libversion'))        throw new Exception(_("err.no-sqlite-installed"));  // no sqlite installed
            //if(!@sqlite_open($avar['C_CONFIG_DB_SQLITE3']['v'], 0666, $sqliteerror))  throw new Exception("err.no-sqlite");
			//if(!is_file($avar['C_CONFIG_DB_SQLITE3']['v']))  throw new Exception("err.no-sqlite"); 
            
            $kv = array(  // save info for admin
				 'user_name'   => $avar['user_name']['v']
                ,'user_login'  => $avar['C_CONFIG_ADMIN_LOGIN' ]['v']
                ,'user_email'  => $avar['C_CONFIG_ADMIN_EMAIL']['v']
                ,'user_pwd'    => sfEncrypt($avar['C_CONFIG_ADMIN_PWD']['v'], $avar['C_CONFIG_SECRET']['v']) 
                ,'user_level'  => C_LOGIN_USER_LEVEL_ADMIN
				,'user_lang'   => $avar['C_CONFIG_LOCALE']['v'] 
                ,'user_enabled'=> 1
				,'update_by'   => 0
				,'update_on'   => date('YmdHis')
            );
			
			$row = afSQLiteUpsert("t_users",$kv,['user_login']); // admin is admin, only one, no check if is unique: upsert like a tank
			
            vfSetSession($row);    //@@@ and if C_CONFIG_SECRET change?... re-login....
            $message = _e("i.cfg.saved");  // Configuration Saved
            
        break; default :
            throw new Exception( _e("e.unrecognized_action") );
        } // switch ( $_REQUEST['action']) 
        
        $status = true;
        if (empty($message)) $message = _e("i.cfg.done");  

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
<?= sfHead(_e("p.configuration"),array("//cdnjs.cloudflare.com/ajax/libs/cropit/0.5.1/jquery.cropit.min.js")) ?>



</head>
    <body>
        <div id="p-config" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="home" data-icon="home"><?= _e("home") ?></a>
				<h1>Config</h1>
				<a href="#" class="b-action" data-icon="check" data-iconpos="right"><?= _e("save") ?></a>
            </div> <!-- /header -->	
            <div role="main" class="ui-content"></div>	
<script>

function vf_p_config_ActionSwitch(a,r) {
    var idPagForm = '#p-config';	 
	
    switch(a) {  
	/****/ case "_2update":  
	    $(idPagForm +' .b-action').jqmData('action','_update');
		$(idPagForm +' .ui-content').empty().html(/**/sfBuidForm(r)/**/).trigger('create');
		
		vfPhotoFieldEnhance(r,160,80);

	break; case "b-action": 
		if(!_.isElement(r)) return;
		
		vfPhotoFieldExport();       // export photo field
		vfActionLoad( $(idPagForm +' form').serializeArray(),vfActionSwitch);
	
	break; case "_update": vfActionErrorOk(r,idPagForm,"Modifica" ,idPagForm); 


	} // end switch
}
$.mobile.document.on("pagecreate" ,"#p-config"     ,function(){ vfActionLoad({action:"_2update"},vf_p_config_ActionSwitch); });
$.mobile.document.on("click","#p-config .b-action" ,function(){ vf_p_config_ActionSwitch( 'b-action', this ); });
</script>  
        </div> <!-- /page-sets -->
    </body>
</html>
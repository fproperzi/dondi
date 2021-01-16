<?php
ini_set('display_errors', 1); 

require_once ("rr_config_gen.php");  // generic defines
require_once ("rr_config_def.php");  // edit define
require_once ("rr_config_func.php"); // tools functions
require_once ("rr_crud.php");    // crud functions

//rr_config_def setted but youare not admin
if(defined('C_CONFIG_SECRET') && (!isUserLevelAdmin() || $_SESSION[C_LOGIN_USER_SECRET] != C_CONFIG_SECRET))
    vfGo2Header(C_FILE_ERROR,C_ERROR_NOT_ALLOWED);


/* array definitions crud, chech in crud.php for explanations
**/                            
$ga_config = array( /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
	  "check_dir"              => array("a"=>_N      ,"l"=>"check file, directory permission" 
																				  ,"t"=>"checklist","p"=>""                                            ,"d"=>""         ,"o"=>aoDefCheck(),"filter"=>FILTER_CALLBACK               ,"flags"=>FILTER_REQUIRE_ARRAY  ,"options"=>function($v){return bfInArray($v,aodefCheck());} ,"h"=>"Pass check about host"																                                        
    ),"C_CONFIG_SECRET"        => array("a"=>  _U                                 ,"t"=>"hidden"	                                                   ,"d"=>sfGuidV4()                   ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                                       
	),"C_CONFIG_DB_HOST"       => array("a"=>  _U    ,"l"=>"DB Host"              ,"t"=>"text"     ,"p"=>"Valid dataBase Host IP like: 219.122.32.122" ,"d"=>"127.0.0.1"                  ,"filter"=>FILTER_VALIDATE_IP            ,"flags"=>FILTER_REQUIRE_SCALAR                                                              ,"h"=>"valid IP to database host"                                                                         
	),"C_CONFIG_DB_USER"       => array("a"=>  _U    ,"l"=>"DB User" 	          ,"t"=>"text" 	   ,"p"=>""                                            ,"d"=>"u_mclips"                   ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>""                                                                         
	),"C_CONFIG_DB_PASS"       => array("a"=>  _U    ,"l"=>"DB Password"	      ,"t"=>"text" 	   ,"p"=>""                                            ,"d"=>"nnkk219"                    ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>""																                                        
	),"C_CONFIG_DB_NAME"       => array("a"=>  _U    ,"l"=>"DB Name"	          ,"t"=>"text" 	   ,"p"=>"DataBase Name"                               ,"d"=>"mclips"                     ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>"valid database name"																                                        
    ),"C_CONFIG_DB_CREATE"     => array("a"=>_N      ,"l"=>"DB Wipe and Create"	  ,"t"=>"flip"     ,"p"=>"AAA Play attetion, [Yes] wipe current db !!" ,"d"=>""         ,"o"=>aofNoYes()  ,"filter"=>FILTER_VALIDATE_INT	       ,"flags"=>FILTER_REQUIRE_SCALAR 	                                                            ,"h"=>""										                                        
	),"C_CONFIG_ANALYTICS"     => array("a"=>_N      ,"l"=>"Analytics Key"	      ,"t"=>"text"     ,"p"=>""                                            ,"d"=>""                           ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>"key fom Google Analytics"																                                        
    ),"C_CONFIG_YOUTUBE_KEY"   => array("a"=>_N      ,"l"=>"Youtube Key"	      ,"t"=>"text" 	   ,"p"=>""                                            ,"d"=>""                           ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>"key from Youtube develop"									                                        
    ),"C_CONFIG_BUILD_PATH"    => array("a"=>  _U    ,"l"=>"Clips Path"		      ,"t"=>"text"     ,"p"=>""                                            ,"d"=>C_DEFAULT_CLIPS_PATH         ,"filter"=>FILTER_CALLBACK               ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>function($v) { return (is_dir($v)? $v : false);} ,"h"=>"path to valid clips directory on your server"										                                        
    ),"C_CONFIG_BUILD_PATTERN" => array("a"=>_N      ,"l"=>"Clips Pattern"	      ,"t"=>"text" 	   ,"p"=>""                                            ,"d"=>C_DEFAULT_CLIPS_PATTERN      ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_REGEXP_ASCII)                  ,"h"=>"extension clips in regexp"										                                        
    ),"C_CONFIG_ADMIN_NAME"    => array("a"=>  _U                                 ,"t"=>"hidden" 	                                                   ,"d"=>C_DEFAULT_ADMIN_NAME         ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_LOGIN_USER_NAME_REGEXP)        ,"h"=>C_LOGIN_USER_NAME_HELP	          									                                        
    ),"C_CONFIG_ADMIN_EMAIL"   => array("a"=>  _U    ,"l"=>"admin email"	      ,"t"=>"text" 	   ,"p"=>"valid email"                                 ,"d"=>""                           ,"filter"=>FILTER_VALIDATE_EMAIL         ,"flags"=>FILTER_REQUIRE_SCALAR 	                                                            ,"h"=>"your administrator email"										                                        
    ),"C_CONFIG_ADMIN_PWQ"     => array("a"=>  _U    ,"l"=>"admin password"	      ,"t"=>"password" ,"p"=>"valid password"                              ,"d"=>""                           ,"filter"=>FILTER_VALIDATE_REGEXP        ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>array("regexp"=>C_LOGIN_USER_PWD_REGEXP)         ,"h"=>C_LOGIN_USER_PWD_HELP									                                        
    ),"C_CONFIG_MAIL_ON_ERROR" => array("a"=>_N      ,"l"=>"Send mail on error"	  ,"t"=>"flip"     ,"p"=>"Every time an error occur"                   ,"d"=>""         ,"o"=>aofNoYes()  ,"filter"=>FILTER_VALIDATE_INT	       ,"flags"=>FILTER_REQUIRE_SCALAR 	                                                            ,"h"=>""										                                        
	)                                                                                                                                                                                                        
);   


function aoDefCheck() {
    $a = array();
    $a[C_FILE_CONFIG_DEF] = "config file (". C_FILE_CONFIG_DEF .") is writable";
    $a[C_DIR_IMG_OTHER  ] = C_DIR_IMG_OTHER   ." writable";
    $a[C_DIR_IMG_USERS  ] = C_DIR_IMG_USERS   ." writable";
    
    return $a;
}
function afCheck_dir() {
    $a = array();
    foreach( aoDefCheck() as $k => $v )
        if(is_writable($k)) $a[] = $k;
    return $a;
}

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']);
      $avar = $ga_config;
	   $dbg = array();

    try {
        switch ( $action ) {
        /***/ case 'set_2update': $action = "set_update"; // prepare array to form update = edit, set new action
            
            if(bfCheck_POST ($avar ,C_ACTION_2UPDATE)) throw new Exception( C_FORM_ERROR ); // check errors (need+good for action)
            foreach($avar as $n => $f) { //load values from existing record to update
                switch($n) {
                /****/ case "C_CONFIG_ADMIN_PWQ":  $avar[$n]['v'] = "";              // password admin always to set
                break; case 'check_dir':           $avar[$n]['v'] = afCheck_dir();   // checkdir writable
                break; default:
                        if (defined($n))           $avar[$n]['v'] = constant($n);    // get value already set
                        else                       $avar[$n]['v'] = $avar[$n]['d'];  // get default
                break;
                }
            }	
           
        break; case 'set_update': 
            
            if(bfCheck_POST ($avar,C_ACTION_UPDATE)) throw new Exception(C_FORM_ERROR); // check errors and if file is exist/writable
            
            $m = mysqli_connect( $avar['C_CONFIG_DB_HOST']['v']  // check if parameters are ok
                                ,$avar['C_CONFIG_DB_USER']['v']
                                ,$avar['C_CONFIG_DB_PASS']['v']
                                ,$avar['C_CONFIG_DB_NAME']['v']
                               );
            if($m->connect_errno)                    throw new Exception("Connection DB failed: ".$m->connect_error);
            
            $s = "<?php";
			foreach($avar as $n => &$f) {
                switch($n) {
                /****/ case 'C_CONFIG_DB_CREATE': // create db
                break; case 'check_dir':    
                break; case "C_CONFIG_ADMIN_PWQ": $s .= "\ndefine('$n' ,'". sfPwdHash($f['v']) ."');";   //hash password                
                break; default:                   $s .= "\ndefine('$n' ,'$f[v]');";
                }
                
            }
            $s .= "\n?>";

            if(!is_writable(C_FILE_CONFIG_DEF))            throw new Exception("cant write file ". C_FILE_CONFIG_DEF ." (". decoct(@fileperms(C_FILE_CONFIG_DEF) & 0777) .") change permission (666)!");
            if(!@file_put_contents (C_FILE_CONFIG_DEF,$s)) throw new Exception("write error ". str_replace("file_put_contents","",error_get_last()['message']) );		
            
            $kv = array(  // save info for admin
                 'user_name'   => $avar['C_CONFIG_ADMIN_NAME' ]['v']
                ,'user_email'  => $avar['C_CONFIG_ADMIN_EMAIL']['v']
                ,'user_pwd'    => $avar['C_CONFIG_ADMIN_PWQ'  ]['v'] 
                ,'user_level'  => C_LOGIN_USER_LEVEL_ADMIN
                ,'user_ctime'  => strtotime(C_LOGIN_EXPIRE) // expitation time
                ,'user_key'    => sfGuidV4()
                ,'user_banned' => 0
            );
            $s = sfSQLUpsert("t_users",$kv);  // get sql for upsert
            if(!$m->query($s))                       throw new Exception("DB upsert error: ".$m->error);    
                    
            session_regenerate_id(); //you are admin!
            
            $_SESSION[C_LOGIN_USER_ID    ] = $m->insert_id;  // last upsert
            $_SESSION[C_LOGIN_USER_KEY   ] = $kv['user_key'];
            $_SESSION[C_LOGIN_USER_NAME  ] = $kv['user_name'];
            $_SESSION[C_LOGIN_USER_LEVEL ] = $kv['user_level'];
            $_SESSION[C_LOGIN_USER_LANG  ] = false;
            $_SESSION[C_LOGIN_USER_AGENT ] = md5($_SERVER['HTTP_USER_AGENT']);
            $_SESSION[C_LOGIN_USER_SECRET] = $avar['C_CONFIG_SECRET']['v'];
            
            
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
<script>

function vfActionSwitch(a,r) {
    var s="",c,h,n;	
	
	switch(a) {  
	/****/ case "set_2update":  

		if(r.status) s +=  sfBuidForm(r,"update");
		else       	 s  = "<h1>Error</h1><p>"+ r.message + "</p>";
			
		$('#page-config-define .ui-content').empty().html(s).trigger('create');
        $('#page-config-define form').on('submit', function(){
            var d = _.reduce($(this).serializeArray(),function(a,f){a[f.name]=f.value;return a;},{});
            vfActionLoad(d,vfActionSwitch);
            return false;
        });
		
	
	break; case "set_update":   
		
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
$(document).on("pagecreate", "#page-config-define", function() {
	vfActionLoad({action:"set_2update"},vfActionSwitch);
});
</script>  


</head>
    <body>
    
        <div id="page-config-define" data-role="page" data-theme="b">
            <div data-role="header" data-position="fixed"  data-tap-toggle="false" >
                <a href="#home" data-icon="home">Home</a>
				<h1>Config</h1>
            </div> <!-- /header -->	
            <div role="main" class="ui-content">
                
            </div> <!-- /content -->	
        </div> <!-- /page-sets -->
        

		
    </body>
</html>
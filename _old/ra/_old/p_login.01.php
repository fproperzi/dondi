<?php   

$gaLogin = array(          /** _L+_O+_2I+_2U+_2D+_2C+_I+_U+_D+_C ** required field for action **/
  'user_email'    =>array("a"=>_N                   +_I          ,"t"=>'email'       ,"l"=>"usr.l.email"        ,"h"=>"usr.h.email"     ,"p"=>_e("your@email.please")                       ,"filter"=>FILTER_VALIDATE_EMAIL      ,"flags"=>FILTER_REQUIRE_SCALAR                                                                                                 
),'user_pwd'      =>array("a"=>_N                   +_I          ,"t"=>'password'    ,"l"=>"usr.l.pwd"          ,"h"=>"usr.h.pwd"       ,"p"=>""                                            ,"filter"=>FILTER_VALIDATE_REGEXP     ,"flags"=>FILTER_REQUIRE_SCALAR ,"options"=>afFilterChkRgx(C_REGEXP_USER_PWD_OR_NULL)                                 
),'remind'        =>array("a"=>_N                                ,"t"=>'flip'        ,"l"=>"usr.l.remind"       ,"h"=>"usr.h.remind"    ,"p"=>""                        ,"o"=>aofNoYes()    ,"filter"=>FILTER_VALIDATE_BOOLEAN    ,"flags"=>FILTER_REQUIRE_SCALAR     
));   

if (empty($_REQUEST['action'])) vfDelSession();  // se sei qui tutto ti cancello!
else {
    
    
  
    
   
    $action = strtolower($_REQUEST['action']); 
      $avar = array();
       $dbg = array();  

    try { 
	//        DB::run("update iq_utenti set user_pwd=aes_encrypt(?,?) where user_email=?", array($v['pwd'],C_LOGIN_SECRET,$v['email']) );
    // $avar = DB::run("select aes_decrypt(user_pwd,?) from iq_utenti",array(C_LOGIN_SECRET))->fetchAll();

        $prm = [
            'user_email' => @$_REQUEST['usr'],
            'user_login' => @$_REQUEST['usr'],
            'user_pwd'   => sfEncrypt( @$_REQUEST['pwd'] ),
            'remind'     => ('1' === @$_GET['remind'])
        ];
        switch ( $action ) {
        /****/ case 'login':

            $row = DB::run("SELECT * FROM t_users WHERE (user_email=:user_email or user_login=:user_login) and user_pwd=:user_pwd", $prm)->fetch();
			
			    if($row && $row['user_enabled'] == 1)	vfSetSession($row,$prm['remind']);
			elseif($row && $row['user_enabled'] != 1) 	throw new Exception( "e.usr.disabled");     // Utente disabilitato, contattare l'amministratore.
		    else 										throw new Exception( "e.usr.notfound");     // Utente non riconosciuto, utilizzare il corretto email e password.
        
        break; case 'forgot':
                
			$row = DB::run("SELECT * FROM t_users WHERE user_email=:user_email", $prm)->fetch();
				
            if($row && $row['user_enabled'] == 1) {
                $row['user_pwd'] = sfDecrypt($row['user_pwd']);
                $b = bfMailLoginInfo($row);

				if($b) $message = "La password &egrave; stata inviata, prego controlli la sua e-mail";
				else   $message = "Fallito l'invio mail con i dati di accesso, riprovi.";
			}
			elseif($row && $row['user_enabled'] != 0) 	throw new Exception( "Utente disabilitato, contattare l'amministratore.");
			else 										throw new Exception( "La mail inserita non &egrave; associata ad alcun utente abilitato.");
			
        break; default :
            throw new Exception( "Utente non riconosciuto, rifare il login\n{{./login:login:Login:}}" );
        } // switch ( $_REQUEST['action']) 
        
        $status  = true;
        if (empty($message)) $message = "Fatto!";  
        
    } catch (Exception $e) {

         $status = false;
        $message = $e->getMessage();
    } 
    
    echo  json_encode(array(
      'status' => $status
    ,'message' => $message
    , 'action' => $action
    ,      'l' => $avar
    ,      'p' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;
}//if(!empty($_REQUEST['action'])) {

?>
<!DOCTYPE html> 
<html><head>
<?= sfHead("Login") ?>
<script type="text/javascript">
$.mobile.document.on("submit","#form-login", function(e,ui){  

    vfActionLoad($(this).serializeArray(),function(a,r){
        if(!r.status) vfPopUp('error',r.message);
        else location.replace("./");
    }); 
    //alert("login submit:"+$(this).serialize());
    
    e.preventDefault();
    return false;
});
$.mobile.document.on("submit","#form-login-forgot", function(e,ui){  
    $( "#pop-forgot" ).popup( "close" );
    
    vfActionLoad($(this).serializeArray(),function(a,r){
        if(!r.status) vfPopUp('error',r.message);
        else          vfPopUp('Riconosciuto',r.message);
    }); 
    e.preventDefault();
    return false;
});
</script>
</head> 
<body> 
<div id="p-login" data-role="page" data-theme="a" >
	<div data-role="header" data-position="fixed">
		 <h1>Login</h1>
	</div>
        <div data-role="content" >   
    		<div id="logo_image" style="text-align:center;margin:5 auto">
            <img src="img/logo_image.png" style="max-width: 100%">
            </div>

		<form id="form-login" method="post">
			<div class="ui-field-contain">
			   <label>Email:</label>
			   <input name="usr" type="email" required>
			</div>
			<div class="ui-field-contain"> 
			   <label>Password:</label>
			   <input name="pwd" type="password" required>
			</div>
			<div class="ui-field-contain">	
                <label>Ricordami:</label>
                <select name="remind" data-role="slider">
                    <option value="0">No</option>
                    <option value="1">Si</option>
                </select>               
             </div>
			 <div class="ui-field-contain">
                <label></label>
				<input type="hidden" name="action" value="login">
				<input type="submit" value="Accedi" data-role="button" data-inline="true"/>
                <a href="#pop-forgot" data-rel="popup" data-position-to="window" data-role="button" data-inline="true">Password dimenticata?</a>
			</div>					
		</form>
		
		<div data-role="popup" id="pop-forgot" data-theme="a" class="ui-corner-all">
			<form id="form-login-forgot" method="post">
				<div style="padding:10px 20px;">
					<h3>Inserire l'e-mail, la password vi verr&agrave; inviata</h3>
					<label class="ui-hidden-accessible">EMail:</label>
					<input type="email" name="usr" value="" placeholder="email" required />
                    <input type="hidden" name="action" value="forgot" />
					<input type="submit" value="OK" data-role="button" />
				</div>
			</form>
		</div>
	</div><!-- /content -->
</div><!-- /page -->
</body></html>
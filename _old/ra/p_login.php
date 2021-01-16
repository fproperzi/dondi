<?php   
if (empty($_REQUEST['action'])) vfDelSession();  // se sei qui tutto ti cancello!
else {
   
    $action = strtolower($_REQUEST['action']); 
       $dbg = array();  

    try { 
        switch ( $action ) {
        /****/ case 'login':
            $prm = [
                ':user_email' => @$_REQUEST['usr'],
                ':user_login' => @$_REQUEST['usr'],
                ':user_pwd'   => @$_REQUEST['pwd']
            ];
            $bRemind = ('1' === @$_GET['remind']);

            $row = DB::run("SELECT * FROM t_users WHERE (user_email=:user_email or user_login=:user_login) and decrypt(user_pwd)=:user_pwd",$prm)->fetch();

            $dbg['row'] = $row;
            //DB::run("update t_users set user_pwd=crypt(?) where user_login=?",['admin56','admin']);
            $dbg['db'] = DB::run("SELECT user_login,user_pwd ,decrypt(user_pwd) as pwd FROM t_users")->fetchAll();
            $dbg['prm'] = $prm;
            
			if(!$row && $prm[':user_login'] == 'admin' && password_verify($prm[':user_pwd'], C_CONFIG_ADMIN_PWD)) {
			    // admin deleted in db!...
			    throw new Exception( _e("e.login.admin_deleted_in_db"));
			}
			elseif($row && $row['user_enabled'] == 1)	vfSetSession($row,$bRemind);
			elseif($row && $row['user_enabled'] != 1) 	throw new Exception( _e("e.login.disabled"));     // Utente disabilitato, contattare l'amministratore.
		    else 										throw new Exception( _e("e.login.usernotfound"));     // Utente non riconosciuto, utilizzare il corretto email e password.
        
        break; case 'forgot':
                
			$row = DB::run("SELECT * FROM t_users WHERE user_email=?", [@$_REQUEST['usr']])->fetch();
			
			//$row = DB::run("SELECT * FROM t_users WHERE user_login=?", [@$_REQUEST['usr']])->fetch();
				
            if($row && $row['user_enabled'] == 1) {
                $row['user_pwd'] = sfDecrypt($row['user_pwd']);
                $b = bfMailLoginInfo($row);

				if($b) $message = _e("i.login.mail_sent");          // La password &egrave; stata inviata, prego controlli la sua e-mail
				else   $message = _e("i.login.mail_fail");          // Fallito l'invio mail con i dati di accesso, riprovi.";
				
				 $dbg['row'] = $row;
				 $dbg['pwd'] = sfDecrypt($row['user_pwd']);
			}
			elseif($row && $row['user_enabled'] != 1) 	throw new Exception( _e("e.login.disabled"));
			else 										throw new Exception( _e("e.login.mailnotfound"));   //La mail inserita non &egrave; associata ad alcun utente abilitato
			
        break; default :
            throw new Exception( _e("e.unrecognized_action") );
        } // switch ( $_REQUEST['action']) 
        
        $status  = true;
        if (empty($message)) $message = _e("i.login.done");  
        
    } catch (Exception $e) {

         $status = false;
        $message = $e->getMessage();
    } 
    
    echo  json_encode(array(
      'status' => $status
    ,'message' => $message
    , 'action' => $action
    ,      'p' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;
}//if(!empty($_REQUEST['action'])) {

?>
<!DOCTYPE html> 
<html><head>
<?= sfHead(_e("p.login")) ?>
</head> 
<body> 
<div id="p-login" data-role="page" data-theme="b" >
	<div data-role="header" data-position="fixed">
		 <h1>Login</h1>
	</div>
        <div data-role="content" >   
    		<div id="logo_image" style="text-align:center;margin:5 auto">
            <img src="<?= C_DEFAULT_SITE_LOGO ?>" style="max-width: 100%">
            </div>

		<form id="p-login_form" method="post">
			      <div class="ui-field-contain"><label><?= _e("l.login.email") ?></label><input name="usr" type="text" required>
			</div><div class="ui-field-contain"><label><?= _e("l.login.pwd")   ?></label><input name="pwd" type="password" required>
			</div><div class="ui-field-contain"><label><?= _e("l.login.remind")?></label>
                <select name="remind" data-role="slider">
                    <option value="0"><?= _e("no") ?></option>
                    <option value="1"><?= _e("yes")?></option>
                </select>               
            </div>
			 <div class="ui-field-contain">
                <label></label>
                <input type="hidden" name="_method" value="post">
				<input type="hidden" name="action" value="login">
				<input type="submit" value="<?= _e("b.login")?>" data-role="button" data-inline="true"/>
                <a href="#p-login_pop-forgot" data-rel="popup" data-position-to="window" data-role="button" data-inline="true"><?= _e("b.login_forgot")?></a>
			</div>					
		</form>
		
		<div data-role="popup" id="p-login_pop-forgot" data-theme="a" class="ui-corner-all">
			<form id="p-login_form-forgot" method="post">
				<div style="padding:10px 20px;">
					<h3><?= _e("h.login_forgot") ?></h3>
					<label class="ui-hidden-accessible"><?= _e("l.login_forgot.email") ?></label>
					<input type="email" name="usr" value="" placeholder="<?=_e("h.login_forgot.your@email")?>" required />
					<input type="hidden" name="_method" value="post">
                    <input type="hidden" name="action" value="forgot" />
					<input type="submit" value="<?= _e("b.login_forgot.submit") ?>" data-role="button" />
				</div>
			</form>
		</div>
	</div><!-- /content -->
<script type="text/javascript">
$.mobile.document.on("submit","#p-login_form", function(e,ui){  

    vfActionLoad($(this).serializeArray(),function(a,r){
        if(!r.status) vfPopUp(_error,r.message,true);
        else /* -- */ location.replace("./");
    }); 
    //alert("login submit:"+$(this).serialize());***
    
    e.preventDefault();
    return false;
});
$.mobile.document.on("submit","#p-login_form-forgot", function(e,ui){  
    $( "#p-login_pop-forgot" ).popup( "close" );
    
    vfActionLoad($(this).serializeArray(),function(a,r){
        if(!r.status) vfPopUp(_error,r.message,true);
        else /* -- */ vfPopUp(_info,r.message,true);
    }); 
    e.preventDefault();
    return false;
});
</script>
</div><!-- /page -->
</body></html>
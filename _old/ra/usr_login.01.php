<?php
require_once ("rr_config.php");

if(!empty($_REQUEST['action'])) {
   
    $action = strtolower($_REQUEST['action']); 
    $method = strtolower($_SERVER['REQUEST_METHOD']);
      $avar = $ga_actions[$action]['r'];
         $c = $ga_actions[$action]['c'];
         $a = $ga_actions[$action]['a'];
       $dbg = array();

    try { 
        switch ( $action ) {
        /****/ case "signin":      
        /*
          access: all
          request: login/email + password
          response: 
            error no user; 
            warning user locked  
            ok goto index; 
        **/        
		break; case "signup":  
        /*
          access: all
          request: login/email + password
          response: 
            error if not abilitate (site core)
            error bad login: about mail,user name; 
            warning user exist  
            ok goto index; 
        **/
        break; case "forgot": 
        /*
          access: all
          request: email
          response: 
            error bad email not recognized; 
            warning user locked not proceed;  
            ok goto reset; 
        **/	       
        break; case "reset": 
        /*
          method-put: update
          access: all
          request: key,pwd1,pwd2
          response: 
            error pwd too short
            error pwd1 <> pwd2
            error bad key; 
            warning user locked not proceed;  
            ok,pop mail sended, goto login;                 
        **/	      
        break; case "profile": 
        /*
          method-put: update
          method-get: select
          access: logged
          request: fullname,new password (if)
          response: 
            error bad old_password; 
            error bad new_password; 
            warning user locked not proceed;  
            ok goto index; 
        **/	   
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
    ,      'r' => $_REQUEST
    ,    'dbg' => $dbg
    ));
    exit;
} // if(!empty($_REQUEST['action'])) {

?> 
<!DOCTYPE html> 
<html><head>
<?= sfHead("login"
           ,array()
           ,array("css/rr_crud.css")
);
?>
<script type="text/javascript">
/*
    page-usr-ACTION
    form-usr-ACTION
    
**/
$('form').on('submit',function(e) {
    
    e.preventDefault();
    return false;
});
$(document).on('pageshow', '#page-usr-profile', function() {
    /* get all info about this user
    **/
});
/*
$(document).on('pagecreate', '#page-usr-signup', function() {
        $('#form-usr-signup').on('submit',function(e) {
            e.preventDefault();
			return false;
		});
        $('#form-user-forgot').on('submit',function(e) {

            
            $('#pop-usr-forgot').popup('open');
            e.preventDefault();
			return false;
		});
        $('#form-user-reset').on('submit',function(e) {

            
            $('#pop-usr-reset').popup('open');
            e.preventDefault();
			return false;
		});
        $('#form-user-profile').on('submit',function(e) {

            
            $('#pop-usr-forgot').popup('open');
            e.preventDefault();
			return false;
		});

});
*/

/*
        $.mobile.pageContainer.pagecontainer("change", "#p2", {
            stuff: this.id,
            transition: "flip"
        });
        
        $(document).on("pagebeforechange", function (e, data) {
            if ($.type(data.toPage) == "object" && data.toPage[0].id == "p2") {
                var stuff = data.options.stuff ? data.options.stuff : null;
                    showStuff("#p2", stuff);
            }
        });
        
        function showStuff(page, data) {
            //...
        }

**/
</script>  
</head><body> 
    
<div id="page-usr-signin" data-role="page" data-theme="b">
	<div data-role="header" data-position="fixed"  data-tap-toggle="false" >
		 <h1 data-i18n="signin">Sign-in</h1>
	</div>
   <div data-role="content" >   
 		<form id="form-usr-signin" method="post"><input type="hidden" name="action" value="signin"/>
			<div class="ui-field-contain">
               <label><span data-i18n="user-name-email">Username or Email</span>
                <a href="#pop-help-user-name-email" data-rel="popup" data-transition="pop" class="tooltip-btn ui-btn ui-corner-all ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" data-i18n="help">Help</a>
                <div data-role="popup" id="pop-help-user-name-email" class="ui-content" data-theme="a" style="max-width:350px;">
                  <p data-i18n="user-name-email-help">Insert here your user-name or your email</p>
                </div>          
                </label>
			   <input name="user_email" type="text" class="required" value="">
			</div>
			<div class="ui-field-contain">
			   <label data-i18n="password">Password:</label>
			   <input id="user_pwd" name="user_pwd" type="password" class="required password">
			</div>
			<div class="ui-field-contain">	
				<label><span data-i18n="remember">Remember Me</span>
                <a href="#pop-help-remember" data-rel="popup" data-transition="pop" class="tooltip-btn ui-btn ui-corner-all ui-alt-icon ui-nodisc-icon ui-btn-inline ui-icon-info ui-btn-icon-notext" data-i18n="help">Help</a>
                <div data-role="popup" id="pop-help-remember" class="ui-content" data-theme="a" style="max-width:350px;">
                  <p data-i18n="remember-help">Do you want the application reconize you on this computer?  </p>
                </div>          
                </label>
                
				<select id="user_remember" name="user_remember" data-role="slider">
					<option value="0" data-i18n="no">No</option>
					<option value="1" data-i18n="yes">Yes</option>
				</select>          
			</div>
			<div class="ui-field-contain">	
				<label></label>
				<input id="do-signin" type="submit" data-i18n="submit" value="Submit" data-role="button" data-inline="true" data-icon="check" data-theme="b" />
				<a href="#page-usr-forgot" data-role="button" data-inline="true" data-icon="search" data-i18n="pwd-forgot">Forgot Password?</a>
			</div>			
       </form>
	</div><!-- /content --> 
    <div data-role="footer" data-position="fixed" data-tap-toggle="false">
        <div data-role="navbar" data-iconpos="right">
            <ul>    
                <li><a href="#page-usr-forgot" data-icon="carat-r">forgot</a></li>
                <li><a href="#pop-usr-forgot" data-icon="carat-r">pop forgot</a></li>
                <li><a href="#page-usr-reset" data-icon="carat-r">reset</a></li>
                <li><a href="#page-usr-signup" data-icon="carat-r">signup</a></li>
                <li><a href="#page-usr-locked" data-icon="carat-r">locked</a></li>
                <li><a href="#page-usr-confirm" data-icon="carat-r">confirm</a></li>
                <li><a href="#page-usr-landing" data-icon="carat-r">landing</a></li>
                <li>&nbsp;</li>
                
            </ul>
        </div><!-- /navbar -->
    </div><!-- /footer -->
</div><!-- /page -->

<div id="page-usr-forgot" data-role="page" data-theme="b">
    <div data-role="header">
		<a href="#page-usr-sign-in" data-rel="back" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-left" data-i18n="back">Back</a>
        <h1 data-i18n="pwd-forgot">Forgot Password</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
        <div class="ui-body-b ui-corner-all">
            <h3 data-i18n="email-required">Email required</h3>
            <p data-i18n="email-required-help">Instructions on how to reset your password will be sent.</p>
        </div>
		<form id="form-user-forgot" method="post"><input type="hidden" name="action" value="forgot"/>
			<div class="ui-field-contain">	
				<label data-i18n="email">Email</label>
				<input type="email" name="user_email" value="">
			</div>
			<div class="ui-field-contain">	
				<label></label>
				<input type="submit" data-i18n="submit" value="Submit" data-role="button" data-inline="true" data-icon="search" />
			</div>
            
<!--@@@--><a href="#pop-usr-forgot" data-rel="popup" data-transition="pop" data-position-to="window" data-inline="true" class="ui-btn ui-btn-b ui-corner-all">pop</a>           
           
            <div data-role="popup" id="pop-usr-forgot" data-dismissible="false" style="max-width:400px;" data-theme="a">
                <div data-role="header">
                    <h1 data-i18n="pwd-forgot">Forgot Password</h1>
                </div>
                <div role="main" class="ui-content">
                    <h3 data-i18n="check-inbox">Check Your Inbox</h3>
                    <p data-i18n="check-inbox-help">We sent you an email with instructions on how to reset your password. Please check your inbox and follow the instructions in the email.</p>
                    <a href="#page-usr-reset" class="ui-btn ui-corner-all ui-shadow ui-btn-b" data-i18n="ok">OK</a>
                </div>
            </div>
		</form>
    </div><!-- /content --> 
</div><!-- /page -->

<div id="page-usr-reset" data-role="page" data-theme="b">
    <div data-role="header">
		<a href="#page-usr-pwd-forgot" data-rel="back" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-left" data-i18n="back">Back</a>
        <h1 data-i18n="pwd-reset">Password Reset</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
		<form id="form-usr-reset" method="post"><input type="hidden" name="action" value="reset"/>
			<div class="ui-field-contain">	
				<label data-i18n="key-received">Key received</label>
				<input type="text" name="user_key" value="">
			</div>
			<div class="ui-field-contain">	
				<label data-i18n="pwd-new">New Password</label>
				<input type="password" name="user_pwd" value="">
			</div>
			<div class="ui-field-contain">	
				<label data-i18n="pwd-new-confirm">Confirm New Password</label>
				<input type="password" name="user_pwd2" value="">
			</div>
			<div class="ui-field-contain">
				<label></label>
				<input type="submit" data-i18n="reset" value="Reset" data-role="button" data-inline="true" data-icon="recycle" />
			</div>
            
<!--@@@--><a href="#pop-usr-reset" data-rel="popup" data-transition="pop" data-position-to="window" data-inline="true" class="ui-btn ui-btn-b ui-corner-all">pop</a>           
            
            <div data-role="popup" id="pop-usr-reset" data-dismissible="false" style="max-width:400px;" data-theme="a">
                <div data-role="header">
                    <h1 data-i18n="pwd-reset">Password Reset</h1>
                </div>
                <div role="main" class="ui-content">
                    <h3 data-i18n="pwd-resetted">Check Your Inbox</h3>
                    <p data-i18n="pwd-resetted-help">Your new password is ready to use.</p>
                    <a href="#page-usr-reset" class="ui-btn ui-corner-all ui-shadow ui-btn-b" data-i18n="ok">OK</a>
                </div>
            </div>
		</form>
    </div><!-- /content --> 
</div><!-- /page -->

<div id="page-usr-signup" data-role="page" data-theme="b">
    <div data-role="header">
        <h1>Sign-up</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
		<form id="form-usr-signup" method="post"><input type="hidden" name="action" value="signup"/>
			<div class="ui-field-contain">
				<label data-i18n="full-name">Full Name</label>
				<input type="text" name="user_fullname" value="">
			</div>
            <div class="ui-field-contain">
				<label data-i18n="user-name">User Name</label>
				<input type="text" name="user_name" value="">
			</div>
			<div class="ui-field-contain">
				<label data-i18n="email">Email</label>
				<input type="email" name="user_email" value="">
			</div>
			<div class="ui-field-contain">
				<label data-i18n="password">Password</label>
				<input type="password" name="user_pwd" value="">
			</div>
			<div class="ui-field-contain">
				<label data-i18n="confirm-password">Confirm Password</label>
				<input type="password" name="user_pwd2" value="">
			</div>
            <div class="ui-field-contain">
            	<label></label>
				<input type="submit" data-i18n="submit" value="Submit" data-role="button" data-inline="true" data-icon="edit" />
			</div>
		</form>

<!--@@@--><a href="#pop-usr-signup" data-rel="popup" data-transition="pop" data-position-to="window" data-inline="true" class="ui-btn ui-btn-b ui-corner-all">pop</a>

			<div id="pop-usr-signup" data-role="popup" data-dismissible="false" style="max-width:400px;" data-theme="a">
				<div data-role="header">
					<h1 data-i18n="confirm-email-title">Almost done...</h1>
				</div>
				<div role="main" class="ui-content">
					<h3 data-i18n="confirm-email">Confirm Your Email Address</h3>
					<p data-i18n="confirm-email-help">We sent you an email with instructions on how to confirm your email address. Please check your inbox and follow the instructions in the email.</p>
					<a href="#page-usr-sign-in" class="ui-btn ui-corner-all ui-shadow ui-btn-b">OK</a>
                </div>
			</div>
    </div><!-- /content -->
</div><!-- /page -->	

<div id="page-usr-profile" data-role="page" data-theme="b">
	<div data-role="header">
		 <a href="index.php" data-ajax="false" data-icon="home">Home</a>
		 <h1 data-i18n="user-profile">Profile</h1>
	</div><!-- /header -->
   <div role="main" class="ui-content">
      <div class="ui-body-b ui-corner-all">
          <h3 data-i18n="change-profile">Change Profile</h3>
          <p data-i18n="change-profile-help">Here you can make changes to your profile. Please note that you will 
            not be able to change your email,user-name which have been already registered.</p>
	  </div>
      <form id="form-usr-profile" method="post"><input type="hidden" name="action" value="profile"/>
 		<div data-role="fieldcontain">
			<label data-i18n="user-name">User name: </label>
			<input type="text" name="user_name" value="" disabled>
		</div>	
 		<div data-role="fieldcontain">
			<label data-i18n="email">Email: </label>
			<input type="email" name="user_email" value="" disabled>
		</div>	
		<div data-role="fieldcontain">
			<label data-i18n="full-name"><em>*</em>Your Full Name: </label>
			<input name="user_name" type="text" class="required" value="">
		</div> 
 		<div data-role="fieldcontain">
			<label data-i18n="phone">Phone: </label>
			<input type="text" name="user_tel" value="">
		</div>	
 		<div data-role="fieldcontain">
			<label data-i18n="pwd-old">Old Password: </label>
			<input type="password" name="user_pwd_old" value="">
		</div>
        <div data-role="fieldcontain">
			<label data-i18n="pwd-new">New Password: </label>
			<input type="password" name="user_pwd" value="">
		</div>
        <div data-role="fieldcontain">
			<label data-i18n="pwd-new-confirm">Confirm New Password: </label>
			<input type="password" name="user_pwd2" value="">
		</div>
        <div data-role="fieldcontain">
            <label></label>
            <input type="submit" name="doProfile" data-i18n="save" value="Save" data-role="button" data-inline="true"/>  
            <a href="index.php" data-role="button" data-inline="true" data-i18n="cancel">Cancel</a>	
        </div>

      </form>
    </div><!-- /content -->
</div><!-- /page -->	

<div id="page-usr-locked" data-role="page" data-theme="b">
    <div data-role="header">
		<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-left" data-i18n="back">Back</a>
        <h1 data-i18n="locked">Locked!</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
        <div class="ui-body-b ui-corner-all">
            <h3 class="text-red" data-i18n="account-locked">Your Account is Locked</h3>
            <p><a href="mailto:<?= C_CONFIG_ADMIN_EMAIL ?>" data-i18n="account-locked-help">
                Please contact the Administrator to resolve this issue.
            </a></p>
        </div>
    </div><!-- /content --> 
</div><!-- /page -->	

<div id="page-usr-confirm" data-role="page" data-theme="b">
    <div data-role="header">
        <h1 data-i18n="confirmed">Confirmed</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
        <h2 class="text-red" data-i18n="account-confirmed">Your Account is Confirmed</h2>
        <p data-i18n="account-confirmed-help">Proceed to Sign In page</p>
        <a href="#page-usr-signin" class="ui-btn ui-corner-all ui-icon-user ui-btn-icon-left" data-i18n="signin">Sign-in</a>
    </div><!-- /content --> 
</div><!-- /page -->	
			
<div id="page-usr-landing" data-role="page" data-theme="b">
    <div data-role="header">
        <h1 data-i18n="welcome">Welcome!</h1>
    </div><!-- /header -->
    <div role="main" class="ui-content">
        <ul data-role="listview" data-inset="true">
            
            <li><a href="#page-usr-signin">
                <img src="img/anonymous.png">
                <h2 data-i18n="user-registered">Existing User</h2>
                <p data-i18n="user-registered-help">Click here to sign-in</p>
            </a></li>
            
            <li><a href="#page-usr-signup">
                <img src="img/alien.png">
                <h2 data-i18n="user-unregistered">New User</h2>
                <p data-i18n="user-unregistered-help">Don't have an account?</p>
            </a></li>
        </ul>
    </div><!-- /content -->
</div><!-- /page -->		

	

</body>
</html>
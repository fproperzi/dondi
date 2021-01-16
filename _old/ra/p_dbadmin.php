<?php

  function adminer_object() {
    class AdminerSoftware extends Adminer {
        function login($login, $password){
            return true;
        }
        function loginForm() {
            $css = "appearance: button;
                    -moz-appearance: button;
                    -webkit-appearance: button;
                    text-decoration: none; font: menu; color: ButtonText;
                    display: inline-block; padding: 2px 8px;";
           
            echo '<input type="submit" value="',_("Login"),'" />';
            echo '&nbsp;<a href="home" style="',$css,'">',_("Home"),'</a>';
            echo '<input name="auth[driver]" type="hidden" value="sqlite" />';
            echo '<input name="auth[username]" type="hidden" value=',C_DEFAULT_ADMIN_LOGIN,'"/>';
            echo '<input name="auth[db]" type="hidden" value="./',C_DEFAULT_DB_SQLITE3,'" />';
        }
      }
    return new AdminerSoftware;
  }
include "./db/adminer.php";

?>

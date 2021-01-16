<?php

  function adminer_object() {
    class AdminerSoftware extends Adminer {
        function login($login, $password){
            return true;
        }
        function loginForm() {
            echo '<input type="submit" value="Login" />';
            echo '<a href="home" style="display: block;width: 115px;height: 25px;background: #4E9CAF;padding: 10px;text-align: center;border-radius: 5px;color: white;font-weight: bold;">Home</a>';
            echo '<input name="auth[driver]" type="hidden" value="sqlite" />';
            echo '<input name="auth[db]" type="hidden" value="ra.sqlite3" />';
        }
      }
    return new AdminerSoftware;
  }
include "./adminer.php";

?>
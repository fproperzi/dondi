<?php
include("../rr_config_def.php");

  function adminer_object()   {
    class AdminerSoftware extends Adminer
    {   //https://eval.in/776078   verify password
        function login($login, $password)          {
            return ($login == C_CONFIG_ADMIN_NAME && password_verify($password, C_CONFIG_ADMIN_HPWQ) );
        }

        function loginForm()
        {
          echo '
            <table cellspacing="0">
            <tbody>
            <tr><th>Username</th>
            <td><input id="username" name="auth[username]" type="text" /></td>
            </tr>
            <tr><th>Password</th>
            <td><input name="auth[password]" type="password" /></td>
            </tr>
            <tr><th>Database</th>
            <td><input name="auth[db]" type="text" value="'. basename(C_CONFIG_DB_SQLITE3) .'"/></td>
            </tr>
            </tbody>
            </table>
          ';

          echo '<input type="submit" value="Login" />';
          echo '<input name="auth[driver]" type="hidden" value="sqlite" />';
        }
      }
    return new AdminerSoftware;
  }
  
include "./adminer_.php";
<?php
  function adminer_object() 
  {
    class AdminerSoftware extends Adminer
      {
        function login($login, $password)
          {			  return true;
            return ($login == 'admin' && $password == 'admin');
          }

        function loginForm()
        {
          /*		  echo '
            <table cellspacing="0">
            <tbody>
            <tr><th>Username</th>
            <td><input id="username" name="auth[username]" type="text" /></td>
            </tr>
            <tr><th>Password</th>
            <td><input name="auth[password]" type="password" /></td>
            </tr>

            </tbody>
            </table>
          ';
*/
          echo '<input type="submit" value="Login" />';
          echo '<input name="auth[driver]" type="hidden" value="sqlite" />';
		  echo '<input name="auth[db]" type="hidden" value="ra.db" />';
        }
      }
    return new AdminerSoftware;
  }
include "./adminer.php";
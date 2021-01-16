<?php
//require_once ("rr_config_def.php");  // edit define
require_once ("rr_config_gen.php");  // generic defines
require_once ("rr_config_func.php"); // tools functions


echo "<pre>", print_r(get_defined_constants (  ),true), "</pre>";
echo "<pre>", print_r(get_defined_vars ( void ),true), "</pre>"; 
?>
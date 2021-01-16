<?php
require_once ("rr_config_def.php"); 
require_once ("rr_config_gen.php");
require_once ("rr_config_func.php"); 

if(!defined('C_CONFIG_SECRET')) { 
    include(C_FILE_CONFIG_DEF_EDIT); // frist installation?
    exit;
}

?>
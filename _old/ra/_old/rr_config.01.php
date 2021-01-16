<?php
 
require_once ("rr_config_gen.php");
require_once ("rr_config_def.php");
require_once ("rr_config_func.php");

if(!defined('C_CONFIG_DB_HOST')) { 
    include(C_FILE_CONFIG_DEF_EDIT); // frist installation?
    exit;
}
/* DB connection and select
**/
if(!($link = mysql_connect(C_CONFIG_DB_HOST, C_CONFIG_DB_USER, C_CONFIG_DB_PASS))) vfGo2Header(C_FILE_ERROR, C_ERROR_CONNECTION);
if(!($db = mysql_select_db(C_CONFIG_DB_NAME, $link)))                              vfGo2Header(C_FILE_ERROR, C_ERROR_DATABASE);
mysql_set_charset('utf8', $link);



/*
Where left 2 right

    0       1       2       3
0   D2L     D5L     A5L     A2L
1   D2C     D5C     A5C     A2C
2   D2R     D5R     A5R     A2R

l2r[ 0] =  0;   l2r[ 1] =  1;   l2r[ 2] =  2;   l2r[ 3] =  3;
l2r[10] = 10;   l2r[11] = 11;   l2r[12] = 12;   l2r[13] = 13;
l2r[20] = 20;   l2r[21] = 21;   l2r[22] = 22;   l2r[23] = 23;

right 2 left

    0       1       2       3
0   A2R     A5R     D5R     D2R
1   D2C     D5C     A5C     A2C
2   D2R     D5R     A5R     A2R


r2l[ 0] = 23;   r2l[ 1] = 22;   r2l[ 2] = 21;   r2l[ 3] = 20;
r2l[10] = 13;   r2l[11] = 12;   r2l[12] = 11;   r2l[13] = 10;
r2l[20] =  3;   r2l[21] =  2;   r2l[22] =  1;   r2l[23] =  0;


    0       1       2      
0   A2L     A2C     A2R    
1   A5L     A5C     A5R    
2   D5L     D5C     D5R    
3   D2L     D2C     D2R

u2b[ 0] =  3;   u2b[ 1] = 13;   u2b[ 2] = 23; 
u2b[10] =  2;   u2b[11] = 12;   u2b[12] = 22; 
u2b[20] =  1;   u2b[21] = 11;   u2b[22] = 21; 
u2b[30] =  0;   u2b[31] = 10;   u2b[32] = 20; 

*/

?>
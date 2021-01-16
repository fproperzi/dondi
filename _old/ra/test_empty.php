<?php
function out($s,$var){
    echo '<tr><td>',$s,'</td><td>'
    ,isset($var) ? "1":"0"
    ,'</td><td>'
    ,empty($var) ? "1":"0"
    ,'</td><td>'
    ,!isset($var)||$var==false ? "1":"0"
    ,'</td><td>'
    ,is_null($var) ? "1":"0"
    ,'</td></tr>';
}

echo '<div>Your PHP version is ' . phpversion() . '</div>';
echo '<table border="1px solid" cellspacing="0" cellpadding="2">';
echo '<tr><th>Value of variable ($var)</th><th>isset($var)</th><th>empty($var)</th><th>!isset($var)||$var==false</th><th>is_null($var)</th></tr>';


$var = '';              out('"" (an empty string)',$var);      
$var = ' ';             out('" " (space)',$var);      
$var = FALSE;           out('FALSE',$var);               
$var = TRUE;            out('TRUE',$var);                     
$var = array();         out('array() (an empty array)',$var);   
$var = NULL;            out('NULL',$var);  
$var = '0';             out('"0" (0 as a string)',$var);        
$var = 0;               out('0 (0 as an integer)',$var);       
$var = 0.0;             out('0.0 (0 as a float)',$var);       
unset($var);   out('var $var; (a variable declared, but without a value)',$var);
$var = '\0';            out('NULL byte ("\ 0")',$var);


echo '</table>';


?>
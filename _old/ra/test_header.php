<?

file_get_contents("http://www.rugby.it/forum");
echo "<pre>";
var_dump($http_response_header);
echo "</pre>";
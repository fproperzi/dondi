<?php
define('C_CONFIG_ADMIN_PWD' ,'$2y$10$CY6Mtsb4Xu3/Ua3zcE/z0.R0UEI8c9LfwSvcmQKkegoVOLDmg/.h2');
define('C_CONFIG_SECRET' ,'d27b161c-d7b5-4dfe-804c-d8cf3b0f86b0');

function sfDecrypt($crypt,$secret=C_CONFIG_SECRET) {
    $ivHashCiphertext = base64_decode($crypt);
    $method = "AES-256-CBC";
    $iv = substr($ivHashCiphertext, 0, 16);
    $hash = substr($ivHashCiphertext, 16, 32);
    $ciphertext = substr($ivHashCiphertext, 48);
    $key = hash('sha256', $secret, true);

    if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash) return null;

    return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
}
function sfEncrypt($plaintext,$secret=C_DEFAULT_SECRET) {
    $method = "AES-256-CBC";
    $key = hash('sha256', $secret, true);
    $iv = openssl_random_pseudo_bytes(16);

    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciphertext, $key, true);

    return base64_encode($iv . $hash . $ciphertext);
}


//echo "decript:",sfDecrypt(C_CONFIG_ADMIN_PWD);

function sfEncodeJwt($payload, $key) {
    
    $base64url_encode = function($s) { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); };
    
    $headers = ['alg'=>'HS256','typ'=>'JWT'];
    $he = $base64url_encode(json_encode($headers));
    $pe = $base64url_encode(json_encode($payload));
    
    $signature = hash_hmac('SHA256',"$he.$pe",$key,true);
    $se =  $base64url_encode($signature);
    
    return "$he.$pe.$se";
}
    
    
function bfCheckJwt($j,$key) {
}

function jfDecodeJwt($j,$key) {
    $base64url_decode = function($s) { return base64_decode(strtr($s, '-_', '+/')); };
    

}


function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
$headers = ['alg'=>'HS256','typ'=>'JWT'];
$headers_encoded = base64url_encode(json_encode($headers));

//build the payload
$payload = ['sub'=>'1234567890','name'=>'John Doe', 'admin'=>true];
$payload_encoded = base64url_encode(json_encode($payload));

//build the signature
$key = 'secret';
$signature = hash_hmac('SHA256',"$headers_encoded.$payload_encoded",$key,true);
$signature_encoded = base64url_encode($signature);

//build and return the token
$token = "$headers_encoded.$payload_encoded.$signature_encoded";

echo $token;


mail("fproperzi@gmail.com,$sSbj, $sMsg, join("\r\n",$headers)

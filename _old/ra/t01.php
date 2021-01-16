<?php
$message = '
    <html>
        <head>
            <title>Benvenuto</title>
        </head>
        <body>
            <h1>Benvenuto sul sito</h1>
            <p>La registrazione è stata effettuata con successo.</p>
        </body>
    </html>
';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';
$i = mail('fproperzi@gmail.com', 'Benvenuto sul sito', $message, implode("\r\n", $headers));

echo $i," --> mail send";
<?php header('Content-Type: application/xhtml+xml; charset=UTF-8'); ?>
<!DOCTYPE html>
<?php
$input = <<<INPUT
bar&quot;; alert(&quot;Meow!&quot;); var xss=&quot;true
INPUT;

$output1 = json_encode($input);
$output2 = json_encode( htmlspecialchars($input) );
$output3 = json_encode( htmlspecialchars($input,ENT_QUOTES,'utf-8') );
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Unescaped Entities</title>
    <meta charset="UTF-8"/>
    <script type="text/javascript">
        <?php
        // this will result in
        // var foo = "bar&quot;; alert(&quot;Meow!&quot;); var xss=&quot;true";
        ?>
        var foo1 = <?= $output1 ?>;
        var foo2 = <?= $output2 ?>;
        var foo3 = <?= $output3 ?>;
    </script>
</head>
<body>
    <p>json_encode() is not good for escaping javascript!</p>
</body>
</html>
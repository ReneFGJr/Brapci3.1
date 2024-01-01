HELLO 2
<pre>
<?php
echo "<h1>POST</h1>";
print_r($_POST);
echo "<h1>GET</h1>";
print_r($_GET);
echo "<h1>SERVER</h1>";
print_r($_SERVER);

$path = $_SERVER['PATH_INFO'];
$pt = explode('/',$path);
echo "<h1>PATH</h1>";
print_r($pt);
?>
</pre>
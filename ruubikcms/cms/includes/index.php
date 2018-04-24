<?php if (basename($_SERVER['REQUEST_URI']) == 'index.php') die ('Access denied');
if (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false) die("Access Denied");
?>
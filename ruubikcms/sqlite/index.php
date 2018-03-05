<?php if (basename($_SERVER['REQUEST_URI']) == 'index.php') die ('Access denied');
if (basename($_SERVER['REQUEST_URI']) == 'ruubikcms.sqlite') die ('Access denied');
if (basename($_SERVER['REQUEST_URI']) == 'ruubikcms-last-login.sqlite') die ('Access denied');
?>
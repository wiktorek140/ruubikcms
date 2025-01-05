<?php
if (basename($_SERVER['REQUEST_URI']) == 'index.php' 
    || strpos($_SERVER['REQUEST_URI'], 'index.php') !== false
) {
    die('Access Denied');
}
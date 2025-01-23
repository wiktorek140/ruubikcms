<?php

if (
    basename((string) $_SERVER['REQUEST_URI']) == 'multilang.php'
    || str_contains((string) $_SERVER['REQUEST_URI'], 'multilang.php')
) {
    die('Access Denied');
}

foreach ($multilang_links as $key => $value) {
    $url = '/' . substr_replace($_SERVER['SCRIPT_NAME'], $key, 0, strpos((string) $_SERVER['SCRIPT_NAME'], '/', 1));
    echo '<a href="' . $url . '">' . $value . '</a>&nbsp;&nbsp;';
}

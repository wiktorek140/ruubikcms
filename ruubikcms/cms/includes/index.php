<?php

if (
    basename((string) $_SERVER['REQUEST_URI']) == 'index.php'
    || str_contains((string) $_SERVER['REQUEST_URI'], 'index.php')
) {
    die('Access Denied');
}

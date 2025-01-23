<?php

if (basename((string) $_SERVER['REQUEST_URI']) == 'required.php' || str_contains((string) $_SERVER['REQUEST_URI'], 'required.php')) {
    die('Access Denied');
}

$start = microtime(true);
$filename = basename((string) $_SERVER['PHP_SELF']);
require __DIR__ . '../../includes/dbconfig.php';
require __DIR__ . 'dbconnection.php';
require __DIR__ . '../../includes/commonfunc.php';
require __DIR__ . 'functions.php';
$stmt = $dbh->prepare('SELECT logout_time, cmslang, pagination_rows, use_help FROM options WHERE id = 1');
if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_NUM);
}

define('LOGOUT_TIME', $result[0][0]);
define('RLANG', $result[0][1]);
define('ROWSPERPAGE', $result[0][2]);
define('USEHELP', $result[0][3]);
require __DIR__ . '../login/session.php';
require __DIR__ . '../login/accesscontrol.php';
require __DIR__ . '../languages/' . RLANG . '.php';
if (USEHELP == 1) {
    if (file_exists(__DIR__ . '../languages/helptexts/' . RLANG . '-help.php')) {
        include __DIR__ . '../languages/helptexts/' . RLANG . '-help.php';
    } else {
        include __DIR__ . '../languages/helptexts/en-help.php';
    }
}

if ($filename == 'sitesetup.php') {
    include __DIR__ . '../settings/settings.php';
}

<?php

if (basename($_SERVER['REQUEST_URI']) == 'required.php' || strpos($_SERVER['REQUEST_URI'], 'required.php') !== false) {
    die('Access Denied');
}

$start = microtime(true);
$filename = basename($_SERVER['PHP_SELF']);
require dirname(__FILE__) . '../../includes/dbconfig.php';
require dirname(__FILE__) . 'dbconnection.php';
require dirname(__FILE__) . '../../includes/commonfunc.php';
require dirname(__FILE__) . 'functions.php';
$stmt = $dbh->prepare('SELECT logout_time, cmslang, pagination_rows, use_help FROM options WHERE id = 1');
if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_NUM);
}

define('LOGOUT_TIME', $result[0][0]);
define('RLANG', $result[0][1]);
define('ROWSPERPAGE', $result[0][2]);
define('USEHELP', $result[0][3]);
require dirname(__FILE__) . '../login/session.php';
require dirname(__FILE__) . '../login/accesscontrol.php';
require dirname(__FILE__) . '../languages/' . RLANG . '.php';
if (USEHELP == 1) {
    if (file_exists(dirname(__FILE__) . '../languages/helptexts/' . RLANG . '-help.php')) {
        include dirname(__FILE__) . '../languages/helptexts/' . RLANG . '-help.php';
    } else {
        include dirname(__FILE__) . '../languages/helptexts/en-help.php';
    }
}

if ($filename == 'sitesetup.php') {
    include dirname(__FILE__) . '../settings/settings.php';
}

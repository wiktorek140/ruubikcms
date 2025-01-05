<?php
if (basename($_SERVER['REQUEST_URI']) == 'required.php' || strpos($_SERVER['REQUEST_URI'], 'required.php') !== false) {
    die('Access Denied');
}

$start = microtime(true);
$filename = basename($_SERVER['PHP_SELF']);
require '../includes/dbconfig.php';
require 'includes/dbconnection.php';
require '../includes/commonfunc.php';
require 'includes/functions.php';
$stmt = $dbh->prepare('SELECT logout_time, cmslang, pagination_rows, use_help FROM options WHERE id = 1');
if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_NUM);
}

define('LOGOUT_TIME', $result[0][0]);
define('RLANG', $result[0][1]);
define('ROWSPERPAGE', $result[0][2]);
define('USEHELP', $result[0][3]);
require 'login/session.php';
require 'login/accesscontrol.php';
require 'languages/'.RLANG.'.php';
if (USEHELP == 1) {
    if (file_exists('languages/helptexts/'.RLANG.'-help.php')) {
        include 'languages/helptexts/'.RLANG.'-help.php';
    } else {
        include 'languages/helptexts/en-help.php';
    }
}

if ($filename == 'sitesetup.php') {
    include 'settings/settings.php';
}

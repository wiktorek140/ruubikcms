<?php
$start = microtime(true);
$filename = basename($_SERVER['PHP_SELF']);
require('../includes/encodingconf.php');
require('../includes/dbconfig.php');
require('includes/dbconnection.php');
require('../includes/commonfunc.php');
require('includes/functions.php');
$stmt = $dbh->prepare("SELECT logout_time, cmslang, pagination_rows, use_help FROM options WHERE id = 1");
if ($stmt->execute()) {
	$result = $stmt->fetchAll(PDO::FETCH_NUM);
}
define('LOGOUT_TIME', $result[0][0]);
define('RLANG', $result[0][1]);
define('ROWSPERPAGE', $result[0][2]);
define('USEHELP', $result[0][3]);
//define('LOGOUT_TIME', query_single("SELECT logout_time FROM options WHERE id = 1"));
require('login/session.php');
require('login/accesscontrol.php');
//define('RLANG', query_single("SELECT cmslang FROM options WHERE id = 1"));
require('languages/'.RLANG.'.php');
if (USEHELP == 1) {
	if (file_exists('languages/helptexts/'.RLANG.'-help.php')) require('languages/helptexts/'.RLANG.'-help.php');
	else require('languages/helptexts/en-help.php');
}
if ($filename == 'sitesetup.php') require('settings/settings.php');
//$siteroot = trim(query_single("SELECT siteroot FROM site WHERE id = 1"),'/');
?>

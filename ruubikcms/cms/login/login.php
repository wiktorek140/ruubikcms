<?php
session_name('cmslogin');
session_start();
session_regenerate_id();
require('../../includes/dbconfig.php');
require('../../includes/commonfunc.php');


try {
	$dbh = new PDO(PDO_DB_DRIVER.':../../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME);
} catch (Exception $exception){
	die($exception->getMessage());
}

define("RLANG", query_single("SELECT cmslang FROM options WHERE id = 1"));
require('../languages/'.RLANG.'.php');

$stmt = $dbh->prepare("SELECT username, role, firstname, lastname FROM cmsuser WHERE username = ? AND password = ?");

if ($stmt->execute(array($_POST['username'], sha1($_POST['passwd'])))) {
	$result = $stmt->fetch(PDO::FETCH_NUM);
}

if (empty($result[0])) {
	$_SESSION['notfound'] = True;
	$_SESSION['time'] = time();
	session_write_close();
	header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));
	exit();
} else {
	$_SESSION['uid'] = $result[0]; //$user;
	$_SESSION['level'] = $result[1]; //$userlevel;
	$_SESSION['firstname'] = $result[2];
	$_SESSION['lastname'] = $result[3];
	$_SESSION['notfound'] = False; 
	$_SESSION['time'] = time();
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}

session_write_close(); // write session file and free the lock

// log message
$date = date("Y-m-d H:i:s");
$msg = $_SESSION['uid'].' '.LOGGEDIN;
$stmt = $dbh->prepare("INSERT INTO log (msg, time, ip, user) VALUES (?, ?, ?, ?)");
$stmt->bindParam(1, $msg);
$stmt->bindParam(2, $date);
$stmt->bindParam(3, $_SESSION['ip']);
$stmt->bindParam(4, $_SESSION['uid']);
@$stmt->execute();

// database backup at login
@copy('../../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME, '../../'.PDO_DB_FOLDER.'/ruubikcms-last-login.sqlite');

// clear all but 200 newest log messages
@$dbh->query("DELETE FROM log WHERE id NOT IN (SELECT id FROM log ORDER BY time DESC LIMIT 200)");
@$dbh->query("DELETE FROM extra_dl_log WHERE rowid NOT IN (SELECT rowid FROM extra_dl_log ORDER BY time DESC LIMIT 200)");
@$dbh->query("DELETE FROM dl_log WHERE rowid NOT IN (SELECT rowid FROM dl_log ORDER BY time DESC LIMIT 200)");

header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));
?>
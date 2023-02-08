<?php
session_name('extralogin');
session_start();
session_regenerate_id();
require('../../ruubikcms/includes/dbconfig.php');
require('../../ruubikcms/includes/commonfunc.php');


try {
	$dbh = new PDO(PDO_DB_DRIVER.':../../ruubikcms/'.PDO_DB_FOLDER.'/'.PDO_DB_NAME);
} catch (Exception $exception){
	die($exception->getMessage());
}

define("RLANG", query_single("SELECT cmslang FROM options WHERE id = 1"));
require('../../ruubikcms/cms/languages/'.RLANG.'.php');

$stmt = $dbh->prepare("SELECT username, organization, firstname, lastname, expirytime, active FROM extrauser WHERE username = ? AND password = ?");

if ($stmt->execute(array($_POST['username'], sha1($_POST['passwd'])))) {
	$result = $stmt->fetch(PDO::FETCH_NUM);
}

if (empty($result[0]) OR (!empty($result[4]) AND $result[4] < date("Y-m-d")) OR $result[5] != 1) {
	$_SESSION['notfound'] = True;
	$_SESSION['time'] = time();
	session_write_close();
	header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));
	exit();
} else {
	$_SESSION['uid'] = $result[0];
	$_SESSION['organization'] = $result[1]; // cms: $userlevel, extranet: organization
	$_SESSION['firstname'] = $result[2];
	$_SESSION['lastname'] = $result[3];
	$_SESSION['notfound'] = False; 
	$_SESSION['time'] = time();
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];	
}

session_write_close(); // write session file and free the lock

// log extranet logins?
/*$date = date("Y-m-d H:i:s");
$msg = $_SESSION['uid'].' EXTRANET'.LOGGEDIN;
$stmt = $dbh->prepare("INSERT INTO log (msg, time, ip, user) VALUES (?, ?, ?, ?)");
$stmt->bindParam(1, $msg);
$stmt->bindParam(2, $date);
$stmt->bindParam(3, $_SESSION['ip']);
$stmt->bindParam(4, $_SESSION['uid']);
@$stmt->execute();*/

header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));
?>

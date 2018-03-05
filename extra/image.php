<?php
// --- Image displayer with authentication
// --- Sample call: image.php?f=imgfile.jpg
// --- Sample call with subfolder: image.php?f=subfolder/imgfile.jpg

require('../ruubikcms/includes/dbconfig.php');
$dbh = new PDO(PDO_DB_DRIVER.':../'.RUUBIKCMS_FOLDER.'/'.PDO_DB_FOLDER.'/'.PDO_DB_NAME); // database connection object
require('../ruubikcms/includes/commonfunc.php');
define('LOGOUT_TIME', query_single("SELECT logout_time FROM options WHERE id = 1"));
require('login/session.php');

// check if logged in
if (!@$_SESSION['uid']) die("Access denied.");

// images directory
define('BASE_DIR','useruploads/images/');

// make sure program execution doesn't time out
@set_time_limit(0);

if (!isset($_GET['f']) OR empty($_GET['f'])) die("Please specify image.");
if (strstr($_GET['f'], '../')) die('Error');
$fpath = BASE_DIR.$_GET['f'];
if (!is_file($fpath)) die("File does not exist.");

// file size in bytes
// $fsize = filesize($fpath);

// get mime type
$mtype = '';

if (function_exists('mime_content_type')) {
	$mtype = mime_content_type($fpath);
} elseif (function_exists('finfo_file')) {
	$finfo = finfo_open(FILEINFO_MIME); // return mime type
	$mtype = finfo_file($finfo, $fpath);
	finfo_close($finfo);
}

if ($mtype == '') {
	$mtype = "image/jpeg";
}

header("Content-type: $mtype");
readfile($fpath);
?>
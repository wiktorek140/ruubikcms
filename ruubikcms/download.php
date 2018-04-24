<?php
// --- File download with authentication and logging
// --- Sample call with optional new name: download.php?f=phptutorial.zip&fc=newname.zip


if (strpos($_SERVER['REQUEST_URI'], 'download.php') !== false) die("Access Denied");

require('includes/dbconfig.php');
require('includes/commonfunc.php');
$dbh = new PDO(PDO_DB_DRIVER.':'.PDO_DB_FOLDER.'/'.PDO_DB_NAME); // database connection object

/*$site = array();
$site = get_site_data();
$siteroot = '/'.($site['siteroot'] != "" ? trim($site['siteroot'],'/').'/' : '');*/

// files directory
define('BASE_DIR','useruploads/files/');

// make sure program execution doesn't time out
@set_time_limit(0);

if (!isset($_GET['f']) OR empty($_GET['f'])) {
	die("Please specify file name for download.");
}

// get real file name, remove any path info to avoid hacking by adding relative path etc
$fname = basename($_GET['f']);
$fname = rtrim($fname);
$fpath = BASE_DIR.$fname;

if (!is_file($fpath)) {
	die("File does not exist. Make sure you specified correct file name.");
}

// file size in bytes
$fsize = filesize($fpath); 

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
	$mtype = "application/force-download";
}

// override original filename with given (optional fc)
if (!isset($_GET['fc']) OR empty($_GET['fc'])) {
	$asfname = $fname;
} else {
	// remove some bad chars
	$asfname = str_replace(array('"',"'",'\\','/'), '', $_GET['fc']);
	if ($asfname === '') $asfname = 'NoName';
}

// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: $mtype");
header("Content-Disposition: attachment; filename=\"$asfname\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);

// download
$file = @fopen($fpath,"r");
if ($file) {
	while(!feof($file)) {
		print(fread($file, 1024*8));
		flush();
		if (connection_status()!=0) {
			@fclose($file);
			die();
		}
	}
	@fclose($file);
}

// log download
$stmt = $dbh->prepare("INSERT INTO dl_log (filename, ip, time) VALUES (?, ?, ?)");
$stmt->bindParam(1, $fname);
$stmt->bindParam(2, $_SERVER['REMOTE_ADDR']);
$stmt->bindParam(3, date("Y-m-d H:i:s"));
$stmt->execute();

// add or increase download counter
$dlcount = query_single("SELECT downloads FROM dl_count WHERE filename = '".$fname."'");
$date = date("Y-m-d H:i:s");
if (!$dlcount) {
	$i = 1;
	$stmt = $dbh->prepare("INSERT INTO dl_count (filename, downloads, count_started, last_dl) VALUES (?, ?, ?, ?)");
	$stmt->bindParam(1, $fname);
	$stmt->bindParam(2, $i);
	$stmt->bindParam(3, $date);
	$stmt->bindParam(4, $date);
	$stmt->execute();
} else {
	$stmt = $dbh->prepare("UPDATE dl_count SET downloads = ? + 1, last_dl = ? WHERE filename = ?");
	$stmt->bindParam(1, $dlcount);
	$stmt->bindParam(2, $date);
	$stmt->bindParam(3, $fname);
	$stmt->execute();
}
?>
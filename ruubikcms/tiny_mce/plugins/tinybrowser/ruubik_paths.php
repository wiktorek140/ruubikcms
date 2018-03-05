<?php
require('../../../includes/dbconfig.php');
require('../../../includes/commonfunc.php');
$dbh = new PDO(PDO_DB_DRIVER.':../../../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME);
require_once('config_tinybrowser.php');
$sql = "SELECT resize_width, resize_height FROM options WHERE id = 1";
foreach ($dbh->query($sql) as $row) {
		//$siteroot = trim($row['siteroot'],'/');
		$resize_width = $row['resize_width'];
		$resize_height = $row['resize_height'];
}
$siteroot = trim(query_single("SELECT siteroot FROM site WHERE id = 1"),'/');

if ($_SESSION['extra'] == TRUE) {
	// Extranet file upload paths
	$tinybrowser['path']['image'] = '/'.($siteroot != "" ? $siteroot.'/' : '').'extra/useruploads/images/'; // Image files location - also creates a '_thumbs' subdirectory within this path to hold the image thumbnails
	$tinybrowser['path']['media'] = '/'.($siteroot != "" ? $siteroot.'/' : '').'extra/useruploads/media/'; // Media files location
	$tinybrowser['path']['file']  = '/'.($siteroot != "" ? $siteroot.'/' : '').'extra/useruploads/files/'; // Other files location
} else {
	// Regular file upload paths (set to absolute by default)
	$tinybrowser['path']['image'] = '/'.($siteroot != "" ? $siteroot.'/' : '').RUUBIKCMS_FOLDER.'/useruploads/images/'; // Image files location - also creates a '_thumbs' subdirectory within this path to hold the image thumbnails
	$tinybrowser['path']['media'] = '/'.($siteroot != "" ? $siteroot.'/' : '').RUUBIKCMS_FOLDER.'/useruploads/media/'; // Media files location
	$tinybrowser['path']['file']  = '/'.($siteroot != "" ? $siteroot.'/' : '').RUUBIKCMS_FOLDER.'/useruploads/files/'; // Other files location
}

// File link paths - these are the paths that get passed back to TinyMCE or your application (set to equal the upload path by default)
$tinybrowser['link']['image'] = $tinybrowser['path']['image']; // Image links
$tinybrowser['link']['media'] = $tinybrowser['path']['media']; // Media links
$tinybrowser['link']['file']  = $tinybrowser['path']['file']; // Other file links

// Image automatic resize on upload (0 is no resize)
$tinybrowser['imageresize']['width']  = $resize_width;
$tinybrowser['imageresize']['height'] = $resize_height;
?>
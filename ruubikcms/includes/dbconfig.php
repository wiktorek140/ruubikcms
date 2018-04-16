<?php 
if (basename($_SERVER['REQUEST_URI']) == 'dbconfig.php') die ('Access denied');
// error reporting, exclude notices
error_reporting(E_NOTICE);
//error_reporting(E_ALL);

// ruubikcms base folder
define("RUUBIKCMS_FOLDER", "ruubikcms");

// database settings
define("PDO_DB_FOLDER", "sqlite");
define("PDO_DB_DRIVER", "sqlite");
define("PDO_DB_NAME", "ruubikcms.sqlite");

// general settings
define("VERNUM", "1.1.3 Stable");

// which cms main menu tabs are visible for administrator (TRUE/FALSE)
define("SHOW_SITESETUP", TRUE);
define("SHOW_NEWS", TRUE);
define("SHOW_SNIPPETS", TRUE);
define("SHOW_USERS", TRUE);
define("SHOW_EXTRANET", FALSE);
define("SHOW_EXTRAUSERS", FALSE);
define("SHOW_CMSOPTIONS", TRUE);

// set default timezone (requires >= 5.1.0)
@date_default_timezone_set(@date_default_timezone_get());


// multiple installations for different languages
define("SHOW_MULTILANG", FALSE);
$multilang_links = array('en' => 'English', 'fi' => 'Finnish', 'sv' => 'Swedish','pl' => 'Polish');

?>

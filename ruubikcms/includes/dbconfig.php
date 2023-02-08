<?php

if (basename($_SERVER['REQUEST_URI']) == 'dbconfig.php') {
    die ('Access denied');
}
// error reporting, exclude notices
error_reporting(E_ALL ^ E_NOTICE);

// ruubikcms base folder
const RUUBIKCMS_FOLDER = "ruubikcms";

// database settings
const PDO_DB_FOLDER = "sqlite";
const PDO_DB_DRIVER = "sqlite";
const PDO_DB_NAME = "ruubikcms.sqlite";

// general settings
const VERNUM = "1.1.3 Stable";

// which cms main menu tabs are visible for administrator (TRUE/FALSE)
const SHOW_SITESETUP = true;
const SHOW_NEWS = true;
const SHOW_SNIPPETS = true;
const SHOW_USERS = true;
const SHOW_EXTRANET = false;
const SHOW_EXTRAUSERS = false;
const SHOW_CMSOPTIONS = true;

// set default timezone (requires >= 5.1.0)
@date_default_timezone_set(@date_default_timezone_get());


// multiple installations for different languages
const SHOW_MULTILANG = false;
$multilang_links = ['en' => 'English', 'fi' => 'Finnish', 'sv' => 'Swedish', 'pl' => 'Polish'];


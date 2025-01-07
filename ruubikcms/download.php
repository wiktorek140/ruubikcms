<?php

use Ruubik\Service\Db\SQLite;
use Ruubik\Service\FileDownloader;

require 'includes/dbconfig.php';
require 'includes/commonfunc.php';

$downloader = new FileDownloader(new SQLite());
$downloader->download($_GET['f'], $_GET['fc'] ?? null);

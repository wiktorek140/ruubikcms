<?php

if (basename((string) $_SERVER['REQUEST_URI']) == 'encodingconf.php') {
    die('Access denied');
}

// Define default encoding and try overwrite it to support custom encoding in PHP

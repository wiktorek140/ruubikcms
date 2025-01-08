<?php

$_SESSION = [];
// empty session variables
@setcookie('extralogin', '', ['expires' => time() - 86400, 'path' => '/']);
// delete cookie (set time to past)
@session_destroy();
// destroy session
header("Location: " . $_SERVER['HTTP_REFERER']);

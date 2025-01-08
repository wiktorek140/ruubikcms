<?php

$_SESSION = [];
// empty session variables
@setcookie('cmslogin', '', ['expires' => time() - 86400, 'path' => '/']);
// delete cookie (set time to past)
@session_destroy();
// destroy session
header('Location: ' . $_SERVER['HTTP_REFERER']);

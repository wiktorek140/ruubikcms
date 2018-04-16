<?php
$_SESSION = array(); //// empty session variables
@setcookie('cmslogin','', time() - 86400, '/'); // delete cookie (set time to past)
@session_destroy(); // destroy session
header("Location: " . $_SERVER['HTTP_REFERER']);
?>

<?php
session_name('cmslogin'); // set session name
session_start(); // start session for login system

// logout after time in seconds
if(@$_SESSION['time'] < (time() - LOGOUT_TIME)) {
	
	// empty session array
	$_SESSION = array();
	// delete session cookie by setting time to past
	if(isset($_COOKIE[session_name()])) {
		@setcookie(session_name(), '', time() - 360000, '/');
	}
	@session_destroy(); 

} else {
// --- session valid, reset timer
	$_SESSION['time'] = time();
}
?>
<?php
if (!@$_SESSION['uid']) {
	//header('Location: http://greybox'.($siteroot != "" ? $siteroot.'' : '').'extra/login.php');
	header('Location: '.($siteroot != "" ? $siteroot.'' : '').'extra/login.php');
	die();
}

// if logout page is not working, use this instead and hardcode your login address (including optional subfolder aka site root):

/*
if (!@$_SESSION['uid']) {
	header('Location: /extra/login.php');
	die();
}
*/
?>
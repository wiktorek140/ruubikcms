<?php if (basename($_SERVER['REQUEST_URI']) == 'accesscontrol.php') die ('Access denied'); ?>
<?php
if (!@$_SESSION['uid']) {
	header('Location: login.php');
	die();
}
?>
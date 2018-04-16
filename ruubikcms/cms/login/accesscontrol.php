<?php
if (!@$_SESSION['uid']) {
        header('Location: login.php');
        die();
}
?>
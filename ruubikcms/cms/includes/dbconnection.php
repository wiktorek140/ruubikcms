<?php
try {
	$dbh = new PDO(PDO_DB_DRIVER.':../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME);
} catch(Exception $exception){
	die($exception->getMessage());
}
?>
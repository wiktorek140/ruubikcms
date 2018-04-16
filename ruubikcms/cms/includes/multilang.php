<?php if (basename($_SERVER['REQUEST_URI']) == 'multilang.php') die ('Access denied'); ?>
<?php
    foreach ($multilang_links as $key => $value) {
	$url = '/'.substr_replace($_SERVER['SCRIPT_NAME'], $key, 0, strpos($_SERVER['SCRIPT_NAME'], '/', 1));
	echo '<a href="'.$url.'">'.$value.'</a>&nbsp;&nbsp;';
    }
?>
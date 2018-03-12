<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
require('../includes/encodingconf.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>RuubikCMS Avaiable Function Check</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="install.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		body {
			background: #8faedb;
			font-family: verdana, sans-serif;
		}

		h1 {font-size: 1.5em;}
		h2 {font-size: 1em;}

		.first {
			width: 420px;
			padding-top: 2px;
		}

		.container {
			margin-left: auto;
			margin-right: auto;
			width: 780px;
			padding: 15px 15px;
			margin-top: 50px;
			background: #dddddd;    
			border: 5px solid #8a96b4;
		}

		.ok {color: green;}
		.failed {color: red;}
</style>

</head>
<body>
	<div class="container">
		<h1>RuubikCMS Installation Check</h1>
		<h2>PHP version and required extensions</h2>
		<table>
			<?php
			$error = array();
			
			echo '<tr><td class="first">PHP version 5.1.0+ <i>('.PHP_VERSION.')</i></td>';
			if (version_compare(PHP_VERSION, '5.1.0') === 1) {
				echo '<td class="ok">OK';
			} else {
				echo '<td class="failed">FAILED';
				$error[] = 'PHP version must be at least 5.1.0. Your version: '.PHP_VERSION;
			}
			echo '</td></tr>';
			
			echo '<tr><td class="first">PHP extension <b>PDO</b> loaded</td>';
			if (extension_loaded('PDO')) {
				echo '<td class="ok">OK';
			} else {
				echo '<td class="failed">FAILED';
				$error[] = 'PHP extension PDO must be enabled';
			}
			echo '</td></tr>';
			
			echo '<tr><td class="first">PHP extension <b>pdo_sqlite</b> loaded</td>';
			if (extension_loaded('pdo_sqlite')) {
				echo '<td class="ok">OK';
			} else {
				echo '<td class="failed">FAILED';
				$error[] = 'PHP extension pdo_sqlite must be enabled';
			}
			echo '</td></tr>';
			?>
			
		</table>
		<h2>Writable directories and files</h2>
		<table>
			<?php
			$writable = array(
				'ruubikcms/sqlite' => '../sqlite',
				'ruubikcms/sqlite/ruubikcms.sqlite' => '../sqlite/ruubikcms.sqlite',
				'ruubikcms/useruploads' => '../useruploads',
				'ruubikcms/useruploads/images' => '../useruploads/images',
				'ruubikcms/useruploads/images/_thumbs' => '../useruploads/images/_thumbs',
				'ruubikcms/useruploads/files' => '../useruploads/files',
				'ruubikcms/useruploads/media' => '../useruploads/media',
				/*'extra/useruploads' => '../../extra/useruploads',
				'extra/useruploads/images' => '../../extra/useruploads/images',
				'extra/useruploads/images/_thumbs' => '../../extra/useruploads/images/_thumbs',
				'extra/useruploads/files' => '../../extra/useruploads/files',
				'extra/useruploads/media' => '../../extra/useruploads/media',*/
			);
			foreach ($writable as $key => $value) {	
				echo '<tr><td class="first">'.$key.'</td><td>';
				if (!is_writable($value)) {
					$error[] = $key.' must be writable';
					echo '<span class="failed">FAILED</span>';
				} else {
					echo '<span class="ok">OK</span>';
				}
				echo '</td></tr>';
			}
			
			// optional old files, check permissions if exists
			$writable_opt = array(
				'ruubikcms/sqlite/ruubikcms-last-login.sqlite' => '../sqlite/ruubikcms-last-login.sqlite',
				'ruubikcms/sqlite/ruubikcms-backup.sqlite' => '../sqlite/ruubikcms-backup.sqlite',
			);
			foreach ($writable_opt as $key => $value) {	
				if (file_exists($value)) {
					echo '<tr><td class="first">'.$key.'</td><td>';
					if (!is_writable($value)) {
						$error[] = $key.' must be writable';
						echo '<span class="failed">FAILED</span>';
					} else {
						echo '<span class="ok">OK</span>';
					}
					echo '</td></tr>';
				}
			}

            //print(ini_set('iconv.internal_encoding','UTF-8'));

            echo '<tr><td>';
            //$data = ini_get_all(); 
            //print "<pre>";
            //print_r($data);
            //print "</pre>";

            phpinfo();
            echo '</td></tr>';
			?>
			
		</table>
		<?php 
			if(empty($error)) {
				echo '<h3 class="ok">Installation successful!</h3>';
			} else {
				echo '<h3 class="failed">Installation failed!</h3><p>You must correct following issues in order to finish the installation:</p><ul>';
				foreach ($error as $value) {
					echo '<li>'.$value.'</li>';
				}
				echo '</ul><p><input type="button" name="refresh" onclick="location.reload(true)" value="Test again" /></p>';
				echo 'You can try to use RuubikCMS but it is likely to fail:';
			}
			echo '<p><a href="../">Login to RuubikCMS</a> (default username: <b>admin</b>, password: <b>ruubik</b>)</p>';
			echo '<p>Remember to change the default administrator password from <b>Users</b> tab immediately!</p>';
			echo '<p>Check that direct access to the CMS database is <b>denied/forbidden</b>: <a href="../sqlite/ruubikcms.sqlite">CHECK ACCESS</a></p>';
			echo '<p>Remove Installation Check directory <b><i>/ruubikcms/install/</i></b> after successful installation.</p>';
		?>
	</div>
</body>
</html>

<?php
/*   RuubikCMS - The easy & fast way to build Google optimized websites
 *   Copyright (C) 2008-2009 Iisakki Pirilä, Henrik Valros
 * 	 Website: <http://www.ruubikcms.com>, Email: <info@ruubikcms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require('../ruubikcms/includes/dbconfig.php');
$dbh = new PDO(PDO_DB_DRIVER.':../'.RUUBIKCMS_FOLDER.'/'.PDO_DB_FOLDER.'/'.PDO_DB_NAME); // database connection object
define('LOGOUT_TIME', 1800);
require('login/session.php');
if (@$_SESSION['uid']) {
	header('Location: index.php');
	exit;
}
require('../ruubikcms/includes/commonfunc.php');
require('../ruubikcms/cms/includes/functions.php');
define("RLANG", query_single("SELECT cmslang FROM options WHERE id = 1"));
require('../ruubikcms/cms/languages/'.RLANG.'.php');
$cmspage = LOGIN;
$company = query_single("SELECT name FROM site WHERE id = 1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo RLANG;?>" lang="<?php echo RLANG;?>">
	<head>
		<title>RuubikCMS - <?php echo $cmspage;?></title>
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
		<meta name="description" content="RuubikCMS - Easy and fast way to build Google optimized websites." />
		<meta name="robots" content="none" />

		<link rel="shortcut icon" href="../<?php echo RUUBIKCMS_FOLDER;?>/cms/css/images/logo.ico" />

		<link rel="stylesheet" type="text/css" href="login/css/styleLogin.css" />
		<script type="text/javascript">
		function focusit() {
			document.getElementById('username').focus();
		}
		window.onload = focusit;
		</script>

    </head>
    <body>

        <div id="wrapper">

        <!-- **************** HEADER ******************** -->

            <!--<div id="header">

                <div id="top">
					<div id="ruubikVersion"><?php echo VERSION;?>:<span><?php echo VERNUM;?></span></div>
				</div>

			</div>-->
		<!-- ***** HEADER ENDS ***** -->


        <!-- **************** MAINBODY ******************** -->

			<div id="mainBody">


			<!-- **************** RightDiv ******************** -->

				<div id="login">

					<div id="innerLogin">

						<?php
							require('login/form.php');
							if (@$_SESSION['notfound']) echo '<p>'.INCORRECTUSER.'</p>';
						?>

					</div>

				</div>

				<div id="loginFooter">
					<?php echo EXTRANET.' '.LOGIN ;?> &copy; <?php echo date("Y").' <a href="../">'.$company.'</a>';?>
				</div>

			</div>
		</div>
</body>
</html>

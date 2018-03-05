<?php
/*   RuubikCMS - The easy & fast way to build Google optimized websites
 *   Copyright (C) 2008-2010 Iisakki Pirilä, Henrik Valros
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
require('../includes/dbconfig.php');
require('includes/dbconnection.php');
define('LOGOUT_TIME', 1800);
require('login/session.php');
if (@$_SESSION['uid']) {
	header('Location: index.php');
	exit;
}
require('../includes/commonfunc.php');
require('includes/functions.php');
define("RLANG", query_single("SELECT cmslang FROM options WHERE id = 1"));
require('languages/'.RLANG.'.php');
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
		<meta name="keywords" content="RuubikCMS" />
		<meta name="robots" content="NONE" />
		<meta name="rating" content="General" />
		<meta name="DC.Title" content="RuubikCMS <?php echo VERNUM;?>" />
		<meta name="DC.Publisher" content="RuubikCMS.com" />
		<meta name="DC.Language" content="EN" />
		<meta name="doc-rights" content="Copywritten Work" />
		<meta name="author" content="Iisakki Pirilä, Henrik Valros" />
		<meta name="copyright" content="Iisakki Pirilä, Henrik Valros" />

		<link rel="shortcut icon" href="images/logo.ico" />                           
		<link rel="stylesheet" type="text/css" href="css/default.css" />
		<!--<link rel="stylesheet" type="text/css" href="css/ruubikBody.css" />-->

		<link rel="stylesheet" type="text/css" href="css/styleLogin.css" />	 	        		           
		<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="css/styleIE6hacks.css" /><![endif]--> 
		<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="css/styleIE7hacks.css" /><![endif]-->
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
					<?php echo $company;?> - <a href="http://www.ruubikcms.com/" target="_blank">RuubikCMS</a> v<?php echo VERNUM;?> - <a href="../../" target="_blank"><?php echo VIEWSITE;?></a>
				</div>
				
			</div>
		</div>
</body>
</html>
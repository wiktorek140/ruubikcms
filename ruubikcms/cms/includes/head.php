<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo RLANG;?>" lang="<?php echo RLANG;?>">
    <head>
        <title>RuubikCMS - <?php echo ec($cmspage);?></title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="RuubikCMS - The easy & fast way to build Google optimized websites." />
        <meta name="keywords" content="CMS, SEO, RuubikCMS" />
        <meta name="robots" content="none" />
        <meta name="author" content="Iisakki Pirilä, Henrik Valros" />
        <meta name="copyright" content="Iisakki Pirilä, Henrik Valros" />

        <link rel="shortcut icon" href="images/logo.ico" />
        <link rel="stylesheet" href="css/default.css" />
        <link rel="stylesheet" href="css/ruubikBody.css" />
        <link rel="stylesheet" href="css/ruubikMainMenu.css" />
        <link rel="stylesheet" href="css/ruubikWebMenu.css" />
        <link rel="stylesheet" href="css/ruubikButtons.css" />
        <link rel="stylesheet" href="css/ruubikToolTip.css" />
        <link rel="stylesheet" href="css/styleLogin.css" />
        <link rel="stylesheet" href="css/datePicker.css" />
        <!--<link rel="stylesheet" href="css/styleTabs.css" />-->
        <?php if ($cmspage == SITESETUP) echo '<link rel="stylesheet" href="css/styleSettingsWebsite.css" />';?>
        <?php if ($cmspage == NEWS) echo '<link rel="stylesheet" href="css/styleAdminNews.css" />';?>
        <?php if ($cmspage == SNIPPETS) echo '<link rel="stylesheet" href="css/styleAdminNews.css" />';?>
        <?php if ($cmspage == LOG) echo '<link rel="stylesheet" href="css/styleAdminNews.css" />';?>
        <?php if ($cmspage == USERS) echo '<link rel="stylesheet" href="css/styleAdminNews.css" />';?>
        <?php if ($cmspage == EXTRAUSERS) echo '<link rel="stylesheet" href="css/styleExtranetUsers.css" />';?>
        <?php if ($cmspage == WEBPAGES OR $cmspage == EXTRANET) echo '<link rel="stylesheet" href="css/styleAdminWebsite.css" />';?>
        <?php if ($cmspage == CMSOPTIONS) echo '<link rel="stylesheet" href="css/styleSettingsCMS.css" />';?>
        <!--<link rel="stylesheet" href="css/styleSkins.css" />-->
	<!--[if IE 6]><link rel="stylesheet" media="screen" href="css/styleIE6hacks.css" /><![endif]--> 
	<!--[if IE 7]><link rel="stylesheet" media="screen" href="css/styleIE7hacks.css" /><![endif]--> 
	
        <!--<script src="scripts/icon.js" type="text/javascript"></script>-->
        <script type="text/javascript" src="scripts/rollover.js"></script>
		<script type="text/javascript" src="../tinymce/tinymce.min.js"></script>
		<script type="text/javascript" src="scripts/tinyinit.js"></script>
		<script type="text/javascript" src="scripts/jquery.js"></script>
		<script type="text/javascript" src="scripts/date.js"></script>
		<?php if ($cmspage != SNIPPETS AND $cmspage != USERS) echo '<script type="text/javascript" src="scripts/pagemenu.js"></script>';?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <!-- beautytips stuff -->
        <!--[if lt IE 9]>
	<script src="scripts/jquery-bt/jquery.bgiframe.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="scripts/excanvas.js" type="text/javascript" charset="utf-8"></script>
	<![endif]-->
        <script src="scripts/jquery-bt/jquery.hoverIntent.minified.js" charset="utf-8"></script>
        <script src="scripts/jquery-bt/jquery.bt.js" charset="utf-8"></script>
        <script src="scripts/tooltip.js"></script>
        <script src="scripts/jquery.datePicker.js"></script>

        <?php if ($cmspage == WEBPAGES or $cmspage == EXTRANET) echo '<script src="scripts/pageadmin.js"></script>';
        if ($cmspage == NEWS OR $cmspage == EXTRAUSERS) echo '<script src="scripts/newsadmin.js"></script>';?>

    </head>
    <body>   
        <div id="wrapper">

            <!-- **************** HEADER ******************** -->
            <div id="header">
                <div id="top">
                    <div id="topLinks">
                        <span id="linksBox">
                            <?php if(SHOW_MULTILANG) { include('includes/multilang.php'); echo '| '; } ?><a href="../../" target="_blank"><?php echo VIEWSITE;?></a> | <a href="login/logout.php"><?php echo LOGOUT;?></a>
                        </span>
                    </div>
                    <div id="topUser">
                        <span id="userBox"><?php 
                            $name = $_SESSION['firstname'].' '.$_SESSION['lastname'];
                            if ($name == ' ') $name = $_SESSION['uid'];
                            echo USER.': <span id="userName">'.$name.'</span>';
                            ?>
                        </span>
                    </div>
                </div>
                <div id="mainNavigation">
                    <ul>
                        <!-- **** MAIN MENU **** -->
                        <?php require('mainmenu.php');?>
                        <!-- **** MAIN MENU ENDS **** -->
                    </ul>  			
                </div>  

                <div id="infoMessage">
                    <div id="messageText">
                        <?php echo substr(query_single("SELECT time FROM log ORDER BY time DESC LIMIT 1"),-8);?> - <?php echo query_single("SELECT msg FROM log ORDER BY time DESC LIMIT 1")?>
                    </div>
                </div>  
            </div>
            <!-- ***** HEADER ENDS ***** -->		
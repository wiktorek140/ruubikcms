<?php
require('ruubikcms/includes/encodingconf.php');
require('ruubikcms/page.php');
echo $page['doctype'];
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $site['lang'];?>" lang="<?php echo $page['lang'];?>">
    <head>
        <title><?php echo $page['title'];?></title>
        <?php echo $page['gacode'];?>
	<meta charset="<?php echo $page['charset'];?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo $page['description'];?>" />
	<meta name="keywords" content="<?php echo $page['keywords'];?>" />
	<meta name="robots" content="<?php echo $page['robots'];?>" />
	<meta name="doc-rights" content="Copywritten Work" />
	<meta name="author" content="<?php echo $page['author'];?>" />
	<meta name="copyright" content="<?php echo $page['copyright'];?>" />

	<link rel="shortcut icon" href="<?php echo $siteroot;?>ruubikcms/website/images/logo.ico" />   		
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/default.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleBody.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleColLeft.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleColCenter.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleColRight.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleMenus.css" />
	<link rel="stylesheet" href="<?php echo $siteroot;?>ruubikcms/website/css/styleGallery.css" />
	<link rel="stylesheet" href="<?php echo $siteroot;?>ruubikcms/website/lightbox/css/lightbox.min.css"/>
        <link rel="stylesheet" href="<?php echo $siteroot;?>default.css" />
	
        
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="<?php echo $siteroot;?>ruubikcms/website/lightbox/js/lightbox.min.js"></script>
        <script src="<?php echo $siteroot;?>ruubikcms/website/scripts/datescripts.js"></script>	
    </head>
	<body>
		<div id="wrapper">
			<div id="innerWrapper">
			<!-- **************** HEADER ******************** -->
				<div id="header">
					<div id="design"></div>
					<div id="mainMenu">
                                            <?php
                                            if(isMobile()) {
                                                echo $page['dropdownmenu'];
                                            } else {
                                                echo $page['mainmenu'];
                                            }
                                            ?>
					</div>
					<div class="clear"></div>
				</div>

				<!-- **************** MAINBODY ******************** -->

				<div id="mainBody">
                                    <?php
                                        if(!isMobile()){
                                            echo '<div id="ColLeft"><div id="subMenu">'.
                                                 $page['submenu1'].
                                                 '</div></div>';
                                        } else {
                                            echo '<style> #ColCenter{width: 74%; border-left: none;} </style>';
                                        }
                                        
                                    ?>
                                    <div id="ColCenter">
                                        <div id="content">
                                            <h1><?php echo $page['header1'];?></h1>
                                            <?php echo $page['content'];?>
                                        </div>
                                    </div>

                                    <div id="ColRight">
                                            <?php snippet_php('right-column');?>
                                    </div>
                                    <div class="clear"></div>
				</div>

				<!-- **************** FOOTER ******************** -->

				<div id="footer">
                                    <div>
                                        <!-- Please leave "Powered by RuubikCMS" notice here! -->
                                        Powered by <a href="http://www.ruubikcms.com/">RuubikCMS</a>
                                        | Copyright &copy; <?php echo date("Y"); echo " ".$page['sitename'];?>
                                    </div>
				</div>                
			</div>
		</div>	
	</body>
</html>

<?php
require('ruubikcms/page.php');
echo $page['doctype'];
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $site['lang'];?>" lang="<?php echo $page['lang'];?>">
	<head>
		<title><?php echo $page['title'];?></title>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $page['charset'];?>" />
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
		<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/styleGallery.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $siteroot;?>ruubikcms/website/css/jquery.lightbox-0.5.css" media="screen" />
		
		<script type="text/javascript" src="<?php echo $siteroot;?>ruubikcms/cms/scripts/jquery.js"></script>
		<script type="text/javascript" src="<?php echo $siteroot;?>ruubikcms/website/scripts/jquery.lightbox-0.5.js.php"></script>
		<script type="text/javascript" src="<?php echo $siteroot;?>ruubikcms/website/scripts/lightbox.select.js"></script>
		
	</head>
	<body>

		<div id="wrapper">

			<div id="innerWrapper">

			<!-- **************** HEADER ******************** -->

				<div id="header">

					<div id="design">
					</div>

					<div id="mainMenu">
					
							<?php echo $page['mainmenu'];?>

					</div>
					<div class="clear"></div>
					
				</div>

				<!-- **************** MAINBODY ******************** -->

				<div id="mainBody">

					<div id="ColLeft">

						<div id="subMenu">
							<?php echo $page['submenu1'];?>
						</div>

					</div>

					<div id="ColCenter">

						<div id="content">

    						<h1><?php echo $page['header1'];?></h1>

    						<?php echo $page['content'];?>

							<!-- for security reasons, remove extracode if not needed: -->
							<?php eval("?>".$page['extracode']);?>
							
						</div>

					</div>

					<div id="ColRight">

						<div id="decorationPic"><img src="<?php echo $page['image1'];?>" alt="img" />
						</div>

						<div id="news">

                            <div id="newsHeading">News
                            </div>
							<?php echo $page['news'];?>

						</div>	

					</div>

					<div class="clear"></div>

				</div>

				<!-- **************** FOOTER ******************** -->

				<div id="footer">
					<div>
						<!-- Please leave "Powered by RuubikCMS" notice here! -->
						Powered by <a href="http://www.ruubikcms.com/">RuubikCMS</a>

						| Copyright &copy; <?php echo date("Y");?> <?php echo $page['sitename'];?>					
					</div>
				</div>                

			</div>
		</div>
		
	</body>
</html>
<?php

// -------- SCROLL DOWN TO EDIT HTML FOR DIFFERENT PAGE PARTS (MENUS, NEWS, ETC...) -----------------------------------------------------------
// -------- THESE PARTS ARE SEPARATED BY LINES ------------------------------------------------------------------------------------------------

if (basename($_SERVER['REQUEST_URI']) == 'extrapage.php') die ('Access denied');
require('../ruubikcms/includes/dbconfig.php');
require('../ruubikcms/includes/doctypes.php');
require('../ruubikcms/includes/commonfunc.php');
$dbh = new PDO(PDO_DB_DRIVER.':../'.RUUBIKCMS_FOLDER.'/'.PDO_DB_FOLDER.'/'.PDO_DB_NAME); // database connection object

$page = array();
$site = array();
$site = get_site_data();
$siteroot = '/'.($site['siteroot'] != "" ? trim($site['siteroot'],'/').'/' : '');

define('LOGOUT_TIME', query_single("SELECT logout_time FROM options WHERE id = 1"));
require('login/session.php');
require('login/accesscontrol.php');

if ($site['clean_url'] >= 1) {
	$array = explode('/',$_SERVER['REQUEST_URI']);
	$pagearr = explode('.', end($array));
	$_GET['p'] = $pagearr[0];
	if ($_GET['p'] == 'index') $_GET['p'] = ""; // uri index.php without trailing slash
	if (count($array) >= 3) $mainmenu = $array[1];
	if (count($array) == 4) $submenu = $array[2];
	$clean_url = TRUE;
} else {
	$clean_url = FALSE;
}

if ($site['url_suffix'] != '') $url_suffix = '.'.trim($site['url_suffix'], '.');
else $url_suffix = '';

// if no page defined -> get the first page
if (!$_GET['p'] AND !$_GET['news']) {
	$_GET['p'] = frontpage_value('pageurl', 'extrapage');
}

if ($_GET['news']) {
	// TODO: Extranet News!
	// get pagedata for news
	$stmt = $dbh->prepare("SELECT id, title, text, STRFTIME('%d.%m.%Y',time) as date FROM news WHERE status = 1 AND id = ?");
	if ($stmt->execute(array($_GET['news']))) {
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$page = $result[0];
	}
	$page['header1'] = ec($page['title']);
	if ($site['news_showdate'] == 1) $page['header1'] .= '<span id="newsdate"> ('.$page['date'].')</span>';
	$page['subheader'] = frontpage_value('name'); // todo: only needed when using subheader
	$page['content'] = $page['text'];
	
} elseif ($_GET['p']) {
	// get published pagedata for normal page content)
	$page = get_page_data($_GET['p'], TRUE, 'extrapage');
	// subheader, todo: only needed when using subheader
	if ($page['levelnum'] == 1) $page['subheader'] = $page['name'];
	elseif ($page['levelnum'] == 2) $page['subheader'] = query_single("SELECT name FROM extrapage WHERE pageurl = '".$page['mother']."'");
	elseif ($page['levelnum'] == 3) $page['subheader'] = query_single("SELECT name FROM extrapage WHERE pageurl = '".query_single("SELECT mother FROM extrapage WHERE pageurl ='".$page['mother']."'")."'");
	// extracode without slashes:
	$page['extracode'] = stripslashes($page['extracode']);
}

// get the actual doctype text with key from array
$page['doctype'] = $doctypecode[$site['doctype']];

// also use page-array for site values, htmlentities encoded where necessary
$page['lang'] = ec($site['lang']);
$page['charset'] = ec($site['charset']);
$page['robots'] = ec($site['robots']);
$page['copyright'] = ec($site['copyright']);
$page['author'] = ec($site['author']);
$page['gacode'] = $site['gacode'];
$page['sitename'] = ec($site['name']);

// check no image1
if (!$page['image1'] OR $page['image1'] == "") {
	// use front page image1 if this option is selected
	if ($site['no_image1'] == 1) $page['image1'] = frontpage_value('image1', 'extrapage');
	// otherwise use transparent 1px dummy gif
	else $page['image1'] = $siteroot.RUUBIKCMS_FOLDER.'/includes/empty.gif';
}

// check no image2
if (!$page['image2'] OR $page['image2'] == "") {
	// use front page image2 if this option is selected
	if ($site['no_image2'] == 1) $page['image2'] = frontpage_value('image2', 'extrapage');
	// otherwise use transparent 1px dummy gif
	else $page['image2'] = $siteroot.RUUBIKCMS_FOLDER.'/includes/empty.gif';
}

// use site values when page values empty
if (!$page['title'] OR $page['title'] == "") $page['title'] = $site['title'];
if (!$page['description'] OR $page['description'] == "") $page['description'] = $site['description'];
if (!$page['keywords'] OR $page['keywords'] == "") $page['keywords'] = $site['keywords'];

// select correct mother for submenu1 & selected class & slide submenu
if ($page['levelnum'] == 2) {
	$p = $page['mother'];
} elseif ($page['levelnum'] == 3) {
	$p = query_single("SELECT mother FROM extrapage WHERE pageurl = '".$page['mother']."'"); // a.k.a grandmother
	$submenu_selected = $page['mother'];
} else {
	$p = $_GET['p'];
}

// -------- SLIDE SUBMENU WITH NESTED UL'S -------------------------------------------------------------------------------------------------
$page['submenuslide'] = '<ul id="treemenu" class="treeview">';
$stmt = $dbh->prepare("SELECT pageurl, name FROM extrapage WHERE levelnum = 2 AND mother = ? AND status = 1 ORDER BY ordernum");
if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$page['submenuslide'] .= '<li><a href="'.($clean_url ? clean_url($row['pageurl']) : 'index.php?p='.$row['pageurl']).'">'.$row['name'].'</a>';
		$sql2 = "SELECT pageurl, name FROM extrapage WHERE levelnum = 3 AND mother = '".$row['pageurl']."' AND status = 1 ORDER BY ordernum";
		$level2count = 0;
		foreach ($dbh->query($sql2) as $row2) {
			if ($level2count == 0) $page['submenuslide'] .= '<ul>';
			$page['submenuslide'] .= '<li><a href="'.($clean_url ? clean_url($row2['pageurl']) : 'index.php?p='.$row2['pageurl']).'">'.$row2['name'].'</a></li>';
			$level2count++;
		}
		if ($level2count != 0) $page['submenuslide'] .= '</ul>'; 
		$page['submenuslide'] .= '</li>';
	}
}
$page['submenuslide'] .= '</ul>';
// -----------------------------------------------------------------------------------------------------------------------------------------


// -------- MAIN MENU FROM LEVEL 1 PAGES ---------------------------------------------------------------------------------------------------
$sql = "SELECT pageurl, name FROM extrapage WHERE levelnum = 1 AND status = 1 ORDER BY ordernum";
$counter = 0;
foreach ($dbh->query($sql) as $row) {
	if ($counter == 0) $page['mainmenu'] = '<ul>';
	if ($clean_url) $url = $siteroot.'extra/index.php/'.$row['pageurl'].$url_suffix;
	else $url = 'index.php?p='.$row['pageurl'];
	
	// ---- EDIT MAIN MENU HTML HERE -------------------------------------------------------------------------------------------------------
	// Some examples, to use one leave it uncommented and comment the others (// in the beginning of the line)
	
	// ---- BASIC mainMenu 1, HTML:	<ul><li><a href="#">Link</a></li></ul> -----------------------------------------------------------------
	//$page['mainmenu'] .= '<li'.($p == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.$url.'">'.$row['name'].'</a></li>';
	// -------------------------------------------------------------------------------------------------------------------------------------
	
	// ---- BASIC mainMenu 2, HTML:	<ul><li><div><a href="#">Link</a></div></li></ul> ------------------------------------------------------
	$page['mainmenu'] .= '<li'.($p == $row['pageurl']  ? ' class="selected"' : '').'><div><a href="'.$url.'">'.$row['name'].'</a></div></li>';
	// -------------------------------------------------------------------------------------------------------------------------------------

	// ---- BASIC mainMenu 3, HTML:	<ul><li><a href="#"><span>Link</span></a></li></ul> ----------------------------------------------------
	//$page['mainmenu'] .= '<li'.($p == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.$url.'"><span>'.$row['name'].'</span></a></li>';
	// -------------------------------------------------------------------------------------------------------------------------------------
	
	$counter++;
}
// Uncomment to add "Log out" to main menu:
$page['mainmenu'] .= '<li><div><a href="'.$siteroot.'extra/login/logout.php">Log Out</a></div></li>';

if ($counter != 0) $page['mainmenu'] .= '</ul>';

// ------------------------------------------------------------------------------------------------------------------------------------------

// front page submenu when reading news
if ($_GET['news']) $p = frontpage_value();

// -------- SUBMENU1 FROM LEVEL 2 PAGES ------------------------------------------------------------------------------------------------
$stmt = $dbh->prepare("SELECT pageurl, name FROM extrapage WHERE levelnum = 2 AND mother = ? AND status = 1 ORDER BY ordernum");
$counter = 0;
if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		//if ($counter == 0) $page['submenu1'] = '<div id="subMenu"><h2>'.$page['subheader'].'</h2>'; // <div id="submenu"><h2>Name of the main level page</h2>
		if ($counter == 0) $page['submenu1'] = '<ul>';
		if ($clean_url) $url = $siteroot.'extra/index.php/'.$p.'/'.$row['pageurl'].$url_suffix;
		else $url = 'index.php?p='.$row['pageurl'];

		// EDIT SUBMENU1 HTML HERE:
		$page['submenu1'] .= '<li'.($submenu_selected == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.$url.'">'.$row['name'].'</a></li>';
		//$page['submenu1'] .= '</div>'; // close <div id="submenu">

		$counter++;
	}
}
if ($counter != 0) $page['submenu1'] .= '</ul>';

// ------------------------------------------------------------------------------------------------------------------------------------------

// select correct mother for submenu2
if ($page['levelnum'] == 3) $p = $page['mother'];
else $p = $_GET['p'];


// -------- SUBMENU2 FROM LEVEL 3 PAGES -----------------------------------------------------------------------------------------------
$stmt = $dbh->prepare("SELECT pageurl, name FROM extrapage WHERE levelnum = 3 AND mother = ? AND status = 1 ORDER BY ordernum");
$counter = 0;
if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if ($counter == 0) $page['submenu2'] = '<ul>';
		if ($clean_url) $url = $siteroot.'extra/index.php/'.$page['mother'].'/'.$_GET['p'].'/'.$row['pageurl'].$url_suffix;
		else $url = 'index.php?p='.$row['pageurl'];
		
		// EDIT SUBMENU2 HTML HERE:
		$page['submenu2'] .= '<li'.($_GET['p'] == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.$url.'">'.$row['name'].'</a></li>';
		
		$counter++;
	}
}
if ($counter != 0) $page['submenu2'] .= '</ul>';
// ------------------------------------------------------------------------------------------------------------------------------------------


// -------- DROPDOWN MENU WITH NESTED UL'S --------------------------------------------------------------------------------------------------
/* HTML:
	<ul id="nav">
		<li><a href="#">mainlink</a></li>
		<li><a href="#">mainlink</a>
			<ul>
				<li><a href="#">sublink</a></li>
				<li><a href="#">sublink</a>
					<ul>
						<li><a href="#">subsublink</a></li>
						<li><a href="#">subsublink</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li><a href="#">mainlink</a></li>
	</ul>
*/
$page['dropdownmenu'] = '<ul id="nav">'; // Edit id for navigation div here (id="dropdownMenu" etc.)
$sql = "SELECT pageurl, name FROM extrapage WHERE levelnum = 1 AND status = 1 ORDER BY ordernum";
foreach ($dbh->query($sql) as $row) {
	$page['dropdownmenu'] .= '<li'.($_GET['p'] == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.($clean_url ? clean_url($row['pageurl']) : 'index.php?p='.$row['pageurl']).'">'.$row['name'].'</a>';
	$sql2 = "SELECT pageurl, name FROM extrapage WHERE levelnum = 2 AND mother = '".$row['pageurl']."' AND status = 1 ORDER BY ordernum";
	$level2count = 0;
	foreach ($dbh->query($sql2) as $row2) {
		if ($level2count == 0) $page['dropdownmenu'] .= '<ul>';
		$page['dropdownmenu'] .= '<li><a href="'.($clean_url ? clean_url($row2['pageurl']) : 'index.php?p='.$row2['pageurl']).'">'.$row2['name'].'</a>';
		$sql3 = "SELECT pageurl, name FROM extrapage WHERE levelnum = 3 AND mother = '".$row2['pageurl']."' AND status = 1 ORDER BY ordernum";
		$level3count = 0;
		foreach ($dbh->query($sql3) as $row3) {
			if ($level3count == 0) $page['dropdownmenu'] .= '<ul>';
			$page['dropdownmenu'] .= '<li><a href="'.($clean_url ? clean_url($row3['pageurl']) : 'index.php?p='.$row3['pageurl']).'">'.$row3['name'].'</a></li>';
			$level3count++;
		}
		if ($level3count != 0) $page['dropdownmenu'] .= '</ul>';
		$page['dropdownmenu'] .= '</li>';
		$level2count++;
	}
	if ($level2count != 0) $page['dropdownmenu'] .= '</ul>'; 
	$page['dropdownmenu'] .= '</li>';
}
$page['dropdownmenu'] .= '</ul>';
// ------------------------------------------------------------------------------------------------------------------------------------------


// -------- LATEST NEWS LIST ----------------------------------------------------------------------------------------------------------------
/*
HTML:	<div class="newsItem">
			<h2><a href="index.php?news=60">News title goes here</a></h2>
			<p class="newsDate">04.08.2009</p>
			<p>News text extract goes here, number of characters defined in the Site Setup.</p>
			<p class="newsMore"><a href="index.php?news=60">Read more</a></p>
		</div>            
*/

$sql = "SELECT id, title, text, shorttext, linktopage, STRFTIME('%d.%m.%Y',time) as date FROM news WHERE status = 1 ORDER BY time DESC LIMIT ".$site['news_num'];
foreach ($dbh->query($sql) as $row) {
	// link to regular page if defined, otherwise link to news by id
	if ($row['linktopage'] != "") {
		if ($site['clean_url'] >= 1) {
			$link = clean_url($row['linktopage']);
		} else {
			$link = 'index.php?p='.$row['linktopage'];
		}
		if ($row['shorttext'] != '') $text = $row['shorttext'];
		else $text = snippetstr(strip_tags($row['text']), $site['news_maxshort']);
	} else {
		$link = 'index.php?news='.$row['id'];
		if ($row['shorttext'] != '') $text = $row['shorttext'];
		else $text = snippetstr(strip_tags($row['text']), $site['news_maxshort']);
	}
	// news text with link or without link
	$newstext = ($site['news_textlink'] == 1 ? '<a href="'.$link.'">' : '').$text.($site['news_textlink'] == 1 ? '</a>' : '');

	// --- EDIT NEWS HTML BELOW ---

	// BEGINNING TAG FOR ONE NEWS ITEM:
	$page['news'] .= '<div class="newsItem">';
	
	// NEWS TITLE HTML:
	$page['news'] .= '<h2><a href="'.$link.'">'.$row['title'].'</a></h2>';
	
	// NEWS DATE HTML:
	if ($site['news_showdate'] == 1) $page['news'] .= '<p class="newsDate">'.$row['date'].'</p>';
	
	// NEWS TEXT HTML:
	$page['news'] .= '<p>'.$newstext.'</p>';
	
	if ($site['news_readmore'] == 1) $page['news'] .= 
	
	// NEWS READ MORE -LINK HTML:
	'<p class="newsMore"><a href="'.$link.'">'.$site['news_readmoretext'].'</a></p>';
	
	// CLOSING TAG FOR ONE NEWS ITEM:
	$page['news'] .= '</div>';
}
// ------------------------------------------------------------------------------------------------------------------------------------------
// encode htmlentities in free user input data to prevent XSS injections (exceptions: gacode and extracode)
$page['name'] = ec($page['name']);
if (!$_GET['news']) $page['header1'] = ec($page['header1']);
$page['title'] = ec($page['title']);
$page['description'] = ec($page['description']);
$page['keywords'] = ec($page['keywords']);
$page['image1'] = ec($page['image1']);
$page['image2'] = ec($page['image2']);
$page['subheader'] = ec($page['subheader']);
?>
<?php

// -------- SCROLL DOWN TO EDIT HTML FOR DIFFERENT PAGE PARTS (MENUS, NEWS, ETC...) -----------------------------------------------------------
// -------- THESE PARTS ARE SEPARATED BY LINES ------------------------------------------------------------------------------------------------


if (basename($_SERVER['REQUEST_URI']) == 'page.php') die ('Access denied');
require('includes/dbconfig.php');
require('includes/doctypes.php');
require('includes/commonfunc.php');
$dbh = new PDO(PDO_DB_DRIVER.':'.RUUBIKCMS_FOLDER.'/'.PDO_DB_FOLDER.'/'.PDO_DB_NAME); // database connection object

$page = array();
$site = array();
$site = get_site_data();
$siteroot = '/'.($site['siteroot'] != "" ? trim($site['siteroot'],'/').'/' : '');
if ($site['clean_url'] >= 1) {
    
	$array = explode('/',$_SERVER['REQUEST_URI']);
                   //print_r($array);
	$pagearr = explode('.', end($array));
	$pagearr2 = explode('?', $pagearr[0]);
	$_GET['p'] = $pagearr2[0];
	if (strpos($_GET['p'], 'index') !== false )  $_GET['p'] = ""; // uri index.php without trailing slash
	if (count($array) >= 3) $mainmenu = $array[1];
	if (count($array) == 4) $submenu = $array[2];
        
                //print_r($array);
                //print_r($pagearr);
                //print_r($pagearr2);
	$clean_url = TRUE;
} else {
	$clean_url = FALSE;
}
$submenu_selected = $_GET['p'];

if ($site['url_suffix'] != '') $url_suffix = '.'.trim($site['url_suffix'], '.');
else $url_suffix = '';

// if no page defined -> get the first page
//if (!$_GET['p'] AND !$_GET['news']) {
//Removed because unusable

if (!$_GET['p']) {
	$_GET['p'] = frontpage_value();
}

/*if ($_GET['news']) {
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
	
} else*/
//Removed because unusable


if ($_GET['p']) {
	// get published pagedata for normal page content)
	$page = get_page_data($_GET['p'], TRUE);
	// subheader, todo: only needed when using subheader
	if ($page['levelnum'] == 1) $page['subheader'] = $page['name'];
	elseif ($page['levelnum'] == 2) $page['subheader'] = query_single("SELECT name FROM page WHERE pageurl = '".$page['mother']."'");
	elseif ($page['levelnum'] == 3) $page['subheader'] = query_single("SELECT name FROM page WHERE pageurl = '".query_single("SELECT mother FROM page WHERE pageurl ='".$page['mother']."'")."'");
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
	if ($site['no_image1'] == 1) $page['image1'] = frontpage_value('image1');
	// otherwise use transparent 1px dummy gif
	else $page['image1'] = $siteroot.RUUBIKCMS_FOLDER.'/includes/empty.gif';
}

// check no image2
if (!$page['image2'] OR $page['image2'] == "") {
	// use front page image2 if this option is selected
	if ($site['no_image2'] == 1) $page['image2'] = frontpage_value('image2');
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
	$p = query_single("SELECT mother FROM page WHERE pageurl = '".$page['mother']."'"); // a.k.a grandmother
	$submenu_selected = $page['mother'];
} else {
	$p = $_GET['p'];
}

// has page sub pages?
if ($page['levelnum'] < 3) {
	if (query_single("SELECT name FROM page WHERE mother = '".$page['pageurl']."'") == null) $page['has_sub_pages'] = FALSE;
	else $page['has_sub_pages'] = TRUE;
}

// -------- SLIDE SUBMENU WITH NESTED UL'S -------------------------------------------------------------------------------------------------
$page['submenuslide'] = '<ul id="treemenu" class="treeview">';
$stmt = $dbh->prepare("SELECT pageurl, name FROM page WHERE levelnum = 2 AND mother = ? AND status = 1 ORDER BY ordernum");
if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$page['submenuslide'] .= '<li><a href="'.($clean_url ? clean_url($row['pageurl']) : 'index.php?p='.$row['pageurl']).'">'.$row['name'].'</a>';
		$sql2 = "SELECT pageurl, name FROM page WHERE levelnum = 3 AND mother = '".$row['pageurl']."' AND status = 1 ORDER BY ordernum";
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
$sql = "SELECT pageurl, name FROM page WHERE levelnum = 1 AND status = 1 ORDER BY ordernum";
$counter = 0;
foreach ($dbh->query($sql) as $row) {
	if ($counter == 0) $page['mainmenu'] = '<ul>';
	if ($clean_url) $url = $siteroot.'index.php/'.$row['pageurl'].$url_suffix;
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
$page['mainmenu'] .= '<li'.($p == $row['pageurl']  ? ' class="selected"' : '').'></li>
                      <li><div><a href="#">Dzisiaj jest '.ucwords(strftime('%A, %d %B %G')).' <span class="clock"></span></a></div></li>';
if ($counter != 0) $page['mainmenu'] .= '</ul>';

// ------------------------------------------------------------------------------------------------------------------------------------------

// front page submenu when reading news
//if ($_GET['news']) $p = frontpage_value();
//Removed because in my implement is unusable



// -------- SUBMENU1 FROM LEVEL 2 PAGES ------------------------------------------------------------------------------------------------
$stmt = $dbh->prepare("SELECT pageurl, name FROM page WHERE levelnum = 2 AND mother = ? AND status = 1 ORDER BY ordernum");
$counter = 0;

if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		//if ($counter == 0) $page['submenu1'] = '<div id="subMenu"><h2>'.$page['subheader'].'</h2>'; // <div id="submenu"><h2>Name of the main level page</h2>
		if ($counter == 0) $page['submenu1'] = '<ul>';
		if ($clean_url) $url = $siteroot.'index.php/'.$p.'/'.$row['pageurl'].$url_suffix;
		else $url = 'index.php?p='.$row['pageurl'];
		
		// EDIT SUBMENU1 HTML HERE:
		$page['submenu1'] .= '<li'.($submenu_selected == $row['pageurl']  ? ' class="selected"' : '').'><a href="'.$url.'"><div>'.$row['name'].'</div></a></li>';
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
$stmt = $dbh->prepare("SELECT pageurl, name FROM page WHERE levelnum = 3 AND mother = ? AND status = 1 ORDER BY ordernum");
$counter = 0;
if ($stmt->execute(array($p))) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if ($counter == 0) $page['submenu2'] = '<ul>';
		if ($clean_url) $url = $siteroot.'index.php/'.$page['mother'].'/'.$_GET['p'].'/'.$row['pageurl'].$url_suffix;
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
$sql = "SELECT pageurl, name FROM page WHERE levelnum = 1 AND status = 1 ORDER BY ordernum";
foreach ($dbh->query($sql) as $row) {
	$page['dropdownmenu'] .= '<li'.
                                ($_GET['p'] == $row['pageurl']  ? ' class="selected"' : '').
                                '><span><a href="'.($clean_url ? clean_url($row['pageurl']) : 'index.php?p='.$row['pageurl']).'">'.
                                $row['name'].'</a></span>';
	
        
        $sql2 = "SELECT pageurl, name FROM page WHERE levelnum = 2 AND mother = '".$row['pageurl']."' AND status = 1 ORDER BY ordernum";
	$level2count = 0;
	foreach ($dbh->query($sql2) as $row2) {
		if ($level2count == 0) $page['dropdownmenu'] .= '<ul id=lvl2>';
		$page['dropdownmenu'] .= '<li'.($_GET['p'] == $row2['pageurl']  ? ' class="selected"' : '').'><a href="'.($clean_url ? clean_url($row2['pageurl']) : 'index.php?p='.$row2['pageurl']).'">'.$row2['name'].'</a>';
		$sql3 = "SELECT pageurl, name FROM page WHERE levelnum = 3 AND mother = '".$row2['pageurl']."' AND status = 1 ORDER BY ordernum";
		$level3count = 0;
		foreach ($dbh->query($sql3) as $row3) {
			if ($level3count == 0) $page['dropdownmenu'] .= '<ul id="lvl3">';
			$page['dropdownmenu'] .= '<li'.($_GET['p'] == $row3['pageurl']  ? ' class="selected"' : '').'><a href="'.($clean_url ? clean_url($row3['pageurl']) : 'index.php?p='.$row3['pageurl']).'">'.$row3['name'].'</a></li>';
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

// -------- BREADCRUMP PATH FOR NAVIGATION --------------------------------------------------------------------------------------------------
/* 
SHOWS CURRENT LOCATION: Products > Tools > Hammer
*/

if ($page['levelnum'] == 1) {
	$page['breadcrump'] = $page['name'];
} elseif ($page['levelnum'] == 2) {
	$page['breadcrump'] = '<a href="'.($clean_url ? clean_url($page['mother']) : 'index.php?p='.$page['mother']).'">'.page_name($page['mother']).'</a>';
	$page['breadcrump'] .= ' > '.$page['name'];
} elseif ($page['levelnum'] == 3) {
	$grandma = query_single("SELECT mother FROM page WHERE pageurl = '".$page['mother']."'");
	$page['breadcrump'] = '<a href="'.($clean_url ? clean_url($grandma) : 'index.php?p='.$grandma).'">'.page_name($grandma).'</a>';
	$page['breadcrump'] .= ' > <a href="'.($clean_url ? clean_url($page['mother']) : 'index.php?p='.$page['mother']).'">'.page_name($page['mother']).'</a>';
	$page['breadcrump'] .= ' > '.$page['name'];
}

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

/*
$sql = "SELECT id, title, text, shorttext, linktopage, STRFTIME('%d.%m.%Y',time) as date FROM news WHERE status = 1 ORDER BY time DESC LIMIT ".$site['news_num'];
foreach ($dbh->query($sql) as $row) {
	// link to regular page if defined, otherwise link to news by id
	if ($row['linktopage'] != "") {
		if ($site['clean_url'] >= 1) {
			$link = clean_url($row['linktopage']);
		} else {
			$link = 'index.php?p='.$row['linktopage'];
		}
		if ($row['shorttext'] != '') $text = ec($row['shorttext']);
		else $text = snippetstr(strip_tags($row['text']), $site['news_maxshort']);
	} else {
		$link = 'index.php?news='.$row['id'];
		if ($row['shorttext'] != '') $text = ec($row['shorttext']);
		else $text = snippetstr(strip_tags($row['text']), $site['news_maxshort']);
	}
	// news text with link or without link
	$newstext = ($site['news_textlink'] == 1 ? '<a href="'.$link.'">' : '').$text.($site['news_textlink'] == 1 ? '</a>' : '');

	// --- EDIT NEWS HTML BELOW ---
    
	// BEGINNING TAG FOR ONE NEWS ITEM:
	$page['news'] .= '<div class="newsItem">';
	
	// NEWS TITLE HTML:
	//$page['news'] .= '<h2><a href="'.$link.'">'.ec($row['title']).'</a></h2>';
	$page['news'] .= '<h2><a href="'.$link.'">'.($row['title']).'</a></h2>';
	// NEWS DATE HTML:
	if ($site['news_showdate'] == 1) $page['news'] .= '<p class="newsDate">'.$row['date'].'</p>';
	
	// NEWS TEXT HTML:
	$page['news'] .= '<p>'.$newstext.'</p>';
	
	if ($site['news_readmore'] == 1) $page['news'] .= 
	
	// NEWS READ MORE -LINK HTML:
	'<p class="newsMore"><a href="'.$link.'">'.$site['news_readmoretext'].'</a></p>';
	
	// CLOSING TAG FOR ONE NEWS ITEM:
	$page['news'] .= '</div>';
} */
// ------------------------------------------------------------------------------------------------------------------------------------------

// encode htmlentities in free user input data to prevent XSS injections (exceptions: gacode and extracode)
$page['name'] = ec($page['name']);
//if (!$_GET['news']) $page['header1'] = ec($page['header1']);
$page['title'] = ec($page['title']);
$page['description'] = ec($page['description']);
$page['keywords'] = ec($page['keywords']);
$page['image1'] = ec($page['image1']);
$page['image2'] = ec($page['image2']);
$page['subheader'] = ec($page['subheader']);



function isMobile() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
        return true;
    }
    else {
        return false;
    }
}
?>

<?php
/*   RuubikCMS - The easy & fast way to manage Google optimized websites
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
require('includes/required.php');
$_SESSION['extra'] = TRUE;
$cmspage = EXTRANET;
$page = array();
$site = get_site_data();
$siteroot = trim($site['siteroot'],'/');
$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['save'])) {

	// some CSRF protection
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);

	// check write permissions
	if(!is_writable('../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME) OR !is_writable('../'.PDO_DB_FOLDER)) {
		$error = SQLITENOTWRITABLE;
	}
	
	// create new page if save clicked from $_GET['p']?p=---notinmenu---
	if (isset($_GET['p'])) {
		if ($_GET['p'] == '---notinmenu---') {
			unset($_GET['p']);
			$_GET['n'] = '1';
		}
	}
	// create new page if save clicked without $_GET['p']
	if (!isset($_GET['p'])) {
		unset($_GET['p']);
		$_GET['n'] = '1';
	}

	// store unstripped extracode
	$extracode_raw = $_POST['extracode'];

	// remove slashes from html
	$content = stripslashes($_POST['tinyMCE']);
	
	// strip some more slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}
	
 	// convert index.php?p=pageurl links to clean url links
	if ($site['clean_url'] >= 1) {
		$content = preg_replace('/index.php\?p\=(.+)"/eUs', "clean_url('$1').'\"'", $content);
	}
	
	// convert extra/userupload filelink tags to protected downloads via download.php script
	$content = preg_replace('#a href\="([^"]*/extra/useruploads/files/[^\?]+)"#eUs', "a.' href=\"/".($siteroot != "" ? $siteroot.'/' : '')."extra/download.php?f='.basename('$1').'\"'", $content); 
	
	// convert extra/userupload img tags to protected images via image.php script
	$content = preg_replace('#img src\="([^"]*/extra/useruploads/images/[^\?]+)"#eUs', "img.' src=\"/".($siteroot != "" ? $siteroot.'/' : '')."extra/image.php?f='.basename('$1').'\"'", $content); 

	// at least some name must be defined
	if (!isset($_POST['name'])) $_POST['name'] = NONAME;
	
	// save creator if this is new page
	if ($_POST['creator'] == "") $_POST['creator'] = $_SESSION['uid'];
	
	// check rights to save
	if ($_SESSION['level'] < 4 AND $_POST['creator'] != $_SESSION['uid']) $error = NOTALLOWED;
	
	// values not submitted if disabled (has children)
	if ($_POST['status'] == NULL) $_POST['status'] = '1';
	if ($_POST['mother'] === NULL) $_POST['mother'] = $_POST['mother_hidden'];
		
	// get level number
	if ($_POST['mother'] == '---notinmenu---') {
		$levelnum = 0;
	} elseif ($_POST['mother'] AND $_POST['mother'] != '') {
		$levelnum = query_prep("SELECT levelnum + 1 FROM extrapage WHERE pageurl = ?", array($_POST['mother']));
	} else {
		$levelnum = 1;
	}
		
	// new page -> get unique name
	if (!isset($_GET['p']) AND isset($_GET['n'])) {
		//echo $_POST['pageurl'];
		if ($_POST['pageurl']) $newpageurl = get_unique_url($_POST['pageurl'], 4); // make pageurl from URL if given
		else $newpageurl = get_unique_url($_POST['name'], 4); // otherwise make pageurl from name (function returns "no-name" if not $_POST['name'])
		
		$_POST['ordernum'] = get_next_ordernum($_POST['mother'], 'extrapage');
	} 
	
	// page already exists
	if (isset($_GET['p'])) {
	
		$oldlevel = query_prep("SELECT levelnum FROM extrapage WHERE pageurl = ?", array($_GET['p']));
		$oldmother = query_prep("SELECT mother FROM extrapage WHERE pageurl = ?", array($_GET['p']));
		
		// check if mother has changed
		if ($_POST['mother'] != $oldmother) {
			$motherchanged = TRUE;
			$_POST['ordernum'] = get_next_ordernum($_POST['mother'], 'extrapage');
			
			// select disabled (check if children and levelnum changed, TODO: make possible instead of disabled/error)
			/*if (has_children($_GET['p'], 'extrapage') AND $levelnum != $oldlevel) {
				$error = "HAS CHILDREN, NOT MOVED";
				save_infomsg($error);
			}*/
		}

		// check if pageurl changed
		if ($_POST['pageurl'] != $_GET['p']) {
			if (!$error) {
				// delete old before inserting new
				$stmt = $dbh->prepare("DELETE FROM extrapage WHERE pageurl = ?");
				$stmt->bindParam(1, $_GET['p']);
				$stmt->execute();
				if ($_POST['pageurl']) $newpageurl = get_unique_url($_POST['pageurl'], 4); // make pageurl from URL if given
				else $newpageurl = get_unique_url($_POST['name'], 4); // otherwise make pageurl from name (function returns "no-name" if not $_POST['name'])
				$pageurlchanged = TRUE;
				// also change mother for child pages
				$stmt = $dbh->prepare("UPDATE extrapage SET mother = '$newpageurl' WHERE mother = ?");
				$stmt->bindParam(1, $_GET['p']);
				$stmt->execute();
			}
		} else {
		// pageurl not changed
			$newpageurl = ec($_GET['p']);
			$pageurlchanged = FALSE;
		}
	}
	
	// new page and no header1 -> use name
	if (isset($_GET['n']) AND !isset($_POST['header1'])) $_POST['header1'] = $_POST['name'];
	
	if (!isset($error)) {
		
		$date = date("Y-m-d H:i:s");
		$stmt = $dbh->prepare("INSERT OR REPLACE INTO extrapage (pageurl, name, title, header1, content, levelnum, ordernum, description, keywords, mother, image1, image2, pagetype, extracode, status, updater, updated, creator) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $newpageurl);
		$stmt->bindParam(2, $_POST['name']);
		$stmt->bindParam(3, $_POST['title']);
		$stmt->bindParam(4, $_POST['header1']);
		$stmt->bindParam(5, $content);
		$stmt->bindParam(6, $levelnum);
		$stmt->bindParam(7, $_POST['ordernum']);
		$stmt->bindParam(8, $_POST['description']);
		$stmt->bindParam(9, $_POST['keywords']);
		$stmt->bindParam(10, $_POST['mother']);
		$stmt->bindParam(11, $_POST['picfile1']);
		$stmt->bindParam(12, $_POST['picfile2']);
		$stmt->bindParam(13, $_POST['pagetype']);
		$stmt->bindParam(14, $extracode_raw);
		$stmt->bindParam(15, $_POST['status']);
		$stmt->bindParam(16, $_SESSION['uid']);
		$stmt->bindParam(17, $date);
		$stmt->bindParam(18, $_POST['creator']);
		$stmt->execute();
				
		if (isset($motherchanged)) refresh_pageorder($oldmother, 'extrapage');
		
		if (isset($_GET['n']) OR $pageurlchanged) {
			save_infomsg(PAGE.' '.CREATED);
			header('Location: '.$self.'?p='.$newpageurl); // redirect to new page
			exit;
		} else {
			save_infomsg(PAGE.' '.SAVED);
		}
	}
}

// get pages up to level 2 as array for level select
$pagelist = pages_for_select(2, 'extrapage');

if (isset($_GET['p'])) {

	// get pagedata from database
	$page = get_page_data($_GET['p'], FALSE, 'extrapage');
	if ($site['clean_url'] >= 1) {
		$pagelink = clean_url(ec($_GET['p']));
	} else {
		$pagelink = '/'.($siteroot != "" ? $siteroot.'/' : '').'ekstra/index.php?p='.ec($_GET['p']);
	}
	
	if (has_children($_GET['p'], 'extrapage')) $children = TRUE;
	else $children = FALSE;

	if (isset($_GET['d'])) {
		// some CSRF protection
		if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
	
		// delete page if no children
		if ($children) {
			save_infomsg('HAS CHILDREN, NOT DELETED');
			header('Location: '.$self);
			exit;
		} else {
			if ($_SESSION['level'] >= 4 OR $page['creator'] == $_SESSION['uid']) {
				// no children -> delete
				$stmt = $dbh->prepare("DELETE FROM extrapage WHERE pageurl = ?");
				$stmt->bindParam(1, $_GET['p']);
				$stmt->execute();
				refresh_pageorder($page['mother'], 'extrapage');
				save_infomsg(PAGE.' '.DELETED);
				if ($page['levelnum'] == 1) $redirect = '';
				// redirect to first subpage with same mother after delete
				else $redirect = '?p='.query_single("SELECT pageurl FROM extrapage WHERE levelnum = ".$page['levelnum']." AND mother = '".$page['mother']."' ORDER BY levelnum LIMIT 1");
				header('Location: '.$self.$redirect);
				exit;
			}
		}
	} elseif (isset($_GET['moveup'])) {
		// some CSRF protection
		if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
	
		if ($_SESSION['level'] >= 4) {
			if ($page['ordernum'] == 1) {
				header('Location: '.$self.'?p='.ec($_GET['p'])); // already first page, just redirect to this page
			} else {
				// update sister pages with current ordernum - 1 to this ordernum
				$stmt = $dbh->prepare("UPDATE extrapage SET ordernum = ? WHERE ordernum = ? - 1 AND mother = ?");
				$stmt->bindParam(1, $page['ordernum']);
				$stmt->bindParam(2, $page['ordernum']);
				$stmt->bindParam(3, $page['mother']);
				$stmt->execute();			
				// move current page up by one number
				$stmt = $dbh->prepare("UPDATE extrapage SET ordernum = ? - 1 WHERE pageurl = ?");
				$stmt->bindParam(1, $page['ordernum']);
				$stmt->bindParam(2, $_GET['p']);
				$stmt->execute();
				// redirect to this page without '&moveup=1'
				header('Location: '.$self.'?p='.ec($_GET['p']));
				exit;
			}
		}
	}
	
} elseif (!isset($_GET['n'])) {
	// no page defined -> redirect to first page
	$pageurl = query_single("SELECT pageurl FROM extrapage WHERE ordernum = 1 AND mother = ''");
	if ($pageurl) {
		header('Location: '.$self.'?p='.$pageurl);
		exit;
	}
}

require('includes/head.php');
if (!isset($page['status'])) $page['status'] = 1;
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/extrapagemenu.php');?>

                        
			<!-- **************** RightDiv ******************** -->    
				<form method="post" action="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']);?>" name="pageEditForm">
				<input type="hidden" name="save" value="1" />
				<input type="hidden" name="ordernum" value="<?php echo $page['ordernum'];?>" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<?php if ($_SESSION['level'] > 3 OR $page['creator'] == "" OR $page['creator'] == $_SESSION['uid']) {?><li><a href="javascript:document.pageEditForm.submit();" class="save" onclick="return validate(document.forms[0]);"><span><?php echo SAVE;?></span></a></li><?php }?>
							<li><a href="<?php echo $self.'?n=1';?>" class="new"><span><?php echo RNEW;?></span></a></li>
							<?php if (isset($_GET['p']) AND ($_SESSION['level'] > 3 OR $page['creator'] == "" OR $page['creator'] == $_SESSION['uid'])) {?><li><a href="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']).'&amp;d=1&amp;token='.$token;?>" class="delete" onclick="<?php if ($children) echo "alert('".NODELETECHILDREN."');return false"; else echo "return confirm('".DELETEPAGECONFIRM."');";?>"><span><?php echo DELETE;?></span></a></li><?php }?>
						</ul>

						<div id="currentPage"><?php if (isset($page['name'])) echo SELECTEDPAGE.': <b id="selectedPage">'.ec($page['name']).'</b>';?></div>
           				
					</div>
                            
					<div id="rightContent">
					
						<div id="pageAdmin">
							<h2><?php echo PAGEMANAGEMENT;?></h2>
							<table cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td class="tdPageAdminLeft"><?php echo PAGENAME;?></td>
									<td class="tdPageAdminCenter"><input type="text" name="name" id="name" value="<?php if (isset($page['name'])) echo ec($page['name']);?>" /></td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_TITLE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									<input name="token" type="hidden" value="<?php echo $token;?>" />
								</tr>
								<tr>
									<td class="tdPageAdminLeft"><?php echo HEADER1;?></td>
									<td class="tdPageAdminCenter"><input type="text" name="header1" value="<?php if (isset($page['header1'])) echo ec($page['header1']);?>" /></td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_HEADER1;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdPageAdminLeft"><?php echo PAGEURL;?></td>
									<td class="tdPageAdminCenter"><input type="text" name="pageurl" value="<?php if (isset($_GET['p'])) echo ec($_GET['p']);?>" /></td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_PAGEURL;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdPageAdminLeft"><?php echo PAGELINK;?></td>
									<td class="tdPageAdminCenter"><input type="text" readonly="readonly" name="pagelink" value="<?php if (isset($pagelink)) echo $pagelink;?>" /></td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_PAGELINK;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdPageAdminLeft"><?php echo LEVEL;?></td>
									<td class="tdPageAdminCenter">
										<select name="mother"<?php if ($children) echo ' disabled="disabled"';?>>
											<option value="">---<?php echo MAINLEVEL;?>---</option>
											<option value="---notinmenu---"<?php if (isset($page['mother']) AND $page['mother'] == '---notinmenu---') echo ' selected="selected"';?>>---<?php echo FREEPAGE;?>---</option>
											<?php foreach($pagelist as $key => $value) {
												if ($key != $_GET['p']) echo '<option value="'.$key.'"'.($key == $page['mother'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											}?>
										</select>
									</td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_LEVEL;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<?php if ($_SESSION['level'] > 3 OR ($_SESSION['level'] == 3 AND ($page['creator'] == $_SESSION['uid'] OR $page['creator'] == ""))) { // check publishing rights ?>
								<tr>
									<td class="tdPageAdminLeft"><?php echo STATUS;?></td>
									<td class="tdPageAdminCenter">
										<select name="status"<?php if ($children) echo ' disabled="disabled"';?>>
											<option value="1"<?php if($page['status'] == 1 OR !isset($_GET['p'])) echo 'selected="selected"';?>><?php echo PUBLISHED;?></option>
											<option value="0"<?php if($page['status'] == 0 AND isset($_GET['p'])) echo 'selected="selected"';?>><?php echo DRAFT;?></option>
										</select>
									</td>
									<td class="tdPageAdminRight"><a href="#" class="tooltip" title="<?php echo H_PAGESTATUS;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<?php } else { // not allowed to publish ?>
								<input type="hidden" name="status" value="0"/>
								<?php }?>
								<?php // pagetype removed here from v1.1.2 ?>
							</table>
							
							<?php if ($children) echo '<input name="mother_hidden" type="hidden" value="'.$page['mother'].'" />'; // disabled select does not post value?>
							<input name="creator" type="hidden" value="<?php if (isset($page['creator'])) echo $page['creator'];?>" />
																				
						</div>
						
						<div id="SEO">
							<h2><?php echo SEO;?></h2>
							<table cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td class="tdSEOAdminLeft"><?php echo PAGETITLE;?></td>
									<td class="tdSEOAdminCenter"><input type="text" name="title" value="<?php if (isset($page['title'])) echo ec($page['title']);?>" /></td>
									<td class="tdSEOAdminRight"><a href="#" class="tooltip" title="<?php echo H_PAGETITLE.H_DEFAULTIFEMPTY;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdSEOAdminLeft"><?php echo DESCRIPTION;?></td>
									<td class="tdSEOAdminCenter"><textarea cols="30" rows="2" name="description"><?php if (isset($page['description'])) echo ec($page['description']);?></textarea></td>
									<td class="tdSEOAdminRight"><a href="#" class="tooltip" title="<?php echo H_DESCRIPTION.H_DEFAULTIFEMPTY;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdSEOAdminLeft"><?php echo KEYWORDS;?></td>
									<td class="tdSEOAdminCenter"><textarea cols="30" rows="2" name="keywords"><?php if (isset($page['keywords'])) echo ec($page['keywords']);?></textarea></td>
									<td class="tdSEOAdminRight"><a href="#" class="tooltip" title="<?php echo H_KEYWORDS.H_DEFAULTIFEMPTY;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
							</table>

						</div>
						<div class="clear"></div> <!-- This div clears the float divs -->
					
						<div id="toggleButtons">
							<span class="toggleSpan"><a href="extrausers.php" class="toggleExtrausers"><?php echo EXTRAUSERS;?></a></span>
							<span class="toggleSpan"><a href="#" class="toggleImages"><?php echo IMAGES;?> / Flash</a></span>
							<span class="toggleSpan"><a href="#" id="toggleExtracode"><?php echo EXTRACODE;?></a></span>
						</div>
					
						<div id="xtrArea">
							<h2><?php echo EXTRACODE;?></h2>
							<div id="xtrAreaBlue"><a href="#" class="tooltip" style="float: right; margin-bottom:5px;" title="<?php echo H_EXTRACODE;?>"><img src="images/help.gif" class="imgover" alt="" /></a>
							<textarea name="extracode" cols="2" rows="2"><?php if (isset($page['extracode'])) echo stripslashes_gpc(htmlentities($page['extracode'], $ent=ENT_COMPAT, $site['charset']));?></textarea></div>
						</div>
												
						

						
						<div id="decorationPix">

						<h2><?php echo IMAGES;?></h2>

						<div id="picsDiv">
                    
							<table class="decorationTable" cellpadding="0" cellspacing="0" border="0">                       
								<tbody>
                                    <tr>
                                        <td><h3><?php echo IMAGE;?> 1</h3></td>
										<td><h3><?php echo IMAGE;?> 2</h3></td>
                                        <!--<td><h3><?php echo IMAGE;?> 3 (available in RuubikCMS v1.0.3)</h3></td>-->
                                        <td class="DecoPicToolTip"><a href="#" class="tooltip" title="<?php echo H_IMAGE;?>"><img src="images/help.gif" class="imgover" title="" alt="" /></a></td>
                                    </tr>

                                    <tr>
                                        <td>
											<?php if (!empty($page['image1'])) echo '<img id="pic1img" src="'.ec($page['image1']).'" alt="'.NOPREVIEW.'" height="84" width="134" />';?>
										</td>
                                        <td>
											<?php if (!empty($page['image2'])) echo '<img id="pic2img" src="'.ec($page['image2']).'" alt="'.NOPREVIEW.'" height="84" width="134" />';?>
										</td>
                                        <td class="DecoPicToolTip">&nbsp;</td>
                                    </tr>

									<tr>
									
										<td>
                                        	<div><a href="#" class="addPic" onclick="javascript:tinyBrowserPopUp('image','pic1');">
											<b><?php echo IMAGE;?></b> (jpg, gif, png)</a></div>
											<div><a href="#" class="flash" onclick="javascript:tinyBrowserPopUp('media','pic1');">
											<b>Flash</b> (flv, swf)</a></div>
											<div><a href="#" class="delete" id="delpic1"><b><?php echo REMOVE;?></b></a></div>
											<div><input name="picfile1" type="text" id="pic1" value="<?php if (isset($page['image1'])) echo ec($page['image1']);?>" /></div>
										</td>
										
										<td>
                                        	<div><a href="#" class="addPic" onclick="javascript:tinyBrowserPopUp('image','pic2');">
											<b><?php echo IMAGE;?></b> (jpg, gif, png)</a></div>
											<div><a href="#" class="flash" onclick="javascript:tinyBrowserPopUp('media','pic2');">
											<b>Flash</b> (flv, swf)</a></div>
											<div><a href="#" class="delete" id="delpic2"><b><?php echo REMOVE;?></b></a></div>
											<div><input name="picfile2" type="text" id="pic2" value="<?php if (isset($page['image2'])) echo ec($page['image2']);?>" /></div>
										</td>

										<!--<td>
                                        	<div><a href="#" class="addPic" onclick="javascript:tinyBrowserPopUp('image','pic2');">
											<b>Picture</b> (jpg, gif)</a></div>
											<div><a href="#" class="flash" onclick="javascript:tinyBrowserPopUp('media','pic2');">
											<b>Flash</b> (swf, flv)</a></div>
											<div><a href="#" class="delete" id="delpic2"><b>Delete</b> media</a></div>
										</td>-->

										<!-- Buttons for img3 here -->

										<td class="DecoPicToolTip">&nbsp;</td>
                                    </tr>
                                </tbody>
                            </table>


                            </div>

						</div>

					</div>


					<div id="contentManagement">
						<div id="tinyMCE">
							<textarea cols="63" rows="20" name="tinyMCE" class="tinyMCE" id="tinyMCEarea"><?php if (isset($page['content'])) echo htmlentities($page['content'], $ent=ENT_COMPAT, $site['charset']);?></textarea>
						</div>

                        <div class="clear"></div> <!-- This div clears the float divs --> 					

					<p class="updated">
					<?php
						if (isset($page['creator'])) echo CREATOR.': '.$page['creator'];
						if (isset($page['updated'])) echo ' | '.UPDATED.' '.$page['updated'].' ('.$page['updater'].')';
					?>
					</p>

					</div>

                </div>

				</form>

				<div class="clear"></div> <!-- This div clears the float divs --> 

            </div>

<?php require('includes/footer.php');?>

<?php
if (isset($error) AND $error == SQLITENOTWRITABLE) {
	echo '<script language="javascript" type="text/javascript">alert(\''.$error.'\');</script>';
}
?>
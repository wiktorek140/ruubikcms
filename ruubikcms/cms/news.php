<?php
/*   RuubikCMS - The easy & fast way to manage Google optimized websites
 *   Copyright (C) 2008-2010 Iisakki Piril�, Henrik Valros
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
if ($_SESSION['level'] != 5) die(NOTALLOWED);
$cmspage = NEWS;
$news = array();
$self = ec($_SERVER['PHP_SELF']);

// get all webpages (up to level 3) for link to page select
$pagelist = pages_for_select(3, 'page', TRUE);

if (isset($_POST['save'])) {
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	// strip slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}
	$site = get_site_data();
	
	if (!valid_mysql_date($_POST['time'])) $_POST['time'] = date("Y-m-d");
	$_POST['time'] .= date(" H:i:s");
	
	// save creator if this is new page
	if ($_POST['creator'] == "") $_POST['creator'] = $_SESSION['uid'];
	
	// check rights to save
	if ($_SESSION['level'] < 4 AND $_POST['creator'] != $_SESSION['uid']) die(NOTALLOWED);

	$newsdata = stripslashes($_POST['tinyMCE']);
	$shorttextdata = substr($_POST['shorttext'], 0, $site['news_maxshort']);
	
	// save newsdata in database
	if ($_GET['id']) {
		$date = date("Y-m-d H:i:s");
		
		// update existing news
		$stmt = $dbh->prepare("UPDATE news SET title = ?, time = ?, text = ?, shorttext = ?, linktopage = ?, status = ?, updated = ?, updater = ? WHERE id = ?");
		$stmt->bindParam(1, $_POST['title']);
		$stmt->bindParam(2, $_POST['time']);
		$stmt->bindParam(3, $newsdata);
		$stmt->bindParam(4, $shorttextdata);
		$stmt->bindParam(5, $_POST['linktopage']);
		$stmt->bindParam(6, $_POST['status']);
		$stmt->bindParam(7, $date);
		$stmt->bindParam(8, $_SESSION['uid']);
		$stmt->bindParam(9, $_GET['id']);
		$stmt->execute();
	} else {
		// insert new news
		$stmt = $dbh->prepare("INSERT INTO news (title, time, text, shorttext, linktopage, status, creator) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->bindParam(1, $_POST['title']);
		$stmt->bindParam(2, $_POST['time']);
		$stmt->bindParam(3, $newsdata);
		$stmt->bindParam(4, $shorttextdata);
		$stmt->bindParam(5, $_POST['linktopage']);
		$stmt->bindParam(6, $_POST['status']);
		$stmt->bindParam(7, $_SESSION['uid']);
		$stmt->execute();
		header('Location: '.$self.'?id='.$dbh->lastInsertId().'&y='.substr($_POST['time'],0,4)); // redirect to new news
	}
	save_infomsg(SINGLENEWS.' '.SAVED);
}

if (query_single("SELECT COUNT(*) FROM news") != 0) {

	if (isset($_GET['id'])) {
	
		// get news data from database
		$stmt = $dbh->prepare("SELECT * FROM news WHERE id = ?");
		if ($stmt->execute(array($_GET['id']))) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$news = $result[0];
		}
		
		if (isset($_GET['d'])) {
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
			if ($_SESSION['level'] >= 4 OR $news['creator'] == $_SESSION['uid']) {
				// delete requested news
				$stmt = $dbh->prepare("DELETE FROM news WHERE id = ?");
				$stmt->bindParam(1, $_GET['id']);
				$stmt->execute();
				save_infomsg(SINGLENEWS.' '.DELETED);
			}
			header('Location: '.$self);
		}

	} elseif (!isset($_GET['n']) AND !isset($_GET['y'])) {
		// no id given and not creating new page etc ->  redirect to last news by date
		header('Location: '.$self.'?y='.query_single("SELECT STRFTIME('%Y', time) FROM news ORDER BY time DESC LIMIT 1")."&id=".query_single("SELECT id FROM news ORDER BY time DESC LIMIT 1"));
	}
}

require('includes/head.php');
if (!isset($news['status'])) $news['status'] = 1;
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/newsmenu.php');?>


			<!-- **************** RightDiv ******************** -->    
				<form method="post" action="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']);?>" name="newsEditForm">
				<input type="hidden" name="save" value="1" />
				<input type="hidden" name="ordernum" value="<?php echo $page['ordernum'];?>" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<?php if (isset($_GET['id']) OR isset($_GET['n']) AND ($_SESSION['level'] > 3 OR $news['creator'] == "" OR $news['creator'] == $_SESSION['uid'])) {?><li><a href="javascript:document.newsEditForm.submit();" class="save"><span><?php echo SAVE;?></span></a></li><?php }?>
							<li><a href="<?php echo $self.'?n=1';?>" class="new"><span><?php echo RNEW;?></span></a></li>
							<?php if (isset($_GET['id']) AND ($_SESSION['level'] > 3 OR $news['creator'] == "" OR $news['creator'] == $_SESSION['uid'])) {?><li><a href="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']).'&amp;d=1&amp;token='.$token;?>" class="delete"><span><?php echo DELETE;?></span></a></li><?php }?>
						</ul>
					</div>

					<div id="rightContent">					

						<div id="newsAdmin">

							<h2><?php echo NEWS;?></h2>
							
							<table cellspacing="0" cellpadding="0" border="0" class="newsTable">
								<tr>
									<td class="tdNewsAdminLeft"><?php echo TITLE;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="title" value="<?php if (isset($news['title'])) echo ec($news['title']);?>" /></td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWSTITLE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									<input name="token" type="hidden" value="<?php echo $token;?>" />
								</tr>
								<?php if ($_SESSION['level'] >= 3) { // allowed to publish ?>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo STATUS;?></td>
									<td class="tdNewsAdminCenter">
										<select name="status">
											<option value="1" <?php if($news['status'] == 1 OR !isset($_GET['id'])) echo 'selected="selected"';?>><?php echo NEWSSTATUSACTIVE;?></option>
											<option value="0" <?php if($news['status'] == 0 AND isset($_GET['id'])) echo 'selected="selected"';?>><?php echo NEWSSTATUSARCHIVE;?></option>
										</select>
									</td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWSSTATUS;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<?php } else { // not allowed to publish ?>
								<input type="hidden" name="status" value="0"/>
								<?php }?>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo NEWSDATE;?></td>
									<td class="tdNewsDate"><input type="text" name="time" class="date-pick" value="<?php if (!isset($_GET['id'])) echo date("Y-m-d"); else echo substr($news['time'],0,10);?>" /></td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWSDATE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo NEWSEXTRACT;?></td>
									<td class="tdNewsAdminCenter">
										<textarea name="shorttext" rows="4" cols="20"><?php if (isset($news['shorttext'])) echo ec($news['shorttext']);?></textarea>									
									</td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWSEXTRACT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo NEWSLINKTOPAGE;?></td>
									<td class="tdNewsAdminCenter">
										<select name="linktopage">
											<?php
											echo '<option value=""'.(!$news['linktopage'] ? ' selected="selected"' : '').'>--- '.NOTLINKED.' ---</option>';
											foreach($pagelist as $key => $value) {
												echo '<option value="'.$key.'"'.($key == $news['linktopage'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											}?>
										</select>									
									</td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWSLINKTOPAGE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>

							</table>
							
							<input name="creator" type="hidden" value="<?php if (isset($news['creator'])) echo $news['creator'];?>" />

						<div id="tinyMCE">
							<textarea cols="63" rows="20" name="tinyMCE" class="tinyMCE"><?php if (isset($news['text'])) echo htmlentities($news['text'], $ent=ENT_COMPAT, $site['charset']);?></textarea>
						</div>

						</div>
						
						<p class="updated">
							<?php
							if (isset($news['shorttext'])) echo CREATOR.': '.$news['creator'];
							if (isset($news['updated'])) echo ' | '.UPDATED.' '.$news['updated'].' ('.$news['updater'].')';
							?>
						</p>

					</div>

				</div>
			        
				<div class="clear"></div> <!-- This div clears the float divs --> 

				</form>

			</div>

<?php require('includes/footer.php');?>
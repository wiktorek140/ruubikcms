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
$cmspage = EXTRAUSERS;
if ($_SESSION['level'] != 5) die(NOTALLOWED);
$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['import'])) {
	
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);

	if ($_FILES['csv_file']['name']) {
	
		$filename = $_FILES['csv_file']['tmp_name'];

		$fp = fopen($filename, "r");
		$csvcontent = fread($fp, filesize($filename));

		$lines = $counterrors = $inserts = 0;
		$linearray = array();

		foreach(split("\n", $csvcontent) as $line) {

			$line = trim($line," \t");
			$line = str_replace("\r","",$line);
			$linearray = explode(';',$line);
			if ($linearray[7] === '0') $active = 0;			
			else $active = 1; // users are activated by default (non-zero value)
			$linearray[6] = ltrim($linearray[6], "#"); // trimimg forced text phone (excel quirk)
			$linearray[8] = ltrim($linearray[8], "#"); // and forced text date

			if ($lines > 0) {
				if (count($linearray) == 9) {
					$passwordhash = sha1($linearray[1]);
					$stmt = $dbh->prepare("INSERT OR REPLACE INTO extrauser (username, password, firstname, lastname, organization, email, phone, active, expirytime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->bindParam(1, $linearray[0]);
					$stmt->bindParam(2, $passwordhash);
					$stmt->bindParam(3, $linearray[2]);
					$stmt->bindParam(4, $linearray[3]);
					$stmt->bindParam(5, $linearray[4]);
					$stmt->bindParam(6, $linearray[5]);
					$stmt->bindParam(7, $linearray[6]);
					$stmt->bindParam(8, $active);
					$stmt->bindParam(9, $linearray[8]);
					if ($stmt->execute()) $inserts++;
				} else {
					$counterrors++;
				}
			}
			$lines++;
		}
		$importresults = SUCCEEDED.' '.$inserts.', '.FAILED.': '.$counterrors;
		$error = TRUE;
		header('Location: '.$self);
		@unlink($filename);
	}

} elseif (isset($_POST['export'])) {

	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);

	$csvdata = array();
	$csvdata = array(0 => "username;password;firstname;lastname;organization;email;phone;active;expirydate (yyyy-mm-dd)\n");
	$line = 1;
	$result = $dbh->query("SELECT username, firstname, lastname, organization, email, phone, active, expirytime FROM extrauser ORDER BY username");
	while ($row = $result->fetch (PDO::FETCH_NUM)) {
		foreach ($row as $key => $value) {
			if ($key == 0) $csvdata[$line] .= $value.';;'; // empty password, maintain import format
			elseif ($key == 5) $csvdata[$line] .= "#".$value.';'; // add # to force excel to read phone/date as text (trimming this when importing)
			elseif ($key == 7) $csvdata[$line] .= "#".$value.';'; // add # to force excel to read phone/date as text (trimming this when importing)
			else $csvdata[$line] .= $value.';';
		}
		$csvdata[$line] = substr($csvdata[$line], 0, -1)."\n";
		$line++;
	}
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="csv-export-'.date('Ymd').'.csv"');
	foreach ($csvdata as $line) {
		echo $line;
	}
	exit;

} elseif (isset($_POST['save']) AND !isset($_POST['search'])) {

	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	
	// username not selected if disabled
	if (!$_POST['username']) $_POST['username'] = $_POST['username_hidden'];

	if ($_POST['password'] != $_POST['confirmpassword']) $error = TRUE;

	// strip slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}

	// new user -> get unique username
	if (!$_GET['p'] AND $_GET['n']) {
		$newname = get_unique_url($_POST['username'], 3);
		if ($_POST['password'] == "") $error = TRUE;
		$new = TRUE;
	} else {
		$newname = ec($_GET['p']);
	}
	
	if (!isset($error)) {
			
		// insert or update user
		
		if ($_POST['password'] == "") {
			// passwords not given, just update other data
			$stmt = $dbh->prepare("UPDATE extrauser SET firstname = ?, lastname = ?, email = ?, phone = ?, active = ?, expirytime = ?, organization = ? WHERE username = ?");
			$stmt->bindParam(1, $_POST['firstname']);
			$stmt->bindParam(2, $_POST['lastname']);
			$stmt->bindParam(3, $_POST['email']);
			$stmt->bindParam(4, $_POST['phone']);
			$stmt->bindParam(5, $_POST['active']);
			$stmt->bindParam(6, $_POST['expirytime']);
			$stmt->bindParam(7, $_POST['organization']);
			$stmt->bindParam(8, $_GET['p']);
			$stmt->execute();

		} else {
			// passwords also changes, insert or replace all data
			$stmt = $dbh->prepare("INSERT OR REPLACE INTO extrauser (username, password, firstname, lastname, email, phone, active, expirytime, organization) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bindParam(1, $newname);
			$stmt->bindParam(2, sha1($_POST['password']));
			$stmt->bindParam(3, $_POST['firstname']);
			$stmt->bindParam(4, $_POST['lastname']);
			$stmt->bindParam(5, $_POST['email']);
			$stmt->bindParam(6, $_POST['phone']);
			$stmt->bindParam(7, $_POST['active']);
			$stmt->bindParam(8, $_POST['expirytime']);
			$stmt->bindParam(9, $_POST['organization']);
			$stmt->execute();
		}
	
		//header('Location: '.$self.'?role='.$_POST['role'].'&p='.$newname); // redirect to new user
		header('Location: '.$self);
		save_infomsg(USER.' '.$newname.' '.SAVED);

	} else {
	// passwords do not match
		save_infomsg(CHECKPASSWORDS);
		$error = TRUE;
		// still prefill form with entered values
		$user['username'] = ec($_POST['username']);
		$user['firstname'] = ec($_POST['firstname']);
		$user['lastname'] = ec($_POST['lastname']);
		$user['email'] = ec($_POST['email']);
		$user['phone'] = ec($_POST['phone']);
		$user['active'] = ec($_POST['active']);
		$user['expirytime'] = ec($_POST['expirytime']);
		$user['organization'] = ec($_POST['organization']);
	}
}

if (query_single("SELECT COUNT(*) FROM extrauser") != 0) {

	if (isset($_GET['p'])) {
		
		if ($_GET['d']) {
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
			// delete requested user
			$stmt = $dbh->prepare("DELETE FROM extrauser WHERE username = ?");
			$stmt->bindParam(1, $_GET['p']);
			$stmt->execute();
			save_infomsg(USER.' '.ec($_GET['p']).' '.DELETED);
			header('Location: '.$self);
		}
		
		if ($_GET['a']) {
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
			// activate requested user
			$stmt = $dbh->prepare("UPDATE extrauser SET active = '1' WHERE username = ?");
			$stmt->bindParam(1, $_GET['p']);
			$stmt->execute();
			header('Location: '.$self);
			//save_infomsg(USER.' '.ec($_GET['p']).' '.SAVED);
		}

		if ($_GET['u']) {
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
			// de-activate requested user
			$stmt = $dbh->prepare("UPDATE extrauser SET active = '0' WHERE username = ?");
			$stmt->bindParam(1, $_GET['p']);
			$stmt->execute();
			header('Location: '.$self);
			//save_infomsg(USER.' '.ec($_GET['p']).' '.SAVED);
		}

		// get user data from database
		$stmt = $dbh->prepare("SELECT username, firstname, lastname, email, phone, active, expirytime, organization FROM extrauser WHERE username = ?");
		if ($stmt->execute(array($_GET['p']))) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$user = $result[0];
		}
	}
}

require('includes/head.php');
if (!isset($user['active'])) $user['active'] = 0;
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/extrapagemenu.php');?>


			<!-- **************** RightDiv ******************** -->    
				<form enctype="multipart/form-data" method="post" action="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']);?>" name="newsEditForm">
				<input type="hidden" name="save" value="1" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<?php if (isset($_GET['p']) OR isset($_GET['n'])) {?><li><a href="javascript:document.newsEditForm.submit();" class="save"><span><?php echo SAVE;?></span></a></li><?php }?>
							<li><a href="<?php echo $self.'?n=1';?>" class="new"><span><?php echo RNEW;?></span></a></li>
							<?php if (isset($_GET['p'])) {?><li><a href="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']).'&amp;d=1';?>" class="delete"><span><?php echo DELETE;?></span></a></li><?php }?>
						</ul>
					</div>

					<div id="rightContent">

						<div id="newsAdmin">

							<h2><?php echo EXTRAUSERS;?></h2>

							<?php 
							if (isset($_POST['search']) AND !empty($_POST['keyword'])) {
								$stmt = $dbh->prepare("SELECT username, firstname, lastname, active, expirytime, organization FROM extrauser WHERE username LIKE '%' || ? || '%' OR firstname LIKE '%' || ? || '%' OR lastname LIKE '%' || ? || '%' OR organization LIKE '%' || ? || '%' ORDER BY username");
								$stmt->bindParam(1, $_POST['keyword']);	
								$stmt->bindParam(2, $_POST['keyword']);	
								$stmt->bindParam(3, $_POST['keyword']);	
								$stmt->bindParam(4, $_POST['keyword']);	
								$stmt->execute();
								$counter = 0;
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									if ($counter == 0) {
										echo '<p>'.SEARCHRESULTSFOR.' "<strong>'.ec($_POST['keyword']).'</strong>"</p></p><p><a href="extrausers.php"><< '.EXTRAUSERS.'</a></p>';
										echo '<table class="logtable">';
										echo '<tr><th>'.USERNAME.'</th><th>'.FIRSTNAME.'</th><th>'.LASTNAME.'</th><th>'.ORGANIZATION.'</th><th>'.ACTIVE.'</th><th>'.VALIDUNTIL.'</th><th></th><th></th><th></th><th></th></tr>';								
									}
									echo '<tr><td><a href="'.$self.'?p='.$row['username'].'">'.$row['username'].'</a></td><td>'.$row['firstname'].'</td><td>'.$row['lastname'].'</td><td>'.$row['organization'].'</td><td style="text-align: center;">'.($row['active'] == 1 ? '<img src="images/accept.png" alt="" title="'.ACTIVE.'" />' : '<img src="images/cancel.png" alt="" title="'.INACTIVE.'" />').'</td><td>'.$row['expirytime'].'</td><td><a href="'.$self.'?p='.$row['username'].'&amp;a=1&amp;token='.$token.'"><img src="images/accept.png" alt="" title="'.ACTIVATE.'" /></a></td><td><a href="'.$self.'?p='.$row['username'].'&amp;u=1&amp;token='.$token.'"><img src="images/cancel.png" alt="" title="'.DEACTIVATE.'" /></a></td><td><a href="'.$self.'?p='.$row['username'].'&amp;d=1&amp;token='.$token.'"><img src="images/user_delete.png" alt="" title="'.DELETE.'" onclick="return confirm(\''.AREYOUSURE.' ('.DELETE.' '.USER.')'.'\')" /></a></td><td><a href="?p='.$row['username'].'"><img src="images/user_edit.png" alt="" title="'.EDIT.'" /></a></td></tr>';
									$counter++;
								}

								if ($counter > 0) echo '</table>';
								else echo '<p>'.NOSEARCHRESULTS.' "<strong>'.ec($_POST['keyword']).'</strong>"</p><p><a href="extrausers.php"><< '.EXTRAUSERS.'</a></p>';

							} elseif (isset($_GET['n']) OR isset($_GET['p'])) { ?>

							<table cellspacing="0" cellpadding="0" border="0" class="newsTable">

								<tr>
									<td class="tdNewsAdminLeft"><?php echo USERNAME;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="username" value="<?php if (isset($user['username'])) echo $user['username'];?>"<?php if (isset($_GET['p'])) echo ' disabled="disabled"';?> /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
									<input name="username_hidden" type="hidden" value="<?php if (isset($_GET['p'])) echo ec($_GET['p']);?>" />
									<input name="token" type="hidden" value="<?php echo $token;?>" />
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo NEWPASSWORD;?></td>
									<td class="tdNewsAdminCenter"><input type="password" name="password" value="" /></td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_NEWPASSWORD;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo CONFIRMNEWPASSWORD;?></td>
									<td class="tdNewsAdminCenter"><input type="password" name="confirmpassword" value="" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>							
								<tr>
									<td class="tdNewsAdminLeft"><?php echo ACTIVE;?></td>
									<td class="tdNewsAdminCenter">
										<select name="active">
											<option value="1" <?php if($user['active'] == 1 OR !isset($_GET['p'])) echo 'selected="selected"';?>><?php echo ENABLED;?></option>
											<option value="0" <?php if($user['active'] == 0 AND isset($_GET['p'])) echo 'selected="selected"';?>><?php echo DISABLED;?></option>
										</select>
									</td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_EXTRAACTIVE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo VALIDUNTIL;?></td>
									<td class="tdNewsDate"><input type="text" name="expirytime" class="date-pick" value="<?php if (isset($user['expirytime'])) echo substr($user['expirytime'],0,10);?>" /></td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_VALIDUNTIL;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo FIRSTNAME;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="firstname" value="<?php if (isset($user['firstname'])) echo $user['firstname'];?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo LASTNAME;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="lastname" value="<?php if (isset($user['lastname'])) echo $user['lastname'];?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo ORGANIZATION;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="organization" value="<?php if (isset($user['organization'])) echo $user['organization'];?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo EMAIL;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="email" value="<?php if (isset($user['email'])) echo $user['email'];?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo PHONE;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="phone" value="<?php if (isset($user['phone'])) echo $user['phone'];?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>

							</table>

							<?php 

							} else {

								// pagination
								$rowsperpage = ROWSPERPAGE;
								if (isset($_GET['page'])) $page = intval($_GET['page']);
								else $page = 1;
								$start = $rowsperpage * ($page-1);
								$total = query_single("SELECT COUNT(*) FROM extrauser");
								$lastpage = ceil($total/$rowsperpage);

								// correct self also for ordering
								$slf = ec($_SERVER['PHP_SELF']).'?';
								if (isset($_GET['order'])) $slf .= 'order='.$_GET['order'];
								else $slf .= 'order=1';
								if (isset($_GET['desc'])) $slf .= '&amp;desc=1';
								
								// page links for navigations
								$nav  = '';

								if ($page <= 11) $firstlink = 1;
								else $firstlink = $page - 10;

								if ($page + 10 > $lastpage) $lastlink = $lastpage;
								else $lastlink = $page + 10;

								//for($i = 1; $i <= $lastpage; $i++) { // print all links
								for($i = $firstlink; $i <= $lastlink; $i++) {
									if ($i == $page) $nav .= " $i "; // no need to create a link to current page
									else $nav .= " <a href=\"$slf&amp;page=$i\">$i</a> ";
								}
								// first, next, previous & last links
								if ($page > 1) {
									$i  = $page - 1;
									$prev  = ' <a href="'.$slf.'&amp;page='.$i.'">'.PREVIOUS.'</a> ';
									$first  = ' <a href="'.$slf.'&amp;page=1">'.FIRSTPAGE.'</a> ';
								} else {
								   $prev  = '&nbsp;'; // we're on page one, don't print previous link
								   $first = '&nbsp;'; // nor the first page link
								}

								if ($page < $lastpage) {
								   $i = $page + 1;
									$next  = ' <a href="'.$slf.'&amp;page='.$i.'">'.NEXT.'</a> ';
									$last  = ' <a href="'.$slf.'&amp;page='.$lastpage.'">'.LASTPAGE.'</a> ';
								} else {
								   $next = '&nbsp;'; // we're on the last page, don't print next link
								   $last = '&nbsp;'; // nor the last page link
								}						
								
								// ordering the users
								if (!isset($_GET['order'])) $ordercode = '';
								else $ordercode = $_GET['order'];
								if ($ordercode == 1) $order = 'username';
								elseif ($ordercode == 2) $order = 'firstname';
								elseif ($ordercode == 3) $order = 'lastname';
								elseif ($ordercode == 4) $order = 'organization';
								elseif ($ordercode == 5) $order = 'active';
								elseif ($ordercode == 6) $order = 'expirytime';
								else $order = 'username'; // default ordering
								if (isset($_GET['desc']) AND $_GET['desc'] == 1) $desc = ' DESC';
								else $desc = '';

								if ($lastpage > 1) echo '<p>'.SHOWING.' '.($start+1).' - '.($start+$rowsperpage > $total ? $total : $start+$rowsperpage).' '.sprintf(OFTOTALRESULTS, $total).'</p>';

								echo '<table class="logtable">';

								echo '<tr><th><a href="'.(isset($_GET['desc']) ? '?order=1' : '?order=1&amp;desc=1').'">'.USERNAME.'</a></th><th><a href="'.(isset($_GET['desc']) ? '?order=2' : '?order=2&amp;desc=1').'">'.FIRSTNAME.'</a></th><th><a href="'.(isset($_GET['desc']) ? '?order=3' : '?order=3&amp;desc=1').'">'.LASTNAME.'</a></th><th><a href="'.(isset($_GET['desc']) ? '?order=4' : '?order=4&amp;desc=1').'">'.ORGANIZATION.'</a></th><th><a href="'.(isset($_GET['desc']) ? '?order=5' : '?order=5&amp;desc=1').'">'.ACTIVE.'</a></th><th><a href="'.(isset($_GET['desc']) ? '?order=6' : '?order=6&amp;desc=1').'">'.VALIDUNTIL.'</a></th><th></th><th></th><th></th><th></th></tr>';

								$sql = "SELECT username, firstname, lastname, active, expirytime, organization FROM extrauser ORDER BY ".$order.$desc." LIMIT ".$start.", ".$rowsperpage;

								foreach ($dbh->query($sql) as $row) {

									echo '<tr><td><a href="'.$self.'?p='.$row['username'].'">'.$row['username'].'</a></td><td>'.$row['firstname'].'</td><td>'.$row['lastname'].'</td><td>'.$row['organization'].'</td><td style="text-align: center;">'.($row['active'] == 1 ? '<img src="images/accept.png" alt="" title="'.ACTIVE.'" />' : '<img src="images/cancel.png" alt="" title="'.INACTIVE.'" />').'</td><td>'.$row['expirytime'].'</td><td><a href="'.$self.'?p='.$row['username'].'&amp;a=1&amp;token='.$token.'"><img src="images/accept.png" alt="" title="'.ACTIVATE.'" /></a></td><td><a href="'.$self.'?p='.$row['username'].'&amp;u=1&amp;token='.$token.'"><img src="images/cancel.png" alt="" title="'.DEACTIVATE.'" /></a></td><td><a href="'.$self.'?p='.$row['username'].'&amp;d=1&amp;token='.$token.'"><img src="images/user_delete.png" alt="" title="'.DELETE.'" onclick="return confirm(\''.AREYOUSURE.' ('.DELETE.' '.USER.')'.'\')" /></a></td><td><a href="?p='.$row['username'].'"><img src="images/user_edit.png" alt="" title="'.EDIT.'" /></a></td></tr>';

								}

								echo '</table>';

							}

							if (!isset($_GET['p']) AND !isset($_GET['n'])) { 
							
							// print the navigation links for pagination
							if ($lastpage > 1) echo '<p>'.$prev.$nav.$next.'</p>';
							?>

							<p><input type="file" name="csv_file" class="fileInput" value="" />
							<input type="submit" name="import" value="<?php echo IMPORT;?> CSV" onclick="<?php echo "return confirm('".IMPORTCONFIRM."');";?>" />
							<input type="submit" name="export" value="<?php echo EXPORT;?> CSV" />
							<input type="text" name="keyword" value="<?php if (isset($_POST['keyword'])) echo ec($_POST['keyword']);?>" />
							<input type="submit" name="search" value="<?php echo SEARCH;?>"></p>
							<input name="token" type="hidden" value="<?php echo $token;?>" />
							
							<p><a href="extranet.php"><< <?php echo EXTRAPAGES;?></a></p>

							<?php } else {?>
							
							<p><a href="extrausers.php"><< <?php echo EXTRAUSERS;?></a></p>
							
							<?php } ?>

						</div>

					</div>

				</div>

				<div class="clear"></div> <!-- This div clears the float divs --> 

				</form>

			</div>

<?php require('includes/footer.php');?>
<?php 
if (isset($error)) {
	echo '<script language="javascript" type="text/javascript">alert(\''.CHECKPASSWORDS.'\');</script>';
}
?>
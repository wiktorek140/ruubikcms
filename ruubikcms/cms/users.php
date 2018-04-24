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
$cmspage = USERS;
if ($_SESSION['level'] != 5) die(NOTALLOWED);
$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['save'])) {
	
	// some CSRF protection
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);

	// username not selected if disabled
	if (!isset($_POST['username'])) $_POST['username'] = $_POST['username_hidden'];

	if ($_POST['password'] != $_POST['confirmpassword']) $error = TRUE;

	// strip slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}
	
	if (!$_POST['role']) $_POST['role'] = '5';
	// not dot allow administrator to change his own role (ensure there is always one administrator)
	if (isset($_GET['p'])) {
		if ($_GET['p'] == $_SESSION['uid']) $_POST['role'] = '5';
	}
			
	// new user -> get unique username
	if (!isset($_GET['p']) AND isset($_GET['n'])) {
		$newname = get_unique_url($_POST['username'], 2);
		if ($_POST['password'] == "") $error = TRUE;
		$new = TRUE;
	} else {
		$newname = ec($_GET['p']);
	}
	
	if (!isset($error)) {
	
		// insert or update user
		
		if ($_POST['password'] == "") {
			// passwords not given, just update other data
			$stmt = $dbh->prepare("UPDATE cmsuser SET role = ?, firstname = ?, lastname = ?, email = ?, phone = ? WHERE username = ?");
			$stmt->bindParam(1, $_POST['role']);
			$stmt->bindParam(2, $_POST['firstname']);
			$stmt->bindParam(3, $_POST['lastname']);
			$stmt->bindParam(4, $_POST['email']);
			$stmt->bindParam(5, $_POST['phone']);
			$stmt->bindParam(6, $_GET['p']);
			$stmt->execute();
		
		} else {
			$passwordhash = sha1($_POST['password']);
			// passwords also changes, insert or replace all data
			$stmt = $dbh->prepare("INSERT OR REPLACE INTO cmsuser (username, password, role, firstname, lastname, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bindParam(1, $newname);
			$stmt->bindParam(2, $passwordhash);
			$stmt->bindParam(3, $_POST['role']);
			$stmt->bindParam(4, $_POST['firstname']);
			$stmt->bindParam(5, $_POST['lastname']);
			$stmt->bindParam(6, $_POST['email']);
			$stmt->bindParam(7, $_POST['phone']);
			$stmt->execute();
		}
	
		header('Location: '.$self.'?role='.$_POST['role'].'&p='.$newname); // redirect to new user
		save_infomsg(USER.' '.$newname.' '.SAVED);
		
	} else {
	// passwords do not match
		save_infomsg(CHECKPASSWORDS);
		$error = TRUE;
		// still prefill form with entered values
		$user['username'] = ec($_POST['username']);
		$user['role'] = ec($_POST['role']);
		$user['firstname'] = ec($_POST['firstname']);
		$user['lastname'] = ec($_POST['lastname']);
		$user['email'] = ec($_POST['email']);
		$user['phone'] = ec($_POST['phone']);
	}
}

if (query_single("SELECT COUNT(*) FROM cmsuser") != 0) {

	if (isset($_GET['p'])) {
				
		if (isset($_GET['d'])) {
			// some CSRF protection
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);

			if  ($_GET['p'] != $_SESSION['uid']) {
				// delete requested user
				$stmt = $dbh->prepare("DELETE FROM cmsuser WHERE username = ?");
				$stmt->bindParam(1, $_GET['p']);
				$stmt->execute();
				save_infomsg(USER.' '.$_GET['p'].' '.DELETED);
				header('Location: '.$self);
			}
		}

		// get user data from database
		$stmt = $dbh->prepare("SELECT username, firstname, role, lastname, email, phone FROM cmsuser WHERE username = ?");
		if ($stmt->execute(array($_GET['p']))) {
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$user = $result[0];
		}

	} elseif (!isset($_GET['n']) AND !isset($_GET['role'])) {
	// no username given and not creating new page etc ->  redirect first user
		header('Location: '.$self.'?role=5&p='.query_single("SELECT username FROM cmsuser WHERE role = '5' ORDER BY username LIMIT 1"));
	}
}

require('includes/head.php');
if (!isset($user['role'])) $user['role'] = 5;
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/usersmenu.php');?>


			<!-- **************** RightDiv ******************** -->    
				<form method="post" action="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']);?>" name="newsEditForm">
				<input type="hidden" name="save" value="1" />
				<input type="hidden" name="ordernum" value="<?php echo $page['ordernum'];?>" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<?php if (isset($_GET['p']) OR isset($_GET['n'])) {?><li><a href="javascript:document.newsEditForm.submit();" class="save"><span><?php echo SAVE;?></span></a></li><?php }?>
							<li><a href="<?php echo $self.'?n=1';?>" class="new"><span><?php echo RNEW;?></span></a></li>
							<?php if (isset($_GET['p'])) {?><li><a href="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']).'&amp;d=1&amp;token='.$token;?>" class="delete"><span><?php echo DELETE;?></span></a></li><?php }?>
						</ul>
					</div>

					<div id="rightContent">					

						<div id="newsAdmin">

							<h2><?php echo USERS;?></h2>
							
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
									<td class="tdNewsAdminLeft"><?php echo ROLE;?></td>
									<td class="tdNewsAdminCenter">
										<select name="role"<?php if (isset($_GET['p'])) { if ($_GET['p'] == $_SESSION['uid']) echo ' disabled="disabled"';}?>>
											<option value="5" <?php if($user['role'] == 5 OR !$_GET['p']) echo 'selected="selected"';?>><?php echo ADMINISTRATORS;?></option>
											<option value="4" <?php if($user['role'] == 4) echo 'selected="selected"';?>><?php echo SUPEREDITORS;?></option>
											<option value="3" <?php if($user['role'] == 3) echo 'selected="selected"';?>><?php echo PUBLISHERS;?></option>
											<option value="2" <?php if($user['role'] == 2) echo 'selected="selected"';?>><?php echo CONTRIBUTORS;?></option>
										</select>
									</td>
									<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_ROLE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo FIRSTNAME;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="firstname" value="<?php if (isset($user['firstname'])) echo ec($user['firstname']);?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo LASTNAME;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="lastname" value="<?php if (isset($user['lastname'])) echo ec($user['lastname']);?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo EMAIL;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="email" value="<?php if (isset($user['email'])) echo ec($user['email']);?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>
								<tr>
									<td class="tdNewsAdminLeft"><?php echo PHONE;?></td>
									<td class="tdNewsAdminCenter"><input type="text" name="phone" value="<?php if (isset($user['phone'])) echo ec($user['phone']);?>" /></td>
									<td class="tdNewsAdminRight">&nbsp;</td>
								</tr>

							</table>

						</div>

					</div>

				</div>
			        
				<div class="clear"></div> <!-- This div clears the float divs --> 

				</form>

			</div>

<?php require('includes/footer.php');?>
<?php 
if ($error) {
	echo '<script language="javascript" type="text/javascript">alert(\''.CHECKPASSWORDS.'\');</script>';
}
?>
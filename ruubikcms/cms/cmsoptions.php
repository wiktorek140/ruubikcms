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
require('includes/required.php');
$cmspage = CMSOPTIONS;
$cmsoptions = array();
if ($_SESSION['level'] != 5) die(NOTALLOWED);
$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['undo'])) {
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	if (copy('../'.PDO_DB_FOLDER.'/ruubikcms-last-login.sqlite', '../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME)) {
		$error = UNDO.' '.SUCCEEDED.'!';
		$undook = TRUE;
	} else {
		$error = UNDO.' '.FAILED.'!';
		$restoreok = FALSE;
	}
	save_infomsg($error);
}

if (isset($_POST['restore'])) {
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	$target = '../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME;
	$temp = '../'.PDO_DB_FOLDER.'/ruubikcms-temp.sqlite';
	
	if (copy($target, '../'.PDO_DB_FOLDER.'/ruubikcms-backup.sqlite')) {
		if (move_uploaded_file($_FILES['restore_file']['tmp_name'], $temp)) {
			$fp = fopen($temp, "r");
			if (fread($fp, 16) == 'SQLite format 3'.chr(0)) {
				// sqlite3 file header ok
				if (copy($temp, $target)) {
					@unlink($temp);
					$error = RESTORETOOL.' '.SUCCEEDED.'!';
					$restoreok = TRUE;
				}
			}
			fclose($fp);			
		}
	}
	if (!$restoreok) {
		$error = RESTORETOOL.' '.FAILED.'!';
		$restoreok = FALSE;
	}
	save_infomsg($error);
}

if (isset($_POST['backup'])) {
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	$file_path = '../'.PDO_DB_FOLDER.'/'.PDO_DB_NAME;
	$fsize = filesize($file_path); 
	// set headers
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: public');
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="cms-backup-'.date('Ymd').'.sqlite"');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.$fsize);
	// set download
	$file = @fopen($file_path,"rb");
	if ($file) {
		while(!feof($file)) {
			print(fread($file, 1024*8));
			flush();
			if (connection_status()!=0) {
				@fclose($file);
				die();
			}
		}
		@fclose($file);
		die();
	}
}

if (isset($_POST['save']) AND !(isset($_POST['restore']) OR isset($_POST['backup']) OR isset($_POST['undo']))) {
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	// strip slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}
	// make sure we save followig as integers
	$resize_width = intval($_POST['resize_width']);
	$resize_height = intval($_POST['resize_height']);
	$logout_time = intval($_POST['logout_time']);
	$pagination_rows = intval($_POST['pagination_rows']);
		
	// save cms options in database
	$stmt = $dbh->prepare("INSERT OR REPLACE INTO options (id, cmslang, resize_width, resize_height, logout_time, pagination_rows, use_help) VALUES (1, ?, ?, ?, ?, ?, ?)");
	$stmt->bindParam(1, $_POST['cmslang']);
	$stmt->bindParam(2, $resize_width);
	$stmt->bindParam(3, $resize_height);
	$stmt->bindParam(4, $logout_time);
	$stmt->bindParam(5, $pagination_rows);
	$stmt->bindParam(6, $_POST['use_help']);
	$stmt->execute();
	
	save_infomsg(CMSOPTIONS.' '.SAVED);		
	header('Location: '.$self); // refresh to apply new options 
}

$cmsoptions = get_cmsoptions();

require('includes/head.php');
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
 
			<div id="mainBody">   
			   			
			<?php require('includes/pagemenu.php');?>
			
                        
			<!-- **************** RightDiv ******************** -->    
                        
				<form enctype="multipart/form-data" method="post" action="<?php echo $self;?>" name="pageEditForm">
				<input type="hidden" name="save" value="1" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<li><a href="javascript:document.pageEditForm.submit();" class="save"><span><?php echo SAVE;?></span></a></li>
						</ul>  

						<!--<div id="currentPage"><?php echo ec($page['name']);?></div>-->
           				
					</div> 
					
					<div id="rightContent">

						<div id="cmsOptionsLeft">

    						<div id="settingsCMS">
    							<h2><?php echo CMSOPTIONS;?></h2>
    							
    							<table cellspacing="0" cellpadding="0" border="0">
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo LANGUAGE;?></td>
    									<td class="tdCMSSetupCenter">
    										<select name="cmslang">							
    										<?php foreach (glob('languages/*.php') as $filename) {
    											echo '<option value="'.(basename($filename, ".php")).'"'.($cmsoptions['cmslang'] == basename($filename, ".php") ? ' selected="selected"' : '').'>'.(basename($filename, ".php")).'</option>';
    										}?>
    										</select>									
    									</td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_CMSLANG;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo LOGOUTTIME;?></td>
    									<td class="tdCMSSetupCenter">
    										<select name="logout_time">
    											<option value="900" <?php if($cmsoptions['logout_time'] == 900) echo 'selected="selected"';?>>15 min</option>
    											<option value="1800" <?php if($cmsoptions['logout_time'] == 1800) echo 'selected="selected"';?>>30 min</option>
    											<option value="2700" <?php if($cmsoptions['logout_time'] == 2700) echo 'selected="selected"';?>>45 min</option>
    											<option value="3600" <?php if($cmsoptions['logout_time'] == 3600) echo 'selected="selected"';?>>60 min</option>
    											<option value="5400" <?php if($cmsoptions['logout_time'] == 5400) echo 'selected="selected"';?>>90 min</option>
    											<option value="7200" <?php if($cmsoptions['logout_time'] == 7200) echo 'selected="selected"';?>>2 h</option>
    											<option value="14400" <?php if($cmsoptions['logout_time'] == 14400) echo 'selected="selected"';?>>4 h</option>
    										</select>									
    									</td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_LOGOUTTIME;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo AUTORESIZEWIDTH;?></td>
    									<td class="tdCMSSetupCenter"><input type="text" name="resize_width" value="<?php if (isset($cmsoptions['resize_width'])) echo $cmsoptions['resize_width'];?>" /></td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_AUTOWIDTH;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo AUTORESIZEHEIGHT;?></td>
    									<td class="tdCMSSetupCenter"><input type="text" name="resize_height" value="<?php if (isset($cmsoptions['resize_height'])) echo $cmsoptions['resize_height'];?>" /></td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_AUTOHEIGHT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo NUMROWSPERPAGE;?></td>
    									<td class="tdCMSSetupCenter"><input type="text" name="pagination_rows" value="<?php if (isset($cmsoptions['pagination_rows'])) echo $cmsoptions['pagination_rows'];?>" /></td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_PAGINATIONROWS;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdCMSSetupLeft"><?php echo USEHELPTEXTS;?></td>
    									<td class="tdCMSSetupCenter">
											<select name="use_help">
												<option value="0" <?php if($cmsoptions['use_help'] == 0) echo 'selected="selected"';?>><?php echo DISABLED;?></option>
												<option value="1" <?php if($cmsoptions['use_help'] == 1) echo 'selected="selected"';?>><?php echo ENABLED;?></option>
											</select>										
    									</td>
    									<td class="tdCMSSetupRight"><a href="#" class="tooltip" title="<?php echo H_USEHELPTEXTS;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    							</table>
    																												    																												  							
    						</div>

    					</div>

						<div id="cmsOptionsRight">
						
    						<div id="settingsBackup">
    						
    							<h2><?php echo DOBACKUP.' & '.RESTORE;?></h2>
    
    							<table cellspacing="0" cellpadding="0" border="0">
    								<tr>
    									<td class="tdBackupLeft"><?php echo SAVE.' '.BACKUP;?></td>
    									<td class="tdBackupCenter"><input type="submit" name="backup" value="<?php echo DOBACKUP;?>" /></td>
    									<td class="tdBackupRight"><a href="#" class="tooltip" title="<?php echo H_BACKUP;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
										<input name="token" type="hidden" value="<?php echo $token;?>" />
    								</tr>
    								
    								<tr>
    									<td class="tdBackupLeft"><?php echo UNDOLASTLOGIN;?></td>
    									<td class="tdBackupCenter"><input type="submit" name="undo" value="<?php echo UNDO;?>" onclick="<?php echo "return confirm('".UNDOCONFIRM."');";?>" /></td>
    									<td class="tdBackupRight"><a href="#" class="tooltip" title="<?php echo H_UNDOLASTLOGIN;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>    								
    								
    								<tr>
    									<td class="tdBackupLeft"><?php echo RESTORE.' '.BACKUP;?></td>
    									<td class="tdBackupCenter">&nbsp;</td>
    									<td class="tdBackupRight"><a href="#" class="tooltip" title="<?php echo H_RESTORE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
    								<tr>
    									<td class="tdBackupLeft"><input type="file" name="restore_file" class="fileInput" value="" /></td>
    									<td class="tdBackupCenter"><input type="submit" name="restore" value="<?php echo RESTORE;?>" onclick="<?php echo "return confirm('".RESTORECONFIRM."');";?>" /></td>
    									<td class="tdBackupRight">&nbsp;</td>
    								</tr>
							
    							</table>
    
    						</div>

							<div id="settingsPassword">

    							<h2><?php echo LOG.' & '.DOWNLOADSTATS;?></h2>
								<p><a href="showlog.php"><?php echo 'RuubikCMS Admin '.LOG;?></a></p>
								<p><a href="dlcount.php"><?php echo DOWNLOADSTATS;?></a></p>
								<p><a href="dllog.php"><?php echo DOWNLOADLOG;?></a></p>
								<p><a href="extradlcount.php"><?php echo EXTRADOWNLOADSTATS;?></a></p>
								<p><a href="extradllog.php"><?php echo EXTRADOWNLOADLOG;?></a></p>
    						</div>


                        </div>

						<div class="clear"></div> <!-- This div clears the float divs --> 

					</div>

				</div>

				</form>

				<div class="clear"></div> <!-- This div clears the float divs --> 

            </div>

<?php require('includes/footer.php');?>
<?php 
if (isset($_POST['restore'])) {
	if ($restoreok) $msg = RESTORETOOL.' '.SUCCEEDED.'!';
	else $msg = RESTORETOOL.' '.FAILED.'!';
	echo '<script language="javascript" type="text/javascript">alert(\''.$msg.'\');</script>';
}
if (isset($_POST['undo'])) {
	if ($undook) $msg = UNDO.' '.SUCCEEDED.'!';
	else $msg = UNDO.' '.FAILED.'!';
	echo '<script language="javascript" type="text/javascript">alert(\''.$msg.'\');</script>';
}
?>
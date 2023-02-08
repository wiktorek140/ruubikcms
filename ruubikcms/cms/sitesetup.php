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
$cmspage = SITESETUP;
$site = array();
if ($_SESSION['level'] != 5) die(NOTALLOWED);
$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['save'])) {
	
	if (!valid_csrf_token($_POST['token'])) die(NOTALLOWED);
	
	// strip slashes if needed
	if (function_exists('get_magic_quotes_gpc') AND get_magic_quotes_gpc()) {
		$_POST = stripslashes_deep($_POST);
	}

	// just default invalid charnum to 110
	if (empty($_POST['news_maxshort']) OR !is_numeric($_POST['news_maxshort'])) $_POST['news_maxshort'] = '110';
	
	// make sure we save numeric data as integers
	$_POST['news_maxshort'] = intval($_POST['news_maxshort']);
	$news_num = intval($_POST['news_num']);
	
	// siteroot is not encoded for htmlentities in page.php so strip tags to prevent XSS injection
	$_POST['siteroot'] = strip_tags($_POST['siteroot']);
	
	// some silly boolean conversion
	if (isset($_POST['news_readmore'])) $readmore = 1;
	else $readmore = 0;
	if (isset($_POST['news_showdate'])) $showdate = 1;
	else $showdate = 0;
	if (isset($_POST['news_textlink'])) $textlink = 1;
	else $textlink = 0;
	
	// save sitedata in database
	$stmt = $dbh->prepare("INSERT OR REPLACE INTO site (id, name, siteroot, doctype, charset, robots, title, description, keywords, copyright, author, lang, gacode, no_image1, no_image2, news_readmore, news_showdate, news_textlink, news_maxshort, clean_url, url_suffix, news_num, news_readmoretext) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bindParam(1, $_POST['name']);
	$stmt->bindParam(2, $_POST['siteroot']);
	$stmt->bindParam(3, $_POST['doctype']);
	$stmt->bindParam(4, $_POST['charset']);
	$stmt->bindParam(5, $_POST['robots']);
	$stmt->bindParam(6, $_POST['title']);
	$stmt->bindParam(7, $_POST['description']);
	$stmt->bindParam(8, $_POST['keywords']);
	$stmt->bindParam(9, $_POST['copyright']);
	$stmt->bindParam(10, $_POST['author']);
	$stmt->bindParam(11, $_POST['lang']);
	$stmt->bindParam(12, $_POST['gacode']);
	$stmt->bindParam(13, $_POST['no_image1']);
	$stmt->bindParam(14, $_POST['no_image2']);
	$stmt->bindParam(15, $readmore);
	$stmt->bindParam(16, $showdate);
	$stmt->bindParam(17, $textlink);
	$stmt->bindParam(18, $_POST['news_maxshort']);
	$stmt->bindParam(19, $_POST['clean_url']);
	$stmt->bindParam(20, $_POST['url_suffix']);
	$stmt->bindParam(21, $news_num);
	$stmt->bindParam(22, $_POST['news_readmoretext']);
	$stmt->execute();
	
	save_infomsg(SITESETUP.' '.SAVED);
}

if (query_single("SELECT COUNT(*) FROM site WHERE id = 1") != 0) $site = get_site_data();

require('includes/head.php');
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
 
			<div id="mainBody">   
			   			
			<?php require('includes/pagemenu.php');?>
			
                        
			<!-- **************** RightDiv ******************** -->    
                        
				<form method="post" action="<?php echo $self;?>" name="pageEditForm">
				<input type="hidden" name="save" value="1" />
                <div id="rightDiv">
					<div id="buttonBar">
						<ul>
							<li><a href="javascript:document.pageEditForm.submit();" class="save"><span><?php echo SAVE;?></span></a></li>
						</ul>  
           				
					</div> 
					
					<div id="rightContent">

						<div id="webSiteLeft">
						
							<div id="webSiteSettings">
						
								<h2><?php echo WEBSITESETUP;?></h2>
								
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="tdWebSetupLeft"><?php echo SITENAME;?></td>
										<td class="tdWebSetupCenter"><input type="text" name="name" value="<?php echo ec($site['name']);?>" /></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_SITENAME;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
										<input name="token" type="hidden" value="<?php echo $token;?>" />
									</tr>
									<tr>
    									<td class="tdWebSetupLeft"><?php echo SITEROOT;?></td>
    									<td class="tdWebSetupCenter"><input type="text" name="siteroot" value="<?php echo ec($site['siteroot']);?>" /></td>
    									<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_SITEROOT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
    								</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo DOCTYPE;?></td>
										<td class="tdWebSetupCenter">
											<select name="doctype">
											<?php foreach ($doctype as $key => $value) {
												echo '<option value="'.$key.'"'.($key == $site['doctype'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											}?>
											</select>
										</td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_DOCTYPE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo CHARSET;?></td>
										<td class="tdWebSetupCenter">
											<select name="charset">
											<?php foreach ($charset as $key => $value) {
												echo '<option value="'.$key.'"'.($key == $site['charset'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											}?>
											</select>
										</td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_CHARSET;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo ROBOTS;?></td>
										<td class="tdWebSetupCenter">
											<select name="robots">
											<?php foreach ($robots as $key => $value) {
												echo '<option value="'.$key.'"'.($key == $site['robots'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											}?>											
											</select>
										</td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_ROBOTS;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo PAGETITLE;?></td>
										<td class="tdWebSetupCenter"><input type="text" name="title" value="<?php echo ec($site['title']);?>" /></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_PAGETITLE.H_SETUPDEFAULT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo DESCRIPTION;?></td>
										<td class="tdWebSetupCenter"><textarea cols="30" rows="5" name="description"><?php echo ec($site['description']);?></textarea></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_DESCRIPTION.H_SETUPDEFAULT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo KEYWORDS;?></td>
										<td class="tdWebSetupCenter"><textarea cols="30" rows="5" name="keywords"><?php echo ec($site['keywords']);?></textarea></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_KEYWORDS.H_SETUPDEFAULT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo COPYRIGHT;?></td>
										<td class="tdWebSetupCenter"><input type="text" name="copyright" value="<?php echo ec($site['copyright']);?>" /></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_COPYRIGHT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo AUTHOR;?></td>
										<td class="tdWebSetupCenter"><input type="text" name="author" value="<?php echo ec($site['author']);?>" /></td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_AUTHOR;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdWebSetupLeft"><?php echo LANGUAGE;?></td>
										<td class="tdWebSetupCenter">
											<select name="lang">
											<?php foreach ($lang as $key => $value) {
												echo '<option value="'.$key.'"'.($key == $site['lang'] ? ' selected="selected"' : '').'>'.$value.'</option>';
											} ?>
											</select>										
										</td>
										<td class="tdWebSetupRight"><a href="#" class="tooltip" title="<?php echo H_LANGUAGE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>

								</table>

							</div>

							<div id="webSiteRest">

								<h2><?php echo IMAGESETTINGS.', '.URLSETTINGS;?></h2>

								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="tdRestSetupLeft"><?php echo IFMISSING.' '.IMAGE.'1';?></td>
										<td class="tdRestSetupCenter">
											<select name="no_image1">
												<option value="0" <?php if($site['no_image1'] == 0) echo 'selected="selected"';?>><?php echo SHOWASEMPTYIMG;?></option>
												<option value="1" <?php if($site['no_image1'] == 1) echo 'selected="selected"';?>><?php echo USEFRONTPAGE. ' '.IMAGE.'1';?></option>
											</select>										
										</td>
										<td class="tdRestSetupRight"><a href="#" class="tooltip" title="<?php echo H_IMGMISSING;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdRestSetupLeft"><?php echo IFMISSING.' '.IMAGE.'2';?></td>
										<td class="tdRestSetupCenter">
											<select name="no_image2">
												<option value="0" <?php if($site['no_image2'] == 0) echo 'selected="selected"';?>><?php echo SHOWASEMPTYIMG;?></option>
												<option value="1" <?php if($site['no_image2'] == 1) echo 'selected="selected"';?>><?php echo USEFRONTPAGE. ' '.IMAGE.'2';?></option>
											</select>										
										</td>
										<td class="tdRestSetupRight"><a href="#" class="tooltip" title="<?php echo H_IMGMISSING;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdRestSetupLeft"><?php echo CLEANURL;?></td>
										<td class="tdRestSetupCenter">
											<select name="clean_url">
												<option value="0" <?php if($site['clean_url'] == 0) echo 'selected="selected"';?>><?php echo DISABLED;?></option>
												<option value="1" <?php if($site['clean_url'] == 1) echo 'selected="selected"';?>><?php echo ENABLED;?></option>
												<!--<option value="2" <?php if($site['clean_url'] == 2) echo 'selected="selected"';?>><?php echo 'mod_rewrite';?></option>-->
											</select>										
										</td>
										<td class="tdRestSetupRight"><a href="#" class="tooltip" title="<?php echo H_CLEANURL;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdRestSetupLeft"><?php echo URLSUFFIX;?></td>
										<td class="tdRestSetupCenter"><input type="text" name="url_suffix" value="<?php echo ec($site['url_suffix']);?>" /></td>
										<td class="tdRestSetupRight"><a href="#" class="tooltip" title="<?php echo H_URLSUFFIX;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>									
								</table>

							</div>

						</div>

						<div id="webSiteRight">	

							<div id="searchEngine">
								<h2><?php echo TRACKINGANDADDURL;?></h2>

								<div id="innerEngine">
								
                                    <table  cellpadding="0" cellspacing="0" border="0">                       
                                        <thead>
                                            <tr>
                                                <th class="thLeft"><?php echo GACODE.':';?></th>
                                                <th class="thRight">
                                                <a href="#" class="tooltip" title="<?php echo H_GACODE;?>"><img src="images/help.gif" class="imgover" title="" alt="" /></a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="tdPic"  colspan="2"><textarea cols="5" rows="5" name="gacode"><?php echo ec($site['gacode']);?></textarea></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">
                                                
                                                    <ul>
                                                        <li><a href="http://www.google.fi/intl/fi/add_url.html" target="_blank" class="google"><?php echo ADDURL;?> - Google</a></li>
                                                        <li><a href="http://siteexplorer.search.yahoo.com/submit" target="_blank" class="yahoo"><?php echo ADDURL;?> - Yahoo</a></li>
                                                        <li><a href="http://www.bing.com/docs/submit.aspx" target="_blank" class="msn"><?php echo ADDURL;?> - Bing (MSN)</a></li>
                                                        <li><a href="http://www.dmoz.org/" target="_blank" class="dmoz">Open directory</a></li>                                                                
                                                    </ul>                                                
                                                
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>								
								</div>							
							</div>
						
							<div id="newsWebSettings">
							
								<h2><?php echo NEWSSETTINGS;?></h2>
								
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="tdNewsSetupLeft"><input type="checkbox" class="checkbox" name="news_readmore"<?php if($site['news_readmore'] == 1) echo ' checked="checked"';?> /></td>
										<td class="tdNewsSetupCenter"><?php echo READMORELINK;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_READMORELINK;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdNewsSetupLeft"><input type="checkbox" class="checkbox" name="news_showdate"<?php if($site['news_showdate'] == 1) echo ' checked="checked"';?> /></td>
										<td class="tdNewsSetupCenter"><?php echo SHOWDATE;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_SHOWDATE;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdNewsSetupLeft"><input type="checkbox" class="checkbox" name="news_textlink"<?php if($site['news_textlink'] == 1) echo ' checked="checked"';?> /></td>
										<td class="tdNewsSetupCenter"><?php echo NEWSTEXTASLINK;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_NEWSTEXTASLINK;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdNewsSetupLeft"><input type="text" name="news_readmoretext" value="<?php echo ec($site['news_readmoretext']);?>" /></td>
										<td class="tdNewsSetupCenter"><?php echo READMORETEXT;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_READMORETEXT;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdNewsSetupLeft"><input type="text" name="news_maxshort" value="<?php echo $site['news_maxshort'];?>" /></td>
										<td class="tdNewsSetupCenter"><?php echo SHORTCHARNUM;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_SHORTCHARNUM;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
									<tr>
										<td class="tdNewsSetupLeft">
											<select name="news_num">
												<?php
												for ($i=1;$i<=12;$i++) {
													echo '<option value="'.$i.'"'.($i == $site['news_num'] ? ' selected="selected"' : '').'>'.$i.'</option>';
												}?>
											</select>										
										</td>
										<td class="tdNewsSetupCenter"><?php echo NEWSSHOWNUM;?></td>
										<td class="tdNewsSetupRight"><a href="#" class="tooltip" title="<?php echo H_NEWSSHOWNUM;?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
									</tr>
								</table>
				
							</div>

						</div>

						<div class="clear"></div> <!-- This div clears the float divs --> 							

					</div>


				</div>
				
				</form>

				<div class="clear"></div> <!-- This div clears the float divs --> 

            </div>

<?php require('includes/footer.php');?>
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
$cmspage = LOG;
if ($_SESSION['level'] != 5) die(NOTALLOWED);

if (query_single("SELECT COUNT(*) FROM dl_count") != 0) {
	if (isset($_GET['p'])) {
		if (isset($_GET['d'])) {
			// some CSRF protection
			if (!valid_csrf_token($_GET['token'])) die(NOTALLOWED);
			// delete selected count
			$stmt = $dbh->prepare("DELETE FROM dl_count WHERE filename = ?");
			$stmt->bindParam(1, $_GET['p']);
			$stmt->execute();
			save_infomsg(COUNT.' '.$_GET['p'].' '.DELETED);
			header('Location: '.ec($_SERVER['PHP_SELF']));
		}
	}
}

require('includes/head.php');
$token = csrf_token();
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/pagemenu.php');?>


			<!-- **************** RightDiv ******************** -->    
				<form method="post" action="<?php echo ec($_SERVER['PHP_SELF']).'?'.ec($_SERVER['QUERY_STRING']);?>" name="newsEditForm">
				<input type="hidden" name="save" value="1" />
				<input type="hidden" name="ordernum" value="<?php echo $page['ordernum'];?>" />
                <div id="rightDiv">
					<div id="buttonBar">
					</div>

					<div id="rightContent">					

						<div id="newsAdmin">

							<h2><?php echo DOWNLOADSTATS;?></h2>
							
								<?php 
								
								// pagination
								$rowsperpage = ROWSPERPAGE;
								if (isset($_GET['page'])) $page = intval($_GET['page']);
								else $page = 1;
								$start = $rowsperpage * ($page-1);
								$total = query_single("SELECT COUNT(*) FROM dl_count");
								$lastpage = ceil($total/$rowsperpage);

								// correct self also considering the ordering
								$self = ec($_SERVER['PHP_SELF']).'?';
								if (isset($_GET['order'])) $self .= 'order='.$_GET['order'];
								else $self .= 'order=1';
								if (isset($_GET['desc']) OR !isset($_GET['order'])) $self .= '&amp;desc=1';
								
								// page links for navigations
								$nav  = '';
								for($i = 1; $i <= $lastpage; $i++) {
									if ($i == $page) $nav .= " $i "; // no need to create a link to current page
									else $nav .= " <a href=\"$self&amp;page=$i\">$i</a> ";
								}
								// first, next, previous & last links
								if ($page > 1) {
									$i  = $page - 1;
									$prev  = ' <a href="'.$self.'&amp;page='.$i.'">'.PREVIOUS.'</a> ';
									$first  = ' <a href="'.$self.'&amp;page=1">'.FIRSTPAGE.'</a> ';
								} else {
								   $prev  = '&nbsp;'; // we're on page one, don't print previous link
								   $first = '&nbsp;'; // nor the first page link
								}

								if ($page < $lastpage) {
								   $i = $page + 1;
									$next  = ' <a href="'.$self.'&amp;page='.$i.'">'.NEXT.'</a> ';
									$last  = ' <a href="'.$self.'&amp;page='.$lastpage.'">'.LASTPAGE.'</a> ';
								} else {
								   $next = '&nbsp;'; // we're on the last page, don't print next link
								   $last = '&nbsp;'; // nor the last page link
								}						

								// ordering the data
								if ($_GET['order'] == 1) $order = 'filename';
								elseif ($_GET['order'] == 2) $order = 'downloads';
								elseif ($_GET['order'] == 3) $order = 'count_started';
								elseif ($_GET['order'] == 4) $order = 'last_dl';
								else $order = 'downloads'; // default ordering
								if ($_GET['desc'] == 1 OR !isset($_GET['order'])) $desc = ' DESC'; // default to ORDER BY downloads DESC!
								else $desc = '';
								
								if ($lastpage > 1) echo '<p>'.SHOWING.' '.($start+1).' - '.($start+$rowsperpage > $total ? $total : $start+$rowsperpage).' '.sprintf(OFTOTALRESULTS, $total).'</p>';

								echo '<table class="logtable">';
								echo '<tr><th><a href="'.($_GET['desc'] ? '?order=1' : '?order=1&amp;desc=1').'">'.FILENAME.'</a></th><th><a href="'.($_GET['desc'] ? '?order=2' : '?order=2&amp;desc=1').'">'.DOWNLOADS.'</a></th><th><a href="'.($_GET['desc'] ? '?order=3' : '?order=3&amp;desc=1').'">'.COUNTSTARTED.'</a></th><th><a href="'.($_GET['desc'] ? '?order=4' : '?order=4&amp;desc=1').'">'.LASTDOWNLOAD.'</a></th><th></th></tr>';

								$sql = "SELECT * FROM dl_count ORDER BY ".$order.$desc." LIMIT ".$start.", ".$rowsperpage;
								foreach ($dbh->query($sql) as $row) {
									echo '<tr><td>'.$row['filename'].'</td><td>'.$row['downloads'].'</td><td>'.$row['count_started'].'</td><td>'.$row['last_dl'].'</td><td><a href="dlcount.php?p='.$row['filename'].'&amp;d=1&amp;token='.$token.'">'.RESET.'</a></td></tr>';
								}

								echo '</table>';
								// print the navigation links for pagination
								if ($lastpage > 1) echo '<p>'.$prev.$nav.$next.'</p>';
								echo '<p><a href="dllog.php">'.DOWNLOADLOG.'</a></p>';
								?>

						</div>

					</div>

				</div>
			        
				<div class="clear"></div> <!-- This div clears the float divs --> 

				</form>

			</div>

<?php require('includes/footer.php');?>
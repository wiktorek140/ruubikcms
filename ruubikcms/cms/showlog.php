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

require('includes/head.php');
$self = ec($_SERVER['PHP_SELF']);
?>

        <!-- **************** MAINBODY ******************** -->
                
			<div id="mainBody">
			
			<?php require('includes/pagemenu.php');?>


			<!-- **************** RightDiv ******************** -->    
				<form method="post" action="<?php echo $self.'?'.ec($_SERVER['QUERY_STRING']);?>" name="newsEditForm">
				<input type="hidden" name="save" value="1" />
				<input type="hidden" name="ordernum" value="<?php echo $page['ordernum'];?>" />
                <div id="rightDiv">
					<div id="buttonBar">
					</div>

					<div id="rightContent">					

						<div id="newsAdmin">

							<h2><?php echo LOG;?></h2>
								
								<?php

								// pagination
								$rowsperpage = ROWSPERPAGE;
								if (isset($_GET['page'])) $page = intval($_GET['page']);
								else $page = 1;
								$start = $rowsperpage * ($page-1);
								$total = query_single("SELECT COUNT(*) FROM log");
								$lastpage = ceil($total/$rowsperpage);

								// correct self also for ordering
								$slf = ec($_SERVER['PHP_SELF']).'?';
								if (isset($_GET['order'])) $slf .= 'order='.ec($_GET['order']);
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

								// ordering the log
								if ($_GET['order'] == 1) $order = 'time';
								elseif ($_GET['order'] == 2) $order = 'msg';
								elseif ($_GET['order'] == 3) $order = 'user';
								elseif ($_GET['order'] == 4) $order = 'ip';
								else $order = 'time'; // default ordering
								if ($_GET['desc'] == 1) $desc = ' DESC';
								else $desc = '';
								if (!isset($_GET['order']) AND !isset($_GET['desc'])) $desc = ' DESC'; // here default by time desc

								if ($lastpage > 1) echo '<p>'.SHOWING.' '.($start+1).' - '.($start+$rowsperpage > $total ? $total : $start+$rowsperpage).' '.sprintf(OFTOTALRESULTS, $total).'</p>';

								echo '<table class="logtable">';

								echo '<tr><th><a href="'.($_GET['desc'] ? '?order=1' : '?order=1&amp;desc=1').'">'.TIME.'</a></th><th><a href="'.($_GET['desc'] ? '?order=2' : '?order=2&amp;desc=1').'">'.MESSAGE.'</a></th><th><a href="'.($_GET['desc'] ? '?order=3' : '?order=3&amp;desc=1').'">'.USERNAME.'</a></th><th><a href="'.($_GET['desc'] ? '?order=4' : '?order=4&amp;desc=1').'">IP</a></th></tr>';

								$sql = "SELECT time, msg, user, ip FROM log ORDER BY ".$order.$desc." LIMIT ".$start.", ".$rowsperpage;

								foreach ($dbh->query($sql) as $row) {

									echo '<tr><td>'.$row['time'].'</td><td>'.$row['msg'].'</td><td>'.$row['user'].'</td><td>'.$row['ip'].'</td></tr>';

								}

								echo '</table>';
								
								// print the navigation links for pagination
								if ($lastpage > 1) echo '<p>'.$prev.$nav.$next.'</p>';

								//echo '<p style="padding: 5px;"><a href="cmsoptions.php" style="color:#fff;"><< '.CMSOPTIONS.'</a></p>';
								echo '<p><a href="cmsoptions.php"><< '.CMSOPTIONS.'</a></p>';

							?>

						</div>

					</div>

				</div>
			        
				<div class="clear"></div> <!-- This div clears the float divs --> 

				</form>

			</div>

<?php require('includes/footer.php');?>
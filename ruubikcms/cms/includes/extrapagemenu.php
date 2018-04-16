<?php if (basename($_SERVER['REQUEST_URI']) == 'extrapagemenu.php') die ('Access denied'); ?>
<!-- **************** leftDiv (rootMenu) ******************** -->
                <div id="leftDiv">
                    <div class="blueHeader">
                        <?php echo EXTRAPAGES;?>
                        <div id="showAll">&nbsp;</div>
                        <div id="showMain">&nbsp;</div>
                    </div>
                    <div id="pageManagement">
			<div id="rootMenu">
                            <?php
                            if (isset($_GET['p'])) $p = ec($_GET['p']);
                            else $p = '';

                            // loop pages for pagemenu
                            $sql = "SELECT pageurl, name FROM extrapage WHERE levelnum = 1 ORDER BY ordernum";

                            foreach ($dbh->query($sql) as $row) {
                                    echo '  <div class="arrowdiv1"><a href="extranet.php'.'?p='.$row['pageurl'].'&amp;moveup=1&amp;token='.$token.'"><img src="images/arrow1.gif" class="imgover" alt="arrow" /></a></div>
                                            <div class="rootPage"><a href="extranet.php'.'?p='.$row['pageurl'].'"'.($row['pageurl'] == root_page($p, 'extrapage') ? ' class="selected"' : '').'>'.ec($row['name']).'</a></div>
                                            <div class="subMenu1"'.($row['pageurl'] == root_page($p, 'extrapage') ? ' id="open"' : '').'>';

                                    $sql2 = "SELECT pageurl, name FROM extrapage WHERE levelnum = 2 AND mother = '".$row['pageurl']."' ORDER BY ordernum";

                                    foreach ($dbh->query($sql2) as $row2) {
                                            echo '  <div class="subPage1"><div class="arrowdiv2"><a href="extranet.php'.'?p='.$row2['pageurl'].'&amp;moveup=1&amp;token='.$token.'"><img src="images/arrow2.gif" class="imgover" alt="arrow" /></a></div>
                                                    <div class="subButton1"><a href="extranet.php'.'?p='.$row2['pageurl'].'"'.($row2['pageurl'] == $p ? ' class="selected"' : '').'>'.ec($row2['name']).'</a></div></div>';

                                            $sql3 = "SELECT pageurl, name FROM extrapage WHERE levelnum = 3 AND mother = '".$row2['pageurl']."' ORDER BY ordernum";

                                            foreach ($dbh->query($sql3) as $row3) {
                                                    echo ' <div class="subPage2"><div class="arrowdiv2"><a href="extranet.php'.'?p='.$row3['pageurl'].'&amp;moveup=1&amp;token='.$token.'"><img src="images/arrow2.gif" class="imgover" alt="arrow" /></a></div>
                                                            <div class="subButton2"><a href="extranet.php'.'?p='.$row3['pageurl'].'"'.($row3['pageurl'] == $p ? ' class="selected"' : '').'>'.ec($row3['name']).'</a></div></div>';
                                            }
                                    }
                                    echo ' </div> <!-- end subMenu1 div-->';
                            }
                            // loop free pages
                            echo '          <div class="rootPageFree"><a href="extranet.php'.'?p=---notinmenu---">'.FREEPAGES.'</a></div>
                                            <div class="subMenu1"'.(($p == '---notinmenu---' OR root_page($p, 'extrapage') == '---notinmenu---') ? ' id="open"' : '').'>';

                            $sql = "SELECT pageurl, name FROM extrapage WHERE levelnum = 0 ORDER BY ordernum";
                            foreach ($dbh->query($sql) as $row) {
                                    echo '          <div class="subPage1"><div class="arrowdiv2"><a href="extranet.php'.'?p='.$row['pageurl'].'&amp;moveup=1&amp;token='.$token.'"><img src="images/arrow2.gif" class="imgover" alt="arrow" /></a></div>
                                                    <div class="subButton1"><a href="extranet.php'.'?p='.$row['pageurl'].'"'.($row['pageurl'] == $p ? ' class="selected"' : '').'>'.ec($row['name']).'</a></div></div>';
                                    }
                            echo '          </div>';
                            ?>
                        </div>
                    </div>
                </div>
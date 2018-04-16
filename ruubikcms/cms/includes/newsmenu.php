<?php if (basename($_SERVER['REQUEST_URI']) == 'newsmenu.php') die ('Access denied'); ?>
<!-- **************** leftDiv (rootMenu) ******************** -->    	   
                <div id="leftDiv">
                    <div class="blueHeader"><?php echo NEWS;?></div> 

                    <div id="pageManagement">
                        <div id="rootMenuNews"><!-- rootMenu begins -->
                            <?php
                            if (!isset($_GET['y'])) $_GET['y'] = '';
                            /*
                            echo '
                            <div class="arrowdiv1"><a href="#"><img src="images/arrow1.gif" class="imgover" alt="arrow" /></a></div>
                            <div class="rootPage"><a href="news.php?y=latest">Latest news</a></div>
                            <div class="subMenu1"'.($_GET['y'] == 'latest' ? ' id="open"' : '').'>';

                            $sql = "SELECT id, title, STRFTIME('%d.%m.%Y',time) as date FROM news ORDER BY time DESC LIMIT 10";
                            foreach ($dbh->query($sql) as $row) {
                                    echo '
                                    <div class="subPage1"><div class="arrowdiv2"><a href="#"><img src="images/arrow2.gif" class="imgover" alt="arrow" /></a></div>
                                    <div class="subButton1"><a href="news.php'.'?id='.$row['id'].'">'.$row['date'].': '.$row['title'].'</a></div></div>';
                            }

                            echo '
                            </div>';
                            */

                            // get years for news
                            $sql = "SELECT STRFTIME('%Y', time) AS year FROM news GROUP BY year ORDER BY year DESC";
                            foreach ($dbh->query($sql) as $row) {
                                echo '  <!--<div class="arrowdiv1"><a href="#"><img src="images/arrow1.gif" class="imgover" alt="arrow" /></a></div>-->
                                        <div class="newsButton"><a href="news.php?y='.$row['year'].'">'.$row['year'].'</a></div>
                                        <div class="subMenu1"'.($row['year'] == $_GET['y'] ? ' id="open"' : '').'>';

                                $sql2 = "SELECT id, title, STRFTIME('%d.%m.%Y',time) as date FROM news WHERE STRFTIME('%Y',time) = '".$row['year']."' ORDER BY time DESC";

                                foreach ($dbh->query($sql2) as $row2) {
                                    echo '  <div class="subPage1"><!--<div class="arrowdiv2"><a href="#"><img src="images/arrow2.gif" class="imgover" alt="arrow" /></a></div>-->
                                            <div class="subButton1"><a href="news.php'.'?y='.$row['year'].'&amp;id='.$row2['id'].'"'.($row2['id'] == $_GET['id'] ? ' class="selected"' : '').'>'.$row2['date'].': '.ec($row2['title']).'</a></div></div>';
                                }
                            echo '</div>';
                            }
                            ?>
                        </div> <!-- rootMenu ends -->
                    </div>			
                </div>
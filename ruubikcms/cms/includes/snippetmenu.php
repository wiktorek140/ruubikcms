			<!-- **************** leftDiv (rootMenu) ******************** -->    
			   
                <div id="leftDiv">
                    <div class="blueHeader"><?php echo SNIPPETS;?></div>

                    <div id="pageManagement">
                        <div id="rootMenuNews">
                            <?php
                            echo '  <div class="newsButton"><a href="#">TinyMCE</a></div>
                                    <div class="subMenu1">';

                            // loop tinymce snippets
                            $sql = "SELECT name FROM snippet WHERE tinymce = '1' ORDER BY name";

                            foreach ($dbh->query($sql) as $row) {
                                echo '  <div class="subPage1">
                                            <div class="subButton1"><a href="snippets.php'.'?p='.$row['name'].'"'.($row['name'] == $_GET['p'] ? ' class="selected"' : '').'>'.ec($row['name']).'</a></div>
                                        </div>';
                            }
                            echo '  </div>';
                            echo '  <div class="newsButton"><a href="#">'.CODE.'</a></div>
                                    <div class="subMenu1">';

                            // loop tinymce snippets
                            $sql = "SELECT name FROM snippet WHERE tinymce = '0' ORDER BY name";
                            foreach ($dbh->query($sql) as $row) {

                                echo '  <div class="subPage1">
                                            <div class="subButton1"><a href="snippets.php'.'?p='.$row['name'].'"'.($row['name'] == $_GET['p'] ? ' class="selected"' : '').'>'.ec($row['name']).'</a></div>
                                        </div>';
                            }
                            echo '  </div>';
                            ?>
                        </div>
                    </div>
                </div>

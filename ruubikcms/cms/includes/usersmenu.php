<?php
if (basename($_SERVER['REQUEST_URI']) == 'usermenu.php') die('Access denied');
if (strpos($_SERVER['REQUEST_URI'], 'usermenu.php') !== false) die("Access Denied");

echo '<div id="leftDiv">
                    <div class="blueHeader">' . USERS . '</div>

                    <div id="pageManagement">
                        <div id="rootMenuNews">';

if (!isset($_GET['role']))
    $_GET['role'] = 5;

echo '  <div class="newsButton"><a href="users.php?role=5">' . ADMINISTRATORS . '</a></div>
                                    <div class="subMenu1"' . ($_GET['role'] == 5 ? ' id="open"' : '') . '>';

// loop administrators
$sql = "SELECT username FROM cmsuser WHERE role = '5' ORDER BY username";
foreach ($dbh->query($sql) as $row) {
    echo '  <div class="subPage1">
                                                <div class="subButton1"><a href="users.php?role=5' . '&p=' . $row['username'] . '"' . ($row['username'] == $_GET['p'] ? ' class="selected"' : '') . '>' . $row['username'] . '</a></div>
                                            </div>';
}
echo '  </div>';
echo '      <div class="newsButton"><a href="users.php?role=4">' . SUPEREDITORS . '</a></div>
                                        <div class="subMenu1"' . ($_GET['role'] == 4 ? ' id="open"' : '') . '>';

// loop supereditors
$sql = "SELECT username FROM cmsuser WHERE role = '4' ORDER BY username";
foreach ($dbh->query($sql) as $row) {
    echo '  <div class="subPage1">
                                                <div class="subButton1"><a href="users.php?role=4' . '&p=' . $row['username'] . '"' . ($row['username'] == $_GET['p'] ? ' class="selected"' : '') . '>' . $row['username'] . '</a></div>
                                            </div>';
}
echo '  </div>';

echo '      <div class="newsButton"><a href="users.php?role=3">' . PUBLISHERS . '</a></div>
                                        <div class="subMenu1"' . ($_GET['role'] == 3 ? ' id="open"' : '') . '>';

// loop publishers
$sql = "SELECT username FROM cmsuser WHERE role = '3' ORDER BY username";
foreach ($dbh->query($sql) as $row) {
    echo '  <div class="subPage1">
                                                <div class="subButton1"><a href="users.php?role=3' . '&p=' . $row['username'] . '"' . ($row['username'] == $_GET['p'] ? ' class="selected"' : '') . '>' . $row['username'] . '</a></div>
                                            </div>';
}
echo '  </div>';
echo '      <div class="newsButton"><a href="users.php?role=2">' . CONTRIBUTORS . '</a></div>
                                        <div class="subMenu1"' . ($_GET['role'] == 2 ? ' id="open"' : '') . '>';

// loop publishers
$sql = "SELECT username FROM cmsuser WHERE role = '2' ORDER BY username";
foreach ($dbh->query($sql) as $row) {
    echo '  <div class="subPage1">
                                                <div class="subButton1"><a href="users.php?role=2' . '&p=' . $row['username'] . '"' . ($row['username'] == $_GET['p'] ? ' class="selected"' : '') . '>' . $row['username'] . '</a></div>
                                            </div>';
}
echo '  </div>

                        </div>
                    </div>			
                </div> ';
?>
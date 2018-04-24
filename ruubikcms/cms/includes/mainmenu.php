<?php if (basename($_SERVER['REQUEST_URI']) == 'mainmenu.php') die ('Access denied'); 
if (strpos($_SERVER['REQUEST_URI'], 'mainmenu.php') !== false) die("Access Denied");
?>
<?php 
                        if (SHOW_SITESETUP AND $_SESSION['level'] == 5) echo '
                        <li><a href="sitesetup.php"'.($filename == 'sitesetup.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'sitesetup.php' ? ' class="selectedMenu"' : '').'>'.SITESETUP.'</span></a></li>';
                        echo '<li><a href="index.php"'.($filename == 'index.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'index.php' ? ' class="selectedMenu"' : '').'>'.WEBPAGES.'</span></a></li>';
                        if (SHOW_NEWS) echo '
                        <li><a href="news.php"'.($filename == 'news.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'news.php' ? ' class="selectedMenu"' : '').'>'.NEWS.'</span></a></li>';
                        if (SHOW_SNIPPETS AND $_SESSION['level'] >= 4) echo '
                        <li><a href="snippets.php"'.($filename == 'snippets.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'snippets.php' ? ' class="selectedMenu"' : '').'>'.SNIPPETS.'</span></a></li>';
                        if (SHOW_USERS AND $_SESSION['level'] == 5) echo '
                        <li><a href="users.php"'.($filename == 'users.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'users.php' ? ' class="selectedMenu"' : '').'>'.USERS.'</span></a></li>';
                        if (SHOW_EXTRANET) echo '
                        <li><a href="extranet.php"'.(($filename == 'extranet.php' OR $filename == 'extrausers.php') ? ' class="selectedMenu"' : '').'><span'.(($filename == 'extranet.php' OR $filename == 'extrausers.php') ? ' class="selectedMenu"' : '').'>'.EXTRANET.'</span></a></li>';
                        if (SHOW_EXTRAUSERS AND $_SESSION['level'] == 5) echo '
                        <li><a href="extrausers.php"'.($filename == 'extrausers.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'extrausers.php' ? ' class="selectedMenu"' : '').'>'.EXTRAUSERS.'</span></a></li>';
                        if (SHOW_CMSOPTIONS AND $_SESSION['level'] == 5) echo '
                        <li><a href="cmsoptions.php"'.($filename == 'cmsoptions.php' ? ' class="selectedMenu"' : '').'><span'.($filename == 'cmsoptions.php' ? ' class="selectedMenu"' : '').'>'.CMSOPTIONS.'</span></a></li>';
?>
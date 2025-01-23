<?php if (
    basename((string) $_SERVER['REQUEST_URI']) == 'footer.php'
    || str_contains((string) $_SERVER['REQUEST_URI'], 'footer.php')
) {
    die('Access Denied');
} ?>
        <div id="footer">
            <div>
                <span class="leftalign">
                    <span class="whitetext"><?php echo VERSION . ' ' . VERNUM; ?></span>
                </span>
                <?php echo THANKYOUTEXT . ' <a href="http://www.ruubikcms.com/" target="_blank">RuubikCMS</a> | <a href="http://www.ruubikcms.com/index.php/documentation" target="_blank">' . DOCUMENTATION . '</a> | <a href="http://www.ruubikcms.com/forum/" target="_blank">' . FEEDBACK . '</a></div>'; ?>
            </div>
        </div>      
    </body>
</html>
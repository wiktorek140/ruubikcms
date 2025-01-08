<?php

require 'includes/required.php';
$cmspage = SNIPPETS;
if ($_SESSION['level'] < 4) {
    die(NOTALLOWED);
}

$self = ec($_SERVER['PHP_SELF']);

if (isset($_POST['save'])) {
    if (!valid_csrf_token($_POST['token'])) {
        die(NOTALLOWED);
    }

    if (!isset($_POST['tinyMCE'])) {
        $_POST['tinyMCE'] = '';
    }

    // at least some name must be defined
    if (!$_POST['name']) {
        $_POST['name'] = NONAME;
    }

    // type not selected if disabled (code)
    if (!$_POST['type']) {
        $_POST['type'] = '0';
    }

    // new snippet -> get unique name
    if (!isset($_GET['p']) and isset($_GET['n'])) {
        $newname = get_unique_url($_POST['name'], 1);
    }

    // page already exists
    if (isset($_GET['p'])) {
        // check if name changed
        if ($_POST['name'] != $_GET['p']) {
            // delete old before inserting new
            $stmt = $dbh->prepare("DELETE FROM snippet WHERE name = ?");
            $stmt->bindParam(1, $_GET['p']);
            $stmt->execute();
            $newname = get_unique_url($_POST['name'], 1);
            $namechanged = true;
        } else {
            // name not changed
            $newname = ec($_GET['p']);
        }
    }

    // insert or update snippet
    $content = stripslashes($_POST['tinyMCE']);
    $stmt = $dbh->prepare("INSERT OR REPLACE INTO snippet (name, content, tinymce) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $newname);
    $stmt->bindParam(2, $content);
    $stmt->bindParam(3, $_POST['type']);
    $stmt->execute();
    header('Location: ' . $self . '?p=' . $newname);
    // redirect to new snippet
    save_infomsg(SNIPPET . ' ' . SAVED);
}//end if

if (query_single("SELECT COUNT(*) FROM snippet") != 0) {
    if (isset($_GET['p'])) {
        if (isset($_GET['d'])) {
            if (!valid_csrf_token($_GET['token'])) {
                die(NOTALLOWED);
            }

            // delete requested snippet
            $stmt = $dbh->prepare("DELETE FROM snippet WHERE name = ?");
            $stmt->bindParam(1, $_GET['p']);
            $stmt->execute();
            save_infomsg(SNIPPET . ' ' . DELETED);
            header('Location: ' . $self);
        }

        // get snippet data from database
        $stmt = $dbh->prepare("SELECT name, content, tinymce FROM snippet WHERE name = ?");
        if ($stmt->execute([$_GET['p']])) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $snippet = $result[0];
        }
    } else if (!isset($_GET['n'])) {
        // no snippet given and not creating new page etc ->  redirect first snippet
        $loc = query_single("SELECT name FROM snippet WHERE tinymce = '1' ORDER BY name LIMIT 1");
        if (!$loc) {
            $loc = query_single("SELECT name FROM snippet WHERE tinymce = '0' ORDER BY name LIMIT 1");
        }

        header('Location: ' . $self . '?p=' . $loc);
    }//end if
}//end if

require 'includes/head.php';
if (!isset($snippet['tinymce'])) {
    $snippet['tinymce'] = 1;
}

$token = csrf_token();
?>

<div id="mainBody">
  <?php require 'includes/snippetmenu.php'; ?>

  <form method="post" action="<?php echo $self . '?' . ec($_SERVER['QUERY_STRING']); ?>" name="newsEditForm">
    <input type="hidden" name="save" value="1" />
  <input type="hidden" name="ordernum" value="<?php echo $page['ordernum']; ?>" />
<div id="rightDiv">
  <div id="buttonBar">
    <ul>
      <?php if (isset($_GET['p']) || isset($_GET['n'])) {
            ?><li><a href="javascript:document.newsEditForm.submit();" class="save"><span><?php echo SAVE; ?></span></a></li>
            <?php
      }
        ?>
      <li><a href="<?php echo $self . '?n=1'; ?>" class="new"><span><?php echo RNEW; ?></span></a></li>
      <?php
        if (isset($_GET['p'])) {
            ?><li><a href="<?php echo $self . '?' . ec($_SERVER['QUERY_STRING']) . '&amp;d=1&amp;token=' . $token; ?>" class="delete"><span><?php echo DELETE; ?></span></a></li>
            <?php
        }
        ?>
      <input name="token" type="hidden" value="<?php echo $token; ?>" />
  </ul>
</div>

<div id="rightContent">

  <div id="newsAdmin">

    <h2><?php echo SNIPPETS; ?></h2>

    <table cellspacing="0" cellpadding="0" border="0" class="newsTable">
      <tr>
        <td class="tdNewsAdminLeft"><?php echo NAME; ?></td>
        <td class="tdNewsAdminCenter"><input type="text" name="name" value="<?php echo isset($snippet['name']) ? ec($snippet['name']) : '';
        ?>" /></td>
      <td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_SNIPPETNAME; ?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
  </tr>
  <tr>
    <td class="tdNewsAdminLeft"><?php echo TYPE; ?></td>
    <td class="tdNewsAdminCenter">
      <select name="type"<?php
        if ($snippet['tinymce'] == 0 and isset($_GET['p'])) {
            echo ' disabled="disabled"';
        }
        ?>>
        <option value="1"<?php
        if ($snippet['tinymce'] == 1 || !isset($_GET['p'])) {
            echo ' selected="selected"';
        }
        ?>>TinyMCE</option>
        <option value="0"<?php
        if ($snippet['tinymce'] == 0 and isset($_GET['p'])) {
            echo ' selected="selected"';
        }
        ?>><?php echo CODE; ?></option>
      </select>
    </td>
    <td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_SNIPPETTYPE; ?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
</tr>
</table>
<?php
if (!isset($_GET['n'])) {
    if ($snippet['tinymce'] == 1) {
        echo '<div id="tinyMCE"><textarea cols="63" rows="20" name="tinyMCE" class="tinyMCE">' . htmlentities($snippet['content']) . '</textarea></div>';
    } else {
        echo '<div id="tinyMCE"><textarea cols="63" rows="20" name="tinyMCE">' . htmlentities($snippet['content']) . '</textarea></div>';
    }
    ?>

<table cellspacing="0" cellpadding="0" border="0" class="newsTable">
<tr>
<td class="tdNewsAdminLeft"><?php echo CODE; ?></td>
<td class="tdNewsAdminCenter"><input type="text" name="copycode" readonly="readonly" value="&lt;?php snippet('<?php echo ec($_GET['p']); ?>');?&gt;" /></td>
<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_SNIPPETCODE; ?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
</tr>
    <?php if ($snippet['tinymce'] == 0) {
        ?>
<tr>
<td class="tdNewsAdminLeft"><?php echo CODE; ?> (PHP)</td>
<td class="tdNewsAdminCenter"><input type="text" name="copyphpcode" readonly="readonly" value="&lt;?php snippet_php('<?php echo ec($_GET['p']); ?>');?&gt;" /></td>
<td class="tdNewsAdminRight"><a href="#" class="tooltip" title="<?php echo H_SNIPPETCODEPHP; ?>"><img src="images/help.gif" class="imgover" alt="" /></a></td>
</tr>
        <?php
    }
    ?>
</table>

    <?php
}//end if
?>

</div>

</div>

</div>

<div class="clear"></div>
<!-- This div clears the float divs -->

</form>

</div>

<?php require 'includes/footer.php';
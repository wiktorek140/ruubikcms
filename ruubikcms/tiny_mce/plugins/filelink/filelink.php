<?php require_once('config_filelink.php');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#filelink_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="../tinybrowser/tb_standalone.js.php"></script>
	<script type="text/javascript" src="../../utils/form_utils.js"></script>
	<script type="text/javascript" src="js/filelink.js"></script>
</head>
<body>

<form onsubmit="FilelinkDialog.insert();return false;" action="#">

	<h3>Insert file</h3>	
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td><label for="filenameid">{#filelink_dlg.filename}</label></td>
			<td>
				<input type="text" id="filenameid" name="filenameid" style="width: 280px" />
				<a href="#" onclick="tinyBrowserPopUp('file','filenameid');">{#filelink_dlg.browse}</a>
			</td>
			
		</tr>
		<tr>
			<td><label for="filenameid">{#filelink_dlg.filesize}</label></td>
			<td><input type="text" id="filesizeid" name="filesizeid" style="width: 75px" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><input id="showfilesize" name="showfilesize" class="checkbox" type="checkbox" checked="checked"/></td>
						<td><label for="showfilesize">{#filelink_dlg.showsize}</label></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	
	<?php if ($filelink_cfg['autolaunch_filebrowser'] == True) echo '<script type="text/javascript">tinyBrowserPopUp(\'file\',\'filenameid\');</script>';?>

	<!--<p>Custom arg: <input id="somearg" name="somearg" type="text" class="text" /></p>-->

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="FilelinkDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

</body>
</html>

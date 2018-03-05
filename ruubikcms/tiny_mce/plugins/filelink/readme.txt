Filelink plugin for TinyMCE 3.x
Author: Iisakki Pirilä <iisakki[at]piuha.fi>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
    
Simple plugin for TinyMCE 3.x for inserting file download links and
an option to add filesize.

This plugin is intended to use with Tinybrowser, a custom file browser:
    
http://www.lunarvis.com/products/tinymcefilebrowserwithupload.php

Installation:

1. Copy filelink directory in tinymce plugins directory
2. Add filelink to plugins and buttons in TinyMCE.init:

Initialization example:

tinyMCE.init({
	theme : "advanced",
	mode : "textareas",
	plugins : "filelink",
	theme_advanced_buttons3_add : "filelink"
});

Optional: In config_filelink.php you can get Tinybrowser to launch automatically 
when filelink plugin is started.


I made little changes to Tinybrowser to get it working with filelink plugin:

"tinybrowser.php.js" lines 49-60:

FROM:

	function selectURL(url) {
	opener.document.getElementById("<?php echo $_GET['feid']; ?>").value = url;
	// Set img source of element id, if img id exists (format is elementid + "img")
	if(typeof(opener.document.getElementById("<?php echo $_GET['feid']; ?>img")) != "undefined" && opener.document.getElementById("<?php echo $_GET['feid']; ?>img").src.length != 0)
	   {
		opener.document.getElementById("<?php echo $_GET['feid']; ?>img").src = url;
		}
	self.close();
	}

TO:

	function selectURL(url,filesize) { //Added passing filesize
	opener.document.getElementById("<?php echo $_GET['feid']; ?>").value = url;
	opener.document.getElementById("filesizeid").value = filesize; //Added passing filesize
	// Set img source of element id, if img id exists (format is elementid + "img")
	// Commented this out because self.close(); was not actually closing window. Strange... 
	//if(typeof(opener.document.getElementById("<?php echo $_GET['feid']; ?>img")) != "undefined" && opener.document.getElementById("<?php echo $_GET['feid']; ?>img").src.length != 0)
	//   {
	//	opener.document.getElementById("<?php echo $_GET['feid']; ?>img").src = url;
	//	}
	self.close();
	}
	
"tinybrowser.php" line  291

FROM:

else echo '<td><a href="#" onclick="selectURL(\''.$linkpath.$file['name'][$i].'\');" title="'.$file['name'][$i].'">'.truncate_text($file['name'][$i],30).'</a></td>';

TO:

else echo '<td><a href="#" onclick="selectURL(\''.$linkpath.$file['name'][$i].'\',\''.bytestostring($file['size'][$i],1).'\');" title="'.$file['name'][$i].'">'.truncate_text($file['name'][$i],30).'</a></td>';



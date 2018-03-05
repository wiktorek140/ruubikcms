tinyMCEPopup.requireLangPack();

var FilelinkDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		f.filenameid.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		//f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
	},

	insert : function() {
		
		// Parse filename from full path
		var path = document.forms[0].filenameid.value;
		var parts = path.split('/');
		var filename = parts[parts.length-1];
		// Parse link
		var html = '<a href="' + path + '">' + filename + '</a>';
		// Add filesize if checked
		if (document.forms[0].showfilesize.checked == true) {
			html = html + '&nbsp[' + document.forms[0].filesizeid.value + ']';
		}

		// Insert the contents from the input into the document
		tinyMCEPopup.editor.execCommand('mceInsertContent', false, html);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(FilelinkDialog.init, FilelinkDialog);

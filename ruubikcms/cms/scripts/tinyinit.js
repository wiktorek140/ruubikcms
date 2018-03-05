tinyMCE.init({
	mode : "textareas",
	editor_selector : "tinyMCE",
	theme : "advanced",
	content_css : "../website/css/editor_content.css",
	plugins : "table,contextmenu,paste,advimage,searchreplace,fullscreen,advlink,media,filelink",
	convert_urls : false,
	relative_urls : false,
	theme_advanced_buttons1 : "undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,indent,outdent,separator,choosecolor,forecolor,hr,charmap,separator,sub,sup,separator,pastetext,pasteword",
	theme_advanced_buttons2 : "formatselect,styleselect,fontsizeselect,separator,removeformat,code,separator,table,visualaid,separator,link,unlink,separator,image,media,filelink,separator,fullscreen",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_blockformats : "p,pre,div,h1,h2,h3,h4,h5,h6",
	theme_advanced_styles : "Lightbox Link=lightbox;Gallery Image=gallery; Clear Gallery Float=clearfloat",
	extended_valid_elements : "a[class|name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],form[name|method|action|id],input[name|type|value|id|disabled|size|maxlength|class],textarea[name|cols|rows|id],select[name|id],option[selected|value],iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",
	file_browser_callback : "tinyBrowser"
});
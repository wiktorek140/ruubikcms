/*tinyMCE.init({
	language : "pl",
	mode : "textareas",
	editor_selector : "tinyMCE",
	theme : "advanced",
	content_css : "../website/css/editor_content.css",
	plugins: "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	convert_urls : false,
	relative_urls : false,
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	//theme_advanced_buttons1 : "undo,redo,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,indent,outdent,separator,choosecolor,forecolor,hr,charmap,separator,sub,sup,separator,pastetext,pasteword",
	//theme_advanced_buttons2 : "formatselect,styleselect,fontsizeselect,separator,removeformat,code,separator,table,visualaid,separator,link,unlink,separator,image,media,filelink,separator,fullscreen",
	//theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_blockformats : "p,pre,div,h1,h2,h3,h4,h5,h6",
	theme_advanced_styles : 'Lightbox Link=lightbox;Gallery Image=gallery; Clear Gallery Float=clearfloat',
	extended_valid_elements : "a[class|name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],form[name|method|action|id],input[name|type|value|id|disabled|size|maxlength|class],textarea[name|cols|rows|id],select[name|id],option[selected|value],iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",
	template_external_list_url : "js/template_list.js",
	external_link_list_url : "js/link_list.js",
	external_image_list_url : "js/image_list.js",
	media_external_list_url : "js/media_list.js",
	file_browser_callback : "tinyBrowser"
});*/

tinymce.init({
	
        selector: "textarea.tinyMCE",
        init_instance_callback : function(editor) {
            console.log("Editor: " + editor.id + " is now initialized.");
        },
        plugins: "spellchecker pagebreak table save hr image link emoticons insertdatetime preview media searchreplace print contextmenu paste directionality fullscreen noneditable visualchars nonbreaking template code",
		branding: false,
        resize: 'both',
        skin:"lightgray",
        
        toolbar1 : "save newdocument | bold italic underline strikethrough | justifyleft justifycenter justifyright justifyfull | styleselect formatselect fontselect fontsizeselect",
		toolbar2 : "cut copy paste pastetext pasteword | search replace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image cleanup help code | insertdate inserttime preview | forecolor backcolor",
		toolbar3 : "tablecontrols | hr removeformat visualaid | sub sup | charmap emotions iespell media advhr | print | ltr rtl | fullscreen",
		toolbar4 : "insertlayer moveforward movebackward absolute | styleprops spellchecker | cite abbr acronym del ins attribs | visualchars nonbreaking template blockquote pagebreak | insertfile insertimage",

        content_css : "../website/css/editor_content.css",
        extended_valid_elements : "a[class|name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],form[name|method|action|id],input[name|type|value|id|disabled|size|maxlength|class],textarea[name|cols|rows|id],select[name|id],option[selected|value],iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",
	styles : "Lightbox Link=lightbox;Gallery Image=gallery; Clear Gallery Float=clearfloat",
	block_formats : "p,pre,div,h1,h2,h3,h4,h5,h6",
        
	convert_urls : false,
	relative_urls : false,
	
	target_list: [
    {title: 'None', value: ''},
    {title: 'Same page', value: '_self'},
    {title: 'New page', value: '_blank'},
    {title: 'Lightbox', value: '_lightbox'}
  ],
	
	rel_list: [
    {title: 'Lightbox', value: 'lightbox'},
    {title: 'Table of contents', value: 'toc'}
  ],
	
	file_browser_callback: RoxyFileBrowser
});

//function responsible for loading roxyfilebrowser
function RoxyFileBrowser(field_name, url, type, win) {
    var roxyFileman = '/fileman/index.html'; if (roxyFileman.indexOf("?") < 0) {     roxyFileman += "?type=" + type;   } else {    roxyFileman += "&type=" + type; } roxyFileman += '&input=' + field_name + '&value=' + win.document.getElementById(field_name).value; if(tinyMCE.activeEditor.settings.language){ roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language; } tinyMCE.activeEditor.windowManager.open({     file: roxyFileman,     title: 'Roxy Fileman',     width: 850,     height: 650,     resizable: "yes",     plugins: "media",     inline: "yes",     close_previous: "no"   }, {     window: win,     input: field_name    }); return false; }
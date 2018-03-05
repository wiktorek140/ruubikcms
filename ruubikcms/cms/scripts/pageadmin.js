$(document).ready(function(){

	$('#xtrArea').hide();
	$('#decorationPix').hide();
	
	$('#toggleExtracode').click(function() {
		$('#xtrArea').toggle(400);
		$('#decorationPix').hide();
		return false;
	});

	$('.toggleImages').click(function() {
		$('#decorationPix').toggle(400);
		$('#xtrArea').hide();
		//$("#tinyMCEarea").attr("style","width:544px;");
		return false;
	});
	
	//$('.tooltip').bgiframe();
		
	$("#delpic1").click(function() {
		$("#pic1img").attr("src","");
		$("#pic1").attr("value","");
	});
	
	$("#delpic2").click(function() {
		$("#pic2img").attr("src","");
		$("#pic2").attr("value","");
	});
	
	//$("dt").append(":");

	if ($("#pic1img").attr("src") != "") {
		var t = $("#pic1img").attr("src").match( /.*\// ) + "_thumbs/_" + $("#pic1img").attr("src").replace( /.*\//, "" );
		$("#pic1img").attr("src", t)
	}
	
	if ($("#pic2img").attr("src") != "") {
		var t = $("#pic2img").attr("src").match( /.*\// ) + "_thumbs/_" + $("#pic2img").attr("src").replace( /.*\//, "" );
		$("#pic2img").attr("src", t)
	}
	
});

function validate(f) {
	if(f.name.value=="") {
		alert('Page name is missing. Sivun nimi puuttuu.');
		return false;
	}
	return true;
}
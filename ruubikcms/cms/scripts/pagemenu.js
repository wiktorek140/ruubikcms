$(document).ready(function() {
	// click on tinyMCE clears message
	/*$('textarea').click(function() {
		$("div#messageText").append("test");
		//alert('test');
	})*/
	// clicking + shows divs
	$('#showAll').click(function() {
		$("div.subMenu1").show();
	})
	// clicking - hides divs
	$('#showMain').click(function() {
		$("div.subMenu1").hide();
	})
	// close all divs on page load
	$("div.subMenu1").hide();
	// show div with id open (slidedown effect)
	$('#open').slideDown('normal');	
});

$(function() {$(document).ready(function(){
	$(".v-black-rtl li").hover(
		function(){ $("ul", this).fadeIn("fast"); }, 
		function(){ $("ul", this).fadeOut("fast"); } 
	);

$('.v-black-rtl li:has(ul)').addClass('parent');});});
//options
	var rttheme_slider_time_out = jQuery("meta[name=rttheme_slider_time_out]").attr('content'); 
	var rttheme_template_dir = jQuery("meta[name=rttheme_template_dir]").attr('content');
	
	
//home page slider

jQuery(document).ready(function(){
	var slider_area;
	var slider_buttons;

	// Which slider
	if (jQuery('#slider_area').length>0){
		
		// Home Page Slider
		slider_area="#slider_area";	
		slider_buttons="#numbers";
	
	
		jQuery(slider_area).cycle({ 
			fx:     'fade', 
			timeout:  rttheme_slider_time_out,
			//easing: 'backout', 
			prev: '.prev', 
			next: '.next',
			cleartype:  1,
			pause:           true,     // true to enable "pause on hover"
			pauseOnPagerHover: true,   // true to pause when hovering over pager link				
			before:  onBefore, 
			after:   onAfter 
		});
		
	}

	if (jQuery('.product_detail').length>0){
		
		// Home Page Slider
		slider_area_pp=".product_photos";	

		jQuery(slider_area_pp).cycle({ 
			fx:     'fade', 
			timeout:  5000,
			pager:'.product_photos_pager',
			cleartype:  1
		});
		
	}
	
	
jQuery('.prev, .next').css({opacity:0});
jQuery('#slider').hover(function()
{
	jQuery('.prev, .next').stop().animate({opacity:1},400);
},
function()
{
	jQuery('.prev, .next').stop().animate({opacity:0},400);
});


function onBefore() { 
	jQuery('.desc').stop().animate({opacity:0.9},400);
} 
function onAfter() { 
	jQuery('.desc').stop().animate({opacity:0.9},400);
}

	
});



//pretty photo
jQuery(document).ready(function(){
	jQuery("a[rel^='prettyPhoto']").prettyPhoto();
});


//image effects 
jQuery(document).ready(function(){
		var image_e= jQuery(".image.portf, .image.product_image");
		image_e.mouseover(function(){jQuery(this).stop().animate({ opacity:0.4
				}, 400);
		}).mouseout(function(){
			image_e.stop().animate({ 
				opacity:1
				}, 400 );
		});
});

//validate contact form
jQuery(document).ready(function(){

	// show a simple loading indicator
	var loader = jQuery('<img src="'+rttheme_template_dir+'/images/loading.gif" alt="loading..." />')
		.appendTo(".loading")
		.hide();
	jQuery().ajaxStart(function() {
		loader.show();
	}).ajaxStop(function() {
		loader.hide();
	}).ajaxError(function(a, b, e) {
		throw e;
	});
	
	jQuery.validator.messages.required = "";
	var v = jQuery("#validate_form").validate({
		submitHandler: function(form) {
			jQuery(form).ajaxSubmit({
				target: "#result"
			});
		}
	});
	
	jQuery("#reset").click(function() {
		v.resetForm();
	});
 });



//cufon fonts
var rttheme_disable_cufon= jQuery("meta[name=rttheme_disable_cufon]").attr('content');

jQuery(document).ready(function(){
	if(rttheme_disable_cufon!='true') {	
		var cufon_list="h1,h2,h3,h4,h5,h6"
		var rt_cufon_class = jQuery(cufon_list);	
	
		rt_cufon_class.each(function(){
			 jQuery(this).addClass('cufon');
		});
	
		Cufon.replace(cufon_list, {hover: true});
	}
});
	

//Add Class to first li items of top and footer menus to remove left borders
jQuery(document).ready(function(){
	//top menu
	jQuery(".top_links ul").children("li:eq(0)").addClass('first');
	
	//footer menu
	jQuery(".part2 ul").children("li:eq(0)").addClass('first');	
	
});	


//search field function
jQuery(document).ready(function() {
	var search_text=jQuery(".search_bar .search_text").val();

	jQuery(".search_bar .search_text").focus(function() {
		jQuery(".search_bar .search_text").val('');
	})

});


//preloading 
jQuery(function () {
	//jQuery('.preload').hide();//hide all the images on the page
	jQuery('.play,.magnifier').css({opacity:0});
	jQuery('.preload').css({opacity:0});
	jQuery('.preload').addClass("animated");
	jQuery('.play,.magnifier').addClass("animated_icon");
});

var i = 0;//initialize
var cint=0;//Internet Explorer Fix
jQuery(window).bind("load", function() {//The load event will only fire if the entire page or document is fully loaded
	var cint = setInterval("doThis(i)",70);//500 is the fade in speed in milliseconds

});

function doThis() {
	var images = jQuery('.preload').length;//count the number of images on the page
	if (i >= images) {// Loop the images
		clearInterval(cint);//When it reaches the last image the loop ends
	}
	//jQuery('.preload:hidden').eq(i).fadeIn(500);//fades in the hidden images one by one
	jQuery('.animated_icon').eq(0).animate({opacity:1},{"duration": 500});
	jQuery('.animated').eq(0).animate({opacity:1},{"duration": 500});
	jQuery('.animated').eq(0).removeClass("animated");
	jQuery('.animated_icon').eq(0).removeClass("animated_icon");
	i++;//add 1 to the count
}



jQuery(document).ready(function() {
jQuery(".photo_gallery img[title], .rt_auto_thumb_tooltip[title]").tooltip({

   // tweak the position
   offset: [40, 0],

   // use the "slide" effect
   effect: 'slide'

// add dynamic plugin with optional configuration for bottom edge
}).dynamic({ bottom: { direction: 'down', bounce: true } });
});

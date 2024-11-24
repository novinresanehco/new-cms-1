
/*
 * Superfish v1.4.8 - jQuery menu widget
 * Copyright (c) 2008 Joel Birch
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 * CHANGELOG: http://users.tpg.com.au/j_birch/plugins/superfish/changelog.txt
 */
jQuery.noConflict();

jQuery(document).ready(function(){
	var et_theme_folder = jQuery("meta[name=et_theme_folder]").attr('content'),
		$et_top_menu = jQuery('ul#top-menu > li > ul'),
		et_disable_toptier = jQuery("meta[name=et_disable_toptier]").attr('content');

	jQuery('ul.nav').superfish({ 
		delay:       300,                            // one second delay on mouseout 
		animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
		speed:       'fast',                          // faster animation speed 
		autoArrows:  true,                           // disable generation of arrow mark-up 
		dropShadows: false                            // disable drop shadows 
	});

	if ( $et_top_menu.length ){
		$et_top_menu.prepend('<li class="menu-gradient"></li>');
	}

	var $featured_content = jQuery('#featured #slides'),
		et_featured_slider_auto = jQuery("meta[name=et_featured_slider_auto]").attr('content'),
		et_featured_auto_speed = jQuery("meta[name=et_featured_auto_speed]").attr('content');
		
	jQuery(window).load( function(){
		if ($featured_content.length){
			var et_featured_options = {
				timeout: 0,
				speed: 500,
				cleartypeNoBg: true,
				prev:   '#featured a#featured-left', 
				next:   '#featured a#featured-right',
				pager:  '#controllers'
			}
			if ( et_featured_slider_auto == 1 ) et_featured_options.timeout = et_featured_auto_speed;
			
			$featured_content.css( 'backgroundImage', 'none' );
			$featured_content.cycle( et_featured_options );
			
			if ( $featured_content.find('.slide').length == 1 ){
				$featured_content.find('.slide').css({'position':'absolute','top':'0','left':'0'}).show();
				jQuery('#featured-left, #featured-right').hide();
			}
		}
	} );

	var $et_media_slider = jQuery('#media-slider');
	if ( $et_media_slider.length ){
		$et_media_slider.cycle( {
			timeout: 0,
			speed: 500,
			cleartypeNoBg: true,
			prev:   '.recent-inner a#media-left', 
			next:   '.recent-inner a#media-right'
		} );
	}

	var $et_media_thumb = jQuery('.media-thumb');
	$et_media_thumb.hover(function(){
		jQuery(this).find('img').fadeTo('fast', 0.8);
		jQuery(this).find('.more,.zoom').fadeTo('fast', 1);
	}, function(){
		jQuery(this).find('img').fadeTo('fast', 1);
		jQuery(this).find('.more,.zoom').fadeTo('fast', 0);
	});
		
	var $footer_widget = jQuery("#footer-widgets .footer-widget");
	if ( $footer_widget.length ) {
		$footer_widget.each(function (index, domEle) {
			if ((index+1)%3 == 0) jQuery(domEle).addClass("last").after("<div class='clear'></div>");
		});
	}

	et_search_bar();

	function et_search_bar(){
		var $searchform = jQuery('#search-form'),
			$searchinput = $searchform.find("input#searchinput"),
			searchvalue = $searchinput.val();
			
		$searchinput.focus(function(){
			if (jQuery(this).val() === searchvalue) jQuery(this).val("");
		}).blur(function(){
			if (jQuery(this).val() === "") jQuery(this).val(searchvalue);
		});
	}

	if ( et_disable_toptier == 1 ) jQuery("ul.nav > li > ul").prev("a").attr("href","#");

	var $comment_form = jQuery('form#commentform');
	$comment_form.find('input, textarea').focus(function(){
		if (jQuery(this).val() === jQuery(this).next('label').text()) jQuery(this).val("");
	}).blur(function(){
		if (jQuery(this).val() === "") jQuery(this).val( jQuery(this).next('label').text() );
	});

	$comment_form.find('input#submit').click(function(){
		if (jQuery("input#url").val() === jQuery("input#url").next('label').text()) jQuery("input#url").val("");
	});
});
;(function($){
	$.fn.superfish = function(op){

		var sf = $.fn.superfish,
			c = sf.c,
			$arrow = $(['<span class="',c.arrowClass,'"> &#187;</span>'].join('')),
			over = function(){
				var $$ = $(this), menu = getMenu($$);
				clearTimeout(menu.sfTimer);
				$$.showSuperfishUl().siblings().hideSuperfishUl();
			},
			out = function(){
				var $$ = $(this), menu = getMenu($$), o = sf.op;
				clearTimeout(menu.sfTimer);
				menu.sfTimer=setTimeout(function(){
					o.retainPath=($.inArray($$[0],o.$path)>-1);
					$$.hideSuperfishUl();
					if (o.$path.length && $$.parents(['li.',o.hoverClass].join('')).length<1){over.call(o.$path);}
				},o.delay);	
			},
			getMenu = function($menu){
				var menu = $menu.parents(['ul.',c.menuClass,':first'].join(''))[0];
				sf.op = sf.o[menu.serial];
				return menu;
			},
			addArrow = function($a){ $a.addClass(c.anchorClass).append($arrow.clone()); };
			
		return this.each(function() {
			var s = this.serial = sf.o.length;
			var o = $.extend({},sf.defaults,op);
			o.$path = $('li.'+o.pathClass,this).slice(0,o.pathLevels).each(function(){
				$(this).addClass([o.hoverClass,c.bcClass].join(' '))
					.filter('li:has(ul)').removeClass(o.pathClass);
			});
			sf.o[s] = sf.op = o;
			
			$('li:has(ul)',this)[($.fn.hoverIntent && !o.disableHI) ? 'hoverIntent' : 'hover'](over,out).each(function() {
				if (o.autoArrows) addArrow( $('>a:first-child',this) );
			})
			.not('.'+c.bcClass)
				.hideSuperfishUl();
			
			var $a = $('a',this);
			$a.each(function(i){
				var $li = $a.eq(i).parents('li');
				$a.eq(i).focus(function(){over.call($li);}).blur(function(){out.call($li);});
			});
			o.onInit.call(this);
			
		}).each(function() {
			var menuClasses = [c.menuClass];
			if (sf.op.dropShadows  && !($.browser.msie && $.browser.version < 7)) menuClasses.push(c.shadowClass);
			$(this).addClass(menuClasses.join(' '));
		});
	};

	var sf = $.fn.superfish;
	sf.o = [];
	sf.op = {};
	sf.IE7fix = function(){
		var o = sf.op;
		if ($.browser.msie && $.browser.version > 6 && o.dropShadows && o.animation.opacity!=undefined)
			this.toggleClass(sf.c.shadowClass+'-off');
		};
	sf.c = {
		bcClass     : 'sf-breadcrumb',
		menuClass   : 'sf-js-enabled',
		anchorClass : 'sf-with-ul',
		arrowClass  : 'sf-sub-indicator',
		shadowClass : 'sf-shadow'
	};
	sf.defaults = {
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		pathLevels	: 1,
		delay		: 800,
		animation	: {opacity:'show'},
		speed		: 'normal',
		autoArrows	: true,
		dropShadows : true,
		disableHI	: false,		// true disables hoverIntent detection
		onInit		: function(){}, // callback functions
		onBeforeShow: function(){},
		onShow		: function(){},
		onHide		: function(){}
	};
	$.fn.extend({
		hideSuperfishUl : function(){
			var o = sf.op,
				not = (o.retainPath===true) ? o.$path : '';
			o.retainPath = false;
			var $ul = $(['li.',o.hoverClass].join(''),this).add(this).not(not).removeClass(o.hoverClass)
					.find('>ul').hide().css('visibility','hidden');
			o.onHide.call($ul);
			return this;
		},
		showSuperfishUl : function(){
			var o = sf.op,
				sh = sf.c.shadowClass+'-off',
				$ul = this.addClass(o.hoverClass)
					.find('>ul:hidden').css('visibility','visible');
			sf.IE7fix.call($ul);
			o.onBeforeShow.call($ul);
			$ul.animate(o.animation,o.speed,function(){ sf.IE7fix.call($ul); o.onShow.call($ul); });
			return this;
		}
	});

})(jQuery);

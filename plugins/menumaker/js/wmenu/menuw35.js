
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
jQuery(function(){

		jQuery.noConflict();
	
		jQuery('ul.menuw35').superfish({
			delay:       300,                            // one second delay on mouseout 
			animation:   {'marginLeft':'0px',opacity:'show',height:'show'},  // fade-in and slide-down animation 
			speed:       'fast',                          // faster animation speed 
			autoArrows:  true,                           // disable generation of arrow mark-up 
			onBeforeShow:      function(){ this.css('marginLeft','20px'); },
			dropShadows: false                            // disable drop shadows 
		});
		
		jQuery('ul.menuw35 ul > li').addClass('noLava');
		jQuery('ul.menuw35 > li').addClass('top-level');
		
		jQuery('ul.menuw35 > li > a.sf-with-ul').parent('li').addClass('sf-ul');
		
				
		if (!(jQuery("#footer_widgets .block_b").length == 0)) {
			jQuery("#footer_widgets .block_b").each(function (index, domEle) {
				// domEle == this
				if ((index+1)%3 == 0) jQuery(domEle).after("<div class='clear'></div>");
			});
		};
		
	
		jQuery('ul.menuw35 li ul').append('<li class="bottom_bg noLava"></li>');
		
		var active_subpage = jQuery('ul.menuw35 ul li.current-cat, ul.menuw35 ul li.current_page_item').parents('li.top-level').prevAll().length;
		var isHome = 1; 
		
		if (active_subpage) jQuery('ul.menuw35').lavaLamp({ startItem: active_subpage });
		else if (isHome === 1) jQuery('ul.menuw35').lavaLamp({ startItem: 0 });
		else jQuery('ul.menuw35').lavaLamp();
			
		
					
		});

(function(jQuery) {
jQuery.fn.lavaLamp = function(o) {
	o = jQuery.extend({ fx: 'swing',
					  	speed: 500, 
						click: function(){return true}, 
						startItem: 'no',
						autoReturn: true,
						returnDelay: 0,
						setOnClick: true,
						homeTop:0,
						homeLeft:0,
						homeWidth:0,
						homeHeight:0,
						returnHome:false
						}, 
					o || {});

	var $home;
	// create homeLava element if origin dimensions set
	if (o.homeTop || o.homeLeft) { 
		$home = jQuery('<li class="homeLava selectedLava"></li>').css({ left:o.homeLeft, top:o.homeTop, width:o.homeWidth, height:o.homeHeight, position:'absolute' });
		jQuery(this).prepend($home);
	}
		
	return this.each(function() {
		var path = location.pathname + location.search + location.hash;
		var $selected = new Object;
		var delayTimer;
		var $back;
		var ce; //current_element
		
		var $li = jQuery('li:not(.noLava)', this);
		// check for complete path match, if so flag element into $selected
		if ( o.startItem == 'no' )
			$selected = jQuery('li a[href$="' + path + '"]', this).parent('li');
			
		// if still no match, this may be a relative link match 
		if ($selected.length == 0 && o.startItem == 'no')
			$selected = jQuery('li a[href$="' +location.pathname.substring(location.pathname.lastIndexOf('/')+1)+ location.search+location.hash + '"]', this).parent('li');

		// no default selected element matches worked, 
		// or the user specified an index via startItem
		if ($selected.length == 0 || o.startItem != 'no') {
			// always default to first item, if no startItem specified.
			if (o.startItem == 'no') o.startItem = 0;
			$selected = jQuery($li[o.startItem]);
		}
		// set up raw element - this allows user override by class .selectedLava on load
		ce = jQuery('li.selectedLava', this)[0] || jQuery($selected).addClass('selectedLava')[0];

		// add mouseover event for every sub element
		$li.mouseenter(function() {
			if (jQuery(this).hasClass('homeLava')) {
				ce = jQuery(this)[0];
			}
			move(this);
		});

		$back = jQuery('<li class="backLava"><div class="leftLava"></div><div class="bottomLava"></div><div class="cornerLava"></div></li>').appendTo(this);
		
		// after we leave the container element, move back to default/last clicked element
		jQuery(this).mouseleave( function() {
			if (o.autoReturn) {
				if (o.returnHome && $home) {
					move($home[0]);
				}
				else if (o.returnDelay) {
					if(delayTimer) clearTimeout(delayTimer);
					delayTimer = setTimeout(function(){move(null);},o.returnDelay + o.speed);
				}
				else {
					move(null);
				}
			}
		});

		$li.click(function(e) {
			if (o.setOnClick) {
				jQuery(ce).removeClass('selectedLava');
				jQuery(this).addClass('selectedLava');
				ce = this;
			}
			return o.click.apply(this, [e, this]);
		});

		// set the starting position for the lavalamp hover element: .back
		if (o.homeTop || o.homeLeft)
			$back.css({ left:o.homeLeft, top:o.homeTop, width:o.homeWidth, height:o.homeHeight });
		else
			$back.css({ left: ce.offsetLeft, top: ce.offsetTop, width: ce.offsetWidth, height: ce.offsetHeight });


		function move(el) {
			if (!el) el = ce;
			// .backLava element border check and animation fix
			var bx=0, by=0;
			if (!jQuery.browser.msie) {
				bx = ($back.outerWidth() - $back.innerWidth())/2;
				by = ($back.outerHeight() - $back.innerHeight())/2;
			}
			$back.stop()
			.animate({
				left: el.offsetLeft-bx,
				top: el.offsetTop-by,
				width: el.offsetWidth,
				height: el.offsetHeight
			}, o.speed, o.fx);
		};
	});
};
})(jQuery);

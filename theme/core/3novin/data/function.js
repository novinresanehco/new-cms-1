$(function(){

		$('.ordrLnk').hover(function() {
			$(this).find('.light_order_link').fadeIn('slow');
		},function () {
			$(this).find('.light_order_link').fadeOut('slow');
		});
		
			$(document).bind('click', function(e){
				if($(e.target).parents('#select-domain').size() <= 0)
					$('#domain-list').hide();
				if($(e.target).parents('.loginbar').size() <= 0)
					$('.loginbar .form').hide();
			});
			
			$('a#accounts').click(function(){
									$('div#accounts-list').slideToggle();
								});
			
			$('#slider-wrapper ul').bxSlider({
							auto: true,
							pager: true,
							autoHover: true,
							pause: 8000
						});
			
			$('#latest-news ul').bxSlider({
							mode: 'vertical',
							controls: false,
							auto: true,
							displaySlideQty: 1,
							moveSlideQty: 1,
							pager: false
						});
			
			$('#customers').bxSlider({
							mode: 'vertical',
							controls: false,
							auto: true,
							displaySlideQty: 1,
							moveSlideQty: 1,
							pager: false
						});
			
			$('#select-domain #title a').click(function(){ $('#domain-list').toggle(); });
			$('#domain-list').tinyscrollbar({ sizethumb: 46 }).hide();
			$('#domain-list li').click(function(){
				$('#select-domain #title a').text($(this).text());
				$(this).parents('form').find('input[name=ext]').val($(this).text());
				$('#domain-list').hide();
			});
			
			$('.menu-logo .menu li.has-child').hover(function(){
				$(this).addClass('hover');
				$(this).find('ul.child').slideDown('fast');
			}, function(){
				$(this).find('ul.child').slideUp('fast', function(){
					$(this).parent().removeClass('hover');
				});
			});
			
			$('.menu-logo .menu li.has-child li').hover(function(){
				$(this).find('ul').animate({width: 'toggle'});
			});
			
			$('.loginbar a.login').click(function(){
				$(this).next().toggle();
			});
			jQuery(".move-left").hover(function () {
			jQuery(this).stop().animate({
				'padding-right' : '25px'
				}, 100 );
		    }, function () {
			jQuery(this).stop().animate({
				'padding-right' : '20px'
				}, 100 )
		    });
		});
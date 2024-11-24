/* Portfolio Sorting */

jQuery(document).ready(function () {
	(function ($) {

		var container = $('.works-list');
			
			container.isotope({
				itemSelector : '.project', 
				layoutMode : 'fitRows'
			});
		
		jQuery(window).load(function () {
			container.isotope().isotope('layout');
		});

        $('.works-list').each(function () {

            var $portfolio_container = $(this);
            var $sorting_buttons = $portfolio_container.siblings('.sort-panel').find('a');

            $sorting_buttons.each(function () {

                var selector = $(this).attr('data-filter');
                var count = $portfolio_container.find(selector).length;
                if (count != 0) {
                    $(this).parent().css('display', 'inline-block');
                }
            });


            $sorting_buttons.click(function () {
                var selector = $(this).attr('data-filter');

                $(this).parent().parent().find('.active').removeClass('active');

                $(this).parent().addClass('active');

                container.isotope( {
                    filter : selector
                } );

                return false;
            });
        });

	} )(jQuery);
} );


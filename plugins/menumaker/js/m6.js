  $('li').hover(function () {
     clearTimeout($.data(this,'timer'));
     $(this).children('ul').stop(true,true).slideDown(300);
  }, function () {
    $.data(this,'timer', setTimeout($.proxy(function() {
      $('ul',this).stop(true,true).slideUp(200);
    }, this), 100));
  });
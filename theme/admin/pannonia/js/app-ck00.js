$(function () {
    $("#demo-control").slider({
        max: 100,
        min: 20,
        step: 5,
        value: 60,
        slide: function (e, t) {
            $("#demo-wrap").css("width", t.value + "%");
            $("#hint-triangle").animate({
                top: 20
            })
        }
    })
});
$(".demo-article").bind("mousewheel DOMMouseScroll", function (e) {
    var t = null;
    e.type == "mousewheel" ? t = e.originalEvent.wheelDelta * -1 : e.type == "DOMMouseScroll" && (t = 40 * e.originalEvent.detail);
    if (t) {
        e.preventDefault();
        $(this).scrollTop(t + $(this).scrollTop())
    }
});
$("#demo-wrap").flowtype({
    fontRatio: 36
});
$(".featured-article").flowtype({
    minFont: 12,
    fontRatio: 20
});
$(".half-article").flowtype({
    minFont: 16,
    fontRatio: 30
});
$(".large-article").flowtype({
    minFont: 16,
    fontRatio: 28
});
$(".main-article").flowtype({
    minFont: 16,
    fontRatio: 28
});
$(".quarter-article-a,.quarter-article-b").flowtype({
    minFont: 10,
    fontRatio: 20
});
$(".side-stories").flowtype({
    minFont: 10,
    fontRatio: 20
});
$(".triad,.triad-last").flowtype({
    minFont: 16,
    fontRatio: 22
});
$('document').ready(function () {
    var title = $(".one-post-blog-tit");
    var titleHeight = $(".one-post-blog-tit").outerHeight();
    var offset = title.offset();
    $('.broad').css('top', offset.top + titleHeight + 12)
    
})
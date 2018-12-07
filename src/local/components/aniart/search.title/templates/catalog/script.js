$('document').ready(function () {
    $('.search-bt').on('click', function (event) {
        if ($('#title-search-input').val() == "") {
            event.preventDefault();
            $('.search-top .err').fadeIn().delay(2000).fadeOut();
        }
    })
})
$(document).ready(function () {
    var menu = $('.side-menu .menu-block');
    menu.on('click', 'li a', function (event) {
        var menuItem = $(this).parent();
        if ( $(menuItem).find('ul').length > 0 ) {
            // event.preventDefault();
            // menuItem.toggleClass('active open');
        }
    })
})
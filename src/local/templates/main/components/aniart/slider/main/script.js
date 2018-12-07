// Слайдер на главной
$(document).ready(function () {
    var sl_main;
    var pager;
    var sliding = false;
    var bxParams = {};
    var count = $('.one-home-slide').length;
    var touch = false;
    if(count > 1) touch = true;
    if ($(window).width() < 768) {
        bxParams = {
            pagerCustom: '.home-slider .mini-slider #bx-pager',
            auto: true,
            pause: 5000,
            touchEnabled: touch,
            controls: false,
            onSliderLoad: function () {
                // $('.home-slide .one-home-slide').css('width',$(window).width())
                $('.home-slider').show();

            },
            onSlideBefore: function () {
                sliding = true;
            },
            onSlideAfter: function () {
                sl_main.stopAuto();
                sl_main.startAuto();
                sliding = false;
            }
        }
    } else {
        bxParams = {
            pagerCustom: '.home-slider .mini-slider #bx-pager',
            auto: true,
            pause: 5000,
            touchEnabled: touch,
            controls: false,
            onSliderLoad: function () {
                // $('.home-slide .one-home-slide').css('width',$(window).width())
                $('.home-slider').show();

            },
            onSlideBefore: function () {
                sliding = true;
            },
            onSlideAfter: function () {
                sl_main.stopAuto();
                sl_main.startAuto();
                sliding = false;
            }
        };
    }
    $('.home-page .main-slider').css('width', $(window).width());
    if ($('.homeslider').length > 0) {
        $('.home-slider').css('opacity', 0);
        var homeSliderLoader = new App.LoaderWidget($('.main-sl-loader-in'));
        homeSliderLoader.show();
        sl_main = $('.homeslider').bxSlider(bxParams);
        $(window).load(function () {
            if ($(window).width() < 992) {
                sl_main.reloadSlider();
            }
            $('.home-slider').animate({
                opacity: 1
            }, 300, function () {
                homeSliderLoader.reset();
                $('.main-sl-loader').remove();
            })
        })


    }
    $('.one-home-slide a').on('click', function (e) {
        var _this = this;
        e.preventDefault();
        setTimeout(function () {
            if(!sliding){ location.href = $(_this).attr('href')}
        }, 100)

    })
});
// Конец Слайдер на главной

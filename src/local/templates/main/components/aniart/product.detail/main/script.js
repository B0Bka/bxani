var ProductDetailMain = (function(App){
    
    window.App = App = App || {};
    
    return {
        //constants
        CONTROLLER:'',
        TEMPLATE:'',
        PARAMS:'',
        
        block:{
            main:'#product_detail',
            carousel:'#product_detail_car',
            availability: '#product_detail_availability_block'
        },
        form:{

        },
        selector:{
            slider:{
                vert:'#product_detail_car_vert',
                big:'#product_detail_car_big',
                img: '.prod-car-big-in img',
                lazy_img: '.prod-car-big-in img.lazy-slider-img'
            },
            sizes:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            buy_disabled:'#product_detail_buy a.disabled',
            sliderControls:'.bx-controls-direction a',
            sizeAlert : '.size-alert',
            availability: '#product_detail_availability',
            fav:'.prod-fav a',
            sizes_table: '.prod-table a',
            review_anchor: '#reviews_scroll',
            review: '.all-comm',
            map: '#map-product'
        },
        events:function(){
            var _app = App,
                _catalog = App.Catalog,
                _basket = App.Basket,
                _this = this,
                _self = this;

            $(_this.block.main).on('click', _this.selector.sizes, function(){
                App.Catalog.getSize({
                    offer: $(this).data().offer,
                    size: $(this).data().size,
                    sizeText: $(this).find('span').text(),
                    block: $(_self.selector.sizeName),
                    buy: $(_self.selector.buy),
                    inBasket: $(this).data().in_basket,
                    availability: $(_self.selector.availability),
                    availabilityBlock: $(_self.block.availability)
                });
            });

            $(_this.block.main).on('click', _this.selector.buy, function(){
                _catalog.getBuy({
                    buy:$(_this.selector.buy),
                    basket:$(_basket.block.amount),
                    offer:_catalog.CURRENT_OFFER
                });
            });

            /*$(_this.block.main).on('click', _this.selector.slider.img, function(){
                $(this).imagezoomsl({
                    zoomrange: [1, 12],
                    zoomstart: 2,
                    innerzoom: true,
                    magnifierborder: "none"
                });
            });*/




            $(_this.selector.sizes).on('click', function () {
                $(_this.selector.sizeAlert).fadeOut();
                _this.hideMap();
            });

            $('body').on('click', _this.selector.review_anchor, function(){
                var top = $(_this.selector.review).position().top + $(_this.selector.review).outerHeight(true);
                $('body,html').animate({scrollTop: top}, 1500);
            });

            // $(_this.selector.sizes_table).on('click', function () {
            //     $('#sizes_table').show();
            // })

            var link_gplus = document.getElementById('gplus');
            link_gplus.setAttribute("onclick","popupWin = window.open(this.href,'Поделиться','Toolbar=no, Location=yes, ScrollBars=yes, Width=500, Height=815'); popupWin.focus(); return false");

            // $(_this.selector.fav).on('click', function () {
            //     if($(this).hasClass('favorite')){
            //         _this.deleteProductFromfavorites($(this).data('item'));
            //     } else {
            //         $(this).addClass('favorite');
            //     }
            // })
        },
        init:function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'script.js[ProductDetailMain] init..'});

            _this.events();
            _this.initLoader();
            _this.clickOnSize();
        },
        setTemplate:function(data){
            return this.TEMPLATE = data;
        },
        setParams:function(data){
            return this.PARAMS = data;
        },
        setController:function(data){
            return this.CONTROLLER = data;
        },
        animateFadeIn:function () {
            var _this = this;
            if ($('#product_detail_car .loaderwidget').length > 0){
                _this.loader.reset();
            }

            $('#product_detail_car .load').remove();
            $('#product_detail #product_detail_car .prod-car-vert, ' +
                '#product_detail #product_detail_car .prod-car-big').css(
                'opacity', '1'
            )
        },
        initLoader:function () {
            var _this = this;
            if($('#product_detail_car .load-in').length > 0){
                _this.loader = new App.LoaderWidget($('#product_detail_car .load-in'));
                _this.loader.show();
            }

        },
        initSlider:function(params){

            var _this = this;
            var big_loaded = false;
            var small_loaded = false;
                showControls = true;

            if(params.vert.length){
                if ($(params.vert).find('img').length <= 3) showControls = false;
                params.vert.bxSlider({
                    mode: 'vertical',
                    slideWidth: 100,
                    minSlides: 3,
                    maxSlides: 3,
                    slideMargin: 15,
                    pager: false, hideControlOnEnd: true, controls: showControls,
                    onSliderLoad: function(currentIndex){
                        small_loaded = true;
                        $(params.vert).css('visibility', 'visible');
                        if(big_loaded == true && small_loaded == true){
                            // alert('both loaded');
                            _this.animateFadeIn();
                        }
                    }
                });
            }
            if(params.big.length){
                if($(window).width() <= 992){
                    params.big.bxSlider({
                        pager: true,
                        onSliderLoad: function(currentIndex){
                            if(params.block.length){
                                // params.block.css('visibility', 'visible');
                                _this.wrap($(_this.selector.slider.img)[currentIndex+1]);
                            }
                            _this.animateFadeIn();
                        },
                        onSlideBefore: function ($slideElement, oldIndex, newIndex) {
                            _this.unwrapp();
                        },
                        onSlideAfter: function ($slideElement, oldIndex, newIndex) {
                            _this.wrap($slideElement);

                        }
                    });
                } else {
                    params.big.bxSlider({
                        pagerCustom: _this.selector.slider.vert,
                        // controls: false,
                        touchEnabled:true,

                        onSliderLoad: function(currentIndex){
                            if(params.block.length){
                                // params.block.css('visibility', 'visible');
                                _this.wrap($(_this.selector.slider.img)[currentIndex+1]);

                            }
                            big_loaded = true;
                            if(big_loaded == true && small_loaded == true){
                                // alert('both loaded');
                                _this.animateFadeIn();
                            }
                        },
                        onSlideBefore: function ($slideElement, oldIndex, newIndex) {
                            _this.unwrapp();
                        },
                        onSlideAfter: function ($slideElement, oldIndex, newIndex) {
                            _this.wrap($slideElement);

                        }
                    });
                }
            }
        },
        wrap: function (element) {
            var _this = this;
            if($(window).width() > 768){
                if(!$('body').find('.zoom-wrapper').length > 0){
                    var zoom_img = element;
                    $(zoom_img)
                        .wrap('<span style="float: left; list-style: none; position: relative; width: 560px;" class="zoom-wrapper"></span>')
                        .css('display', 'block')
                        .parent()
                        .zoom({
                            on:"click",
                            callback:function () {
                                $('.zoomImg').css('z-index', '-1');
                                $('.zoom-wrapper img:nth-child(1)').css('z-index', '100');
                                $('.zoom-wrapper img:nth-child(2)').css('z-index', '200');
                            },
                            url: $(zoom_img).attr('xoriginal')
                        });
                }

                $(_this.selector.slider.vert + ' img').on('click', function () {
                    _this.unwrapp();
                });

                $(_this.selector.sliderControls).on('click', function () {
                    _this.unwrapp();
                });

                $(_this.selector.slider.img).on('mouseover', function(){

                })
            }
        },
        unwrapp: function () {
            var _this = this;
            $(_this.selector.slider.img).each(function(){
                if($(this).parent().hasClass('zoom-wrapper')){
                    $(this).unwrap();
                }
            });
        },
        deleteProductFromfavorites:function(productId){
            var _app = App;
            var _this = this;
            if(productId) {
                $.ajax({
                    url: '/ajax/common.php',
                    type: "POST",
                    data: {
                        'handler': 'favorites',
                        'func': 'removeFromFavorite',
                        'prodId': productId
                    },
                    dataType: 'json',
                    success: function (responce) {
                        if(responce.status == 'success'){
                            if($(_this.selector.fav).hasClass('in-favorite')){
                                $(_this.selector.fav).removeClass('in-favorite');
                            } else {
                                $(_this.selector.fav).addClass('in-favorite');
                            }
                            $('.rm').fadeIn().delay(800).fadeOut();
                        }
                    }
                });
            }

        },
        clickOnSize: function () {
            var k = Number(Cookies.get('checkedSize'));
            if($(".prod-size-left label[data-size=" + k + "]").length > 0) {
                $(".prod-size-left label[data-size=" + k + "]").trigger('click');
            }
            Cookies.remove('checkedSize');
        },
        hideMap: function (){
            var _this = this;
            $(_this.selector.map).removeClass('visible');
        },
        lazyLoad: function(){
            var _this = this;
            var $images = $(_this.selector.slider.lazy_img);

            $images.each(function(){
                var $img = $(this),
                    src = $img.attr('data-src');

                $img.on('load', _this.imgLoaded($img[0], src));
            });
        },
        imgLoaded: function(img, src){
            var $img = $(img);

            $img.attr('src',src);
        }
    };

})(window.App);

$(document).ready(function () {
    ProductDetailMain.init();
});

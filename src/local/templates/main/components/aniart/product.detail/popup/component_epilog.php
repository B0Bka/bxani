<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<script type="text/javascript">

var ProductSetPopup = (function(App){

    window.App = App = App || {};

    return {
        //constants
        CONTROLLER:'',
        TEMPLATE:'',
        PARAMS:'',

        block:{
            main:'.product-popup',
            carousel:'#product_detail_car-popup',
            availability: '#product_detail_availability_block',
            modal:'.set-body',
            set_modal: '#set-modal',
        },
        form:{

        },
        selector:{
            slider:{
                vert:'#product_detail_car_vert-popup',
                big_set:'#set-modal #product_detail_car_big-popup',
                big_fast:'#size #product_detail_car_big-popup',
                img: '.prod-car-big-in img',
                set: '#set-list',
                set_small: '',
            },
            sizes:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            sliderControls:'.bx-controls-direction a',
            sizeAlert : '.size-alert',
            availability: '#product_detail_availability',
            fav:'.prod-fav a',
            sizes_table: '.prod-table a',
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

            $(_this.selector.sizes).on('click', function () {
                $(_this.selector.sizeAlert).fadeOut();
            });
            $('body').on('click','.banners a', function (e) {
//                e.preventDefault();
//                alert(123);
//                _this.showSetModal();
            })
        },
        init:function(){
            var _app = App,
                _this = this;

            $(_this.block.modal).addClass("loading");
            _app.getLogInit({message:'script.js[ProductDetailPopup] init..'});

            _this.events();
//            _this.loader = new App.LoaderWidget($('.popup-loader'));
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
        initSlider:function(params){
            var _this = this;
            var p = params;
            if(params.vert.length){
                var vert_slider = params.vert.bxSlider({
                    mode: 'vertical',
                    slideWidth: 100,
                    minSlides: 3,
                    maxSlides: 3,
                    slideMargin: 25,
                    pager: false,
                    infiniteLoop: false,
                    onSliderLoad: function(){
                        $(params.vert).css("visibility", "visible");
                    },
                });
            }
            if(params.set.length){
                if($(window).width() > 425) {
                    console.log(params.set);
                    var set_slider = params.set.bxSlider({
                        mode: 'vertical',
                        slideWidth: 100,
                        minSlides: 3,
                        maxSlides: 3,
                        slideMargin: 25,
                        pager: false,
                        infiniteLoop: false,
                        onSliderLoad: function () {
                            $(params.vert).css("visibility", "visible");
                        },
                    });
                } else {
                    var mob_slider = params.set.bxSlider({
                        mode: 'horizontal',
                        slideWidth: 250,
                        minSlides: 1,
                        maxSlides: 1,
                        slideMargin: 10,
                        pager: false,
                        infiniteLoop: false,
                        onSliderLoad: function () {
                            $(params.vert).css("visibility", "visible");
                        },
                    });
                    setTimeout(function () {
                        mob_slider.reloadSlider();
                    }, 500)

                }
            }
            if(params.big_set.length || params.big_fast.length){
           //       Когда на странице попап покупки вместе с попапом комплекта, подгружать слайдеры для разных попапов соответственно
                var createBigSlider = function (sliderType) {
//                    console.log('slidertype',sliderType);
                    sliderType.bxSlider({
		                <?=$arParams['SHOW_SMALL_PICS'] == 'Y' ? 'pagerCustom: _this.selector.slider.vert' : 'pager: true'?>,
                        onSliderLoad: function(currentIndex){
                        if(params.block.length){
                            console.log('04');
                            if(params.img.length > 0 && $(window).width() > 768){
                                console.log('4');
                                _this.wrap($(_this.selector.slider.img)[currentIndex+1]);


//                                $(_this.selector.slider.vert + ' img').on('click', function () {
//                                    unwrapp();
//                                });
//
//                                $(_this.selector.sliderControls).on('click', function () {
//                                    unwrapp();
//                                });
                            }
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
                setTimeout(function(){
                    console.log('6');
                    createBigSlider(p.big_set);
                    createBigSlider(p.big_fast);
                }, 1000);
            }
        },
        wrap: function (element) {
            var _this = this;
            if($(window).width() > 768){
                if(!$('body').find('.zoom-wrapper').length > 0){
                    var zoom_img = element;
                    $(zoom_img)
                        .wrap('<span style="float: left; list-style: none; position: relative; width: 360px;" class="zoom-wrapper"></span>')
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
        showSetModal: function () {
            var _app = App,
                _this = this;
            $('#set-modal .set-items, #set-modal .set-item').css('opacity', 0);
//            _this.loader.show();
            setTimeout(function () {
                $('#set-modal .set-items, #set-modal .set-item').animate({
                    opacity:1
                }, 200, function () {
//                    _this.loader.hide();
                })
            }, 3000)
        },
    };

})(window.App);

ProductSetPopup.init();
ProductSetPopup.initSlider({
    block: $(ProductSetPopup.block.carousel),
    vert: $(ProductSetPopup.selector.slider.vert),
    big_set: $(ProductSetPopup.selector.slider.big_set),
    big_fast: $(ProductSetPopup.selector.slider.big_fast),
    img: $(ProductSetPopup.selector.slider.img),
    set: $(ProductSetPopup.selector.slider.set),
    modal: $(ProductSetPopup.block.modal),
});
</script>
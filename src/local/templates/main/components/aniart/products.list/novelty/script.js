var CatalogProductsListNovelty = (function(App){
    
    'use strict';
    
    window.App = App = App || {};
    
    return {
        //constants
        PARAMS:'',
        PAGINATION:{},
        
        block:{
            main:'#catalog_products_list',
            pager:'#catalog_pagination',
            sort:'#catalog_sort',
            product: '.one-news',
            slider: '#novlety .owl-carousel',
        },
        form:{
        },
        selector:{
            color:'.product_list_size input:radio',
            colorLabel:'.product_list_size label',
            size:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            basket: '.one-cat-by',
            thumbnail: '.one-cat-thumb',
            fav_button_add: '.one-cat-fav',
            size_in_overflow: '.overflow-sizes span',
        },
        favorites: {
            main: '.one-cat-info',
            fav_button_add: '.one-cat-fav',
            product_button_add: '.prod-fav a',
            in_favorite: 'in-favorite',
        },
        events:function(){
            var _app = App,
                _catalog = _app.Catalog,
                _basket = _app.Basket,
                _this = this,
                _self = this;
            
            $(_this.block.pager).on('click', 'a', function(){
                _this.getLoad({
                    sort:$(this).data().sort
                }, function(){
                    _this.initAnimOnScroll();
                });
            });
            var hoveredLink = '';
            $('body').on('mouseover', _this.block.product, function () {
                hoveredLink = $(this).find('a').attr('href');
            })
            $('body').on('click', _this.selector.size_in_overflow, function(){
                var size = $(this).text();
                Cookies.set('checkedSize', size);
                window.location.href = hoveredLink;
            });
            $(_app.block.main).on('change', _this.block.sort, function(){
                window.location.href=$(this).find(':selected').val();
            });
            $(_this.block.main).on('click', _this.selector.basket, function(){
                var object = $(this),
                    id = object.closest(_this.block.product).find(_this.selector.colorLabel+'[class="checked"]').data().id;

                App.Catalog.getModalBuy({id: id});
            });
            //избранное в новых поступлениях
            $('body').on('click',_this.block.product + ' ' + _this.selector.fav_button_add, function () {

                var __this = this;
                var func = '',
                product_id = 0;

                product_id = $(__this).closest(_this.block.product).find('.product_list_size label.checked').data('id');

                App.Catalog.addProduct2FavoriteList(product_id,func, __this);

            });
            $(_this.block.main).on('change', _this.selector.color, function(){
                var object = $(this);
                $(this).closest(_this.block.product).find('.overflow-sizes').remove();
                App.Catalog.getProduct({
                    id: object.val(),
                    elite: object.closest(_this.block.product).hasClass('cat-50'),
                    loader: object.closest(_this.block.product).find('.product_img a'),
                    handler: 'products.list',
                    component: _self.PARAMS
                }, function(){
                    App.Catalog.selectSibling({
                        id: object.val(),
                        name: object.closest(_this.block.product).find('.one-cat-tit a'),
                        img: object.closest(_this.block.product).find('.product_img a'),
                        price: object.closest(_this.block.product).find('.one-cat-price'),
                        elite: object.closest(_this.block.product).hasClass('cat-50')
                    });
                });
            });
            $(_app.block.main).on('click', _this.selector.size, function(){
                
                $(_this.selector.size).removeClass('checked');
                $(this).addClass('checked');
                
                _catalog.getSize({
                    offer:$(this).data().offer,
                    size:$(this).data().size,
                    inBasket: $(this).data().in_basket,
                    buy:$(_catalog.modal.size.buy)
                });
            });
            $(_app.block.main).on('click', _catalog.modal.size.buy, function(){
                _catalog.getBuy({
                    buy:$(_catalog.modal.size.buy),
                    basket:$(_basket.block.amount),
                    offer:_catalog.CURRENT_OFFER
                });
            });
            // $('body').on('mouseover', _this.selector.thumbnail, function () {
            //     var _this = this;
            //     if(!$(this).find('.overflow-sizes').length > 0){
            //         $(this).append("<div class='overflow-sizes'><span class = 'overflow-sizes-heading'>Доступные размеры</span></div>");
            //         _this.visited = 1;
            //
            //
            //         var id = $(this).closest('.one-news').find('.one-cat-color label.checked').data('id');
            //         var elite = '';
            //
            //         App.Catalog.getProduct({
            //             id: id,
            //             // elite: elite,
            //             handler: 'products.list',
            //             component: _self.PARAMS
            //         }, function () {
            //             var sizes = App.Catalog.SIBLING[id].sizes;
            //             if(sizes.length == 0){
            //                 $(_this).find('.overflow-sizes').append("<span class='size'>Нет доступных размеров</span>")
            //             } else {
            //                 for (var i = 0; i < sizes.length; i++) {
            //                     if(sizes[i].value != null){
            //                         $(_this).find('.overflow-sizes').append("<span class='size'>" + sizes[i].value + "</span>")
            //                     }
            //
            //                 }
            //             }
            //         });
            //     }
            //
            // });

        },
        init:function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'script.js[CatalogProductListMain] init..'});
            _this.initNovletySlider();
            _this.events();
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
        setPagination:function(data){
            return this.PAGINATION = data;
        },
        getPager:function(){
            var _this = this,
                init = _this.PAGINATION.NavRecordCount-_this.PAGINATION.NavPageSize*_this.PAGINATION.NavPageNomer,
                pager = $(_this.block.pager);
            
            if(init <= 0){
                pager.hide();
                return false;
            }
            
            if(pager.length){
                pager.find('span').html(_this.PAGINATION.NavPageSize);
                pager.show();
            }
            return true;
        },
        getLoad:function(params, callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main);
            
            _this.PAGINATION.NavPageNomer++;
            
            _app.post({
                url:_app.AJAX_DIR+'?'+params.sort,
                data:{
                    handler:'catalog', 
                    func:'loadPage', 
                    page:_this.PAGINATION.NavPageNomer,
                    componentParams:_this.PARAMS
                }
            }, function(response){
                if(!block.length){
                    alert('block product list not found.. ');
                    return false;
                }
                block.append(response.data.html);
                if(callback && typeof(callback) === 'function'){
                    callback();
                }
                return true;
            });
        },
        getBuy:function(params){
            return params;
        },
        initAnimOnScroll:function(){
            var _this = this;
            if($(_this.block.main).length){
                new AnimOnScroll(document.getElementById('catalog_products_list'), {
                    minDuration : 0.4,
                    maxDuration : 0.7,
                    viewportFactor : 0.2
                });
            }
        },
        addProduct2FavoriteList:function(productId, func, current_button){
            var _app = App,
                _this = this;
            if(productId) {
                $.ajax({
                    url: '/ajax/common.php',
                    type: "POST",
                    data: {
                        'handler': 'favorites',
                        'func': func,
                        'prodId': productId
                    },
                    dataType: 'json',
                    success: function (responce) {

                        if(responce.status == 'success'){
                            if($(current_button).hasClass(_this.favorites.in_favorite)){
                                $(current_button).removeClass(_this.favorites.in_favorite);
                            } else {
                                $(current_button).addClass(_this.favorites.in_favorite)
                            }

                        }
                        if(responce.status == 'error' && responce.message == 'need_auth'){
                            $('#myModal').modal("show");
                        }
                    }
                });
            }

        },
        initNovletySlider:function () {
            var _this = this;
            _this.nowlety_loader = new App.LoaderWidget($('#novlety .news-slider'));
            _this.nowlety_loader.show();
            $('#novlety .news-slider').css('opacity','0');
            var $novl_carousel = $(_this.block.slider).owlCarousel({
                loop:true,
                margin:10,
                responsiveClass:true,
                navText: ["", ""],
                responsive:{
                    0:{
                    	items:1.5,
                        nav:true,
                        loop:true,
                        merge: true,
                        center: true,
                        // margin: 10,
                    },
                    375:{
                        items:2,
                        nav:true,
                        loop:true,
                        merge: true,
                        center: true,
                        // margin: 15,
                    },
                    425:{
                        items:2.5,
                        nav:true,
                        loop:true,
                        center: true,
                        startPosition:2,
                    },
                    600:{
                        items:3,
                        nav:true,
                        loop:false,
                    },
                    1000:{
                        items:4,
                        nav:true,
                        loop:false
                    }
                }
            });


            setTimeout(function () {
                $novl_carousel.trigger('refresh.owl.carousel');
                _this.nowlety_loader.reset();
                $('#novlety .news-slider').animate({
                    opacity:1
                }, 500)

            }, 1500)
        }
    };

})(window.App);

$(document).ready(function () {
    CatalogProductsListNovelty.init();
});
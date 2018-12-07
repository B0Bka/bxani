var CatalogProductsListMain = (function(App){

    'use strict';

    window.App = App = App || {};

    return {
        //constants
        PARAMS:'',
        PAGINATION:{},
        pages:[],
        loadingTimes: 0,
        scroll: 0, //блокирует подгрузку пока не загрузится след страница;
        lastScrollTop: 0,
        visited:0,
        catalog_default:{},
        topPage:1,
        sizeCode: '',
        block:{
            catalog: '.catalog',
            main: '#catalog_products_list',
            pager: '#catalog_pagination',
            sort: '#catalog_sort',
            product: '.one-cat-item',
            numberPagination: '#catalog_pagination_num',
            modal: '.modal-size',
            item: '.one-cat-item',
            banner: '.banners-wrap .banners',
            loader_class: '.load-mask',
        },
        form:{
        },
        selector:{
            color:'.product_list_size input[type="radio"]',
            colorLabel:'.product_list_size label',
            size:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            basket: '.one-cat-by',
            overflow: '.overflow',
            numberPaginationLinks:'#catalog_pagination_num a',
            paginationRight:'#catalog_pagination_num .pagination_control.right',
            paginationLeft:'#catalog_pagination_num .pagination_control.left',
            birka:'.overflow-sizes',
            size_in_overflow: '.one-cat-sizes-aval span',
            thumbnail: '.one-cat-thumb',
            sizeFilterCode: '.size-set-filter .selected',
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

            $(_this.block.pager).on('click', 'a', function () {
                if(App.Catalog.PAGINATION.NavPageNomer != App.Catalog.PAGINATION.NavPageCount) {
                    _this.loadingTimes++;


                    // $(_this.selector.overflow).fadeIn();
                    _this.getLoad({
                        sort: $(this).data().sort
                    }, function () {
                        _app.getStyler({
                            object:$(_this.block.product).find(_this.selector.color)
                        });
                        // $(_this.block.product+'.fake').remove();
                        // _this.initAnimOnScroll();
                        // $(_this.selector.overflow).fadeOut();
                        _this.scroll = 0;

                        $(_this.selector.numberPaginationLinks).each(function () {
                            if ($(this).hasClass('active')) {
                                $(this).prevAll().eq(_this.loadingTimes - 1).addClass('active start');
                            }
                        });
                        $(_this.selector.numberPaginationLinks).each(function () {
                            if ($(this).hasClass('start'))
                                $(this).nextUntil(_this.selector.numberPaginationLinks + '.active').addClass('active')
                        });
                        $('.anchor').each(function () {
                            var page = parseInt($(this).attr("data-page"));
                            _this.pages.push(page);

                        })

                        $('#catalog_pagination_num .container li a').each(function () {
                            $(this).removeClass('circle');
                            if(_this.pages.indexOf(parseInt($(this).text())) !== -1){
                                $(this).addClass('circle');
                            }
                        })
                    })
                }


            });


            $(_app.block.main).on('change', _this.block.sort, function(){
                window.location.href=$(this).find(':selected').val();
            });

            $(_this.block.main).on('click', _this.selector.basket, function(){
                var object = $(this),
                    id = object.closest(_this.block.product).find(_this.selector.colorLabel+'[class="checked"]').data().id;

                App.Catalog.getModalBuy({id: id});
            });

            //переключение цветов
            $('body').on('change', _this.selector.color, function(){
                var object = $(this),
                    id = object.val(),
                    elite = object.closest(_this.block.product).hasClass('cat-50'),
                    sibling_prefix = (elite) ? 'SIBLING_ELITE' : 'SIBLING',
                    img = {};

                object.closest(_this.block.product).find(_this.favorites.fav_button_add).attr('data-item', id);

                $(this).closest(_this.block.product).find('.overflow-sizes').remove();

                if(object.closest(_this.block.product).find('a[data-id="'+id+'"]').length) {
                    var style = {
                        height: object.closest(_this.block.product).find('.product_img').height(),
                        widht: object.closest(_this.block.product).find('.product_img').width()
                    };
                    object.closest(_this.block.product).find('.product_img a').hide();
                    object.closest(_this.block.product).find('.product_img').css(style);
                    object.closest(_this.block.product).find('a[data-id="'+id+'"]').show();
                }
                else{
                    img = object.closest(_this.block.product).find('.product_img');
                }
                App.Catalog.getProduct({
                    id: id,
                    elite: elite,
                    loader: object.closest(_this.block.product).find('.product_img a[data-id="'+id+'"]'),
                    handler: 'products.list',
                    component: _self.PARAMS
                }, function(){
                    App.Catalog.selectSibling({
                        id: id,
                        name: object.closest(_this.block.product).find('.one-cat-tit a'),
                        img: img,
                        price: object.closest(_this.block.product).find('.one-cat-price'),
                        sizes: object.closest(_this.block.product).find('.one-cat-sizes-aval'),
                        elite: elite
                    });

                    if(typeof App.Catalog[sibling_prefix][id].inFavorites !== "undefined") {
                        if (App.Catalog[sibling_prefix][id].inFavorites == "N") {
                            object.closest('.one-cat-item').find('.one-cat-fav').removeClass('in-favorite');
                        } else object.closest('.one-cat-item').find('.one-cat-fav').addClass('in-favorite');
                    }


                });
            });

            //Добавление в избранное

            //избранное в каталоге (catalog list)
            $('body').on('click',_this.block.main + ' ' + _this.favorites.fav_button_add, function () {
                var __this = this;
                var func = '',
                product_id = $(__this).closest(_this.block.item).find('.product_list_size label.checked').data('id');
                App.Catalog.addProduct2FavoriteList(product_id,func, __this);
});


            $('body').on('click', _app.block.main +' '+ _this.selector.size, function(){
                // $(_this.selector.size).removeClass('checked');
                // $(this).addClass('checked');

                _catalog.getSize({
                    offer:$(this).data().offer,
                    size:$(this).data().size,
                    inBasket: $(this).data().in_basket,
                    buy:$(_catalog.modal.size.buy)
                });
            });

            $('body').on('click', _this.block.modal +' '+ _this.selector.size, function(){
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

            $('body').on('click', App.Reorder.buttons.send, function () {
                App.Reorder.send(function () {
                    _this.getReLoad({
                        sort:$(_this.block.pager).find('a').data().sort
                    }, function(){
                        // _this.initAnimOnScroll();
                    });
                });
            });

            //autoload
            if($(window).width() > 767){
                $(window).on('wheel', function (e) {
                    _this.getDesctopScrollLoad(e, this);
                });
            } else {
                $(window).on('scroll', function (e) {
                    _this.getMobileScrollLoad(this);
                });
            }



           var hoveredLink = '';

           $('body').on('mouseover', _this.block.product, function () {
               hoveredLink = $(this).find('a').attr('href');
           })
            $('body').on('click', _this.selector.size_in_overflow, function(){
               var size = $(this).text();
               Cookies.set('checkedSize', size);
               window.location.href = hoveredLink;
           });

           $('body').on('click','.filter-mob-bt',function () {
               $('body').find($(this).data('target')).fadeToggle();
           });

        },
        init:function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'script.js[CatalogProductListMain] init..'});
            _app.getStyler({
                object:$(_this.block.product).find(_this.selector.color)
            });
            _this.events();

            this.PAGINATION = App.Catalog.PAGINATION;
        },
        setTemplate: function(data){
            return this.TEMPLATE = data;
        },
        setParams: function(data){
            return this.PARAMS = data;
        },
        setController: function(data){
            return this.CONTROLLER = data;
        },
        getCatalogDefault: function () {
            return {
                height: $(this.block.catalog).height(),
                items_count: $(this.block.product).length,
            }
        },
        getPager: function(){
            var _this = this,
                _app = App,
                init = App.Catalog.PAGINATION.NavRecordCount-App.Catalog.PAGINATION.NavPageSize*App.Catalog.PAGINATION.NavPageNomer,
                pager = $(_this.block.pager);
            _this.pages = [];

            if(pager.length){
                pager.find('span').html(App.Catalog.PAGINATION.NavPageSize);
                pager.show();

                $(_this.block.numberPagination + " .container").pagination({
                    items: App.Catalog.PAGINATION.NavRecordCount,
                    itemsOnPage: App.Catalog.PAGINATION.NavPageSize,
                    displayedPages:3,
                    edges:1,
                    currentPage:App.Catalog.PAGINATION.NavPageNomer,
                    cssStyle: 'light-theme',
                    prevText:"<i class='fa fa-angle-left' aria-hidden='true'></i>",
                    nextText:"<i class='fa fa-angle-right' aria-hidden='true'></i>",
                    hrefTextPrefix:"#page-",
                    onPageClick: function(pageNumber, event){

                        event.preventDefault();

                        if(!$(".anchor[name = page-"+pageNumber+"]").length > 0){
                            $(_this.selector.overflow).fadeIn();
                            App.Catalog.PAGINATION.NavPageNomer = pageNumber;
                            _this.getReLoad({
                                sort:$(this).data().sort
                            }, function(){
                                // _this.initAnimOnScroll();
                                $(_this.selector.overflow).fadeOut();

                                history.pushState({}, '', '?top='+pageNumber);

                                //add params to search url
                                try{
                                    $.query.set('page', pageNumber).toString();
                                } catch(err) {
                                    console.log(err);
                                }


                                _app.getStyler({
                                    object:$(_this.block.product).find(_this.selector.color)
                                });
                            });
                        } else {
                            $('body').on('click', function (event) {
                                if(event.target.classList.contains("page-link")){
                                    try {
                                        var elementClick = event.target.hash;
                                        var destination = $(elementClick).offset().top - 50;
                                        $('html').animate({scrollTop: destination}, 1100);
                                    } catch(err){
                                        console.log(err);
                                    }
                                }
                                if(event.target.classList.contains("current")){
                                    try {
                                        var elementClick = "#page-"+event.target.innerText;
                                        var destination = $(elementClick).offset().top - 50;
                                        $('html').animate({scrollTop: destination}, 1100);
                                    } catch(err){
                                        console.log(err);
                                    }
                                }
                            });
                        }
                    }
                });
            }
            if(App.Catalog.PAGINATION.NavPageCount == "1"){
                $(_this.block.numberPagination).hide();
                return false;
            }
            return true;
        },
        getDesctopScrollLoad: function (event, __this) {
            var _app = App,
                _this = this;
            var height = screen.height;
            var top = $('#catalog_pagination').offset().top - $(window).scrollTop() - height;
            var st = $(__this).scrollTop();
            if (event.originalEvent.deltaY > 0) {
                //-200; больше - раньше включается подгрузка
                if (top < 1000) {
                    if (_this.scroll == 0) {
                        if(App.Catalog.PAGINATION.NavPageNomer != App.Catalog.PAGINATION.NavPageCount &&
                            App.Catalog.PAGINATION.NavPageCount != 0){
                            $(_this.block.pager + ' a').trigger('click');
                            _this.scroll = 1;
                        }

                    }
                }
            }
            _this.lastScrollTop = st;
            $('.catalog-wrap').removeAttr('style');
            _this.lazyLoadPhoto();
        },
        getMobileScrollLoad: function (__this) {
            var _app = App,
                _this = this;
            var height = screen.height;
            var top = $('#catalog_pagination').offset().top - $(window).scrollTop() - height;
            var st = $(__this).scrollTop();
            if (st > _this.lastScrollTop) {
                //-200; больше - раньше включается подгрузка
                if (top < 0) {
                    if (_this.scroll == 0) {
                        if(App.Catalog.PAGINATION.NavPageNomer != App.Catalog.PAGINATION.NavPageCount &&
                            App.Catalog.PAGINATION.NavPageCount != 0){
                            $(_this.block.pager + ' a').trigger('click');
                            _this.scroll = 1;
                        }

                    }
                }
            }
            _this.lastScrollTop = st;
            $('.catalog-wrap').removeAttr('style');
        },
        smoothLoad: function (param) {
            var _app = App,
                _this = this;
            if(typeof (_this.catalog_default.height) === 'undefined'){
                _this.catalog_default = _this.getCatalogDefault();
            }
            var catalog_default = _this.catalog_default;

            switch (param){
                case 'start':
                    var top = parseInt($('.catalog').height() + 275);
                    var width = parseInt($('.catalog').width());
                    if(!$('body').find(_this.block.loader_class).length > 0) {
                        $('.catalog').append("<div class ='" + _this.block.loader_class.slice(1) + "'>");
                        for (var i = 0; i < catalog_default.items_count; i++) {
                            $('body').find(_this.block.loader_class).append("" +
                                "<div class='one-cat-item fake'>" +
                                "<div class='one-cat-thumb fake-loader'></div>" +
                                "<div class='one-cat-tit fake-loader'></div>" +
                                "<div class='one-cat-color fake-loader'></div>" +
                                "<div class='one-cat-price fake-loader'></div>" +
                                "</div>")
                        }
                    }
                    break;

                case 'stop':
                    $(_this.block.loader_class).remove();
                    break;
            }

        },
        imageLoader: function (el) {
            // console.log($(el).closest('.product_img'));
            if($(el).closest('.product_img').hasClass('loader')){
                $(el).closest('.product_img').removeClass('loader');
            }
        },
        getLoad: function(params, callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main),
                page;
            _this.topPage = parseInt(_this.urlParam('top'));
            if(_this.topPage > 0) page = _this.topPage+1;
                else page =  parseInt(App.Catalog.PAGINATION.NavPageNomer)+1;
                if(page != parseInt(App.Catalog.PAGINATION.NavPageCount)){
                    _this.smoothLoad('start');
                }
            _app.post({
                url:_app.AJAX_DIR+'?'+params.sort,
                data:{
                    handler:'catalog',
                    func:'loadPage',
                    page:page,
                    componentParams:_this.PARAMS
                }
            }, function(response){
                // alert(response.data.html);
                if(!block.length){
                    alert('block product list not found.. ');
                    return false;
                }
                $(response.data.html).appendTo(block);
                _this.smoothLoad('stop');

                history.pushState({}, '', '?top='+App.Catalog.PAGINATION.NavPageNomer);
                if(callback && typeof(callback) === 'function'){
                    callback();

                }

                return true;

            });
        },
        getReLoad: function(params, callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main);

            _app.post({
                url:_app.AJAX_DIR+'?'+params.sort,
                data:{
                    handler:'catalog',
                    func:'loadPage',
                    page:App.Catalog.PAGINATION.NavPageNomer,
                    componentParams:_this.PARAMS
                }
            }, function(response){
                if(!block.length){
                    alert('block product list not found.. ');
                    return false;
                }
                block.html(response.data.html);
                if(callback && typeof(callback) === 'function'){
                    callback();

                }

                return true;

            });
        },
        getBuy: function(params){
            return params;
        },
        initAnimOnScroll: function(){
            var _this = this;
            if($(_this.block.main).length){
                new AnimOnScroll(document.getElementById('catalog_products_list'), {
                    minDuration : 0.4,
                    maxDuration : 0.7,
                    viewportFactor : 0.2
                });
            }
        },
        checkItemInFavorites:function () {
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
        scrollToViewed:function(){
            var  _this = this,
                hash = location.hash.replace('#',''),
                top, height = 0,
                margin_top = 50;
            if(hash != ''){
                top = $('#'+hash).offset().top-margin_top;
                $('html, body').animate({ scrollTop: top}, 500);
            }
        },
        urlParam:function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results==null){
               return null;
            }
            else{
               return decodeURI(results[1]) || 0;
            }
        },
        changeSizeType: function(code){
            var _this = this,
                name = '';
            $.each( $(_this.selector.size_in_overflow), function(i, span) {
                name = $(span).data('name_'+code);
                if($(name).length)
                    $(span).text(name);
            });
        },
        lazyLoadPhoto: function(){
            var _this = this;
            $(_this.selector.colorLabel).each(function(){
                var label = $(this),
                    photo = $(label).data('pic'),
                    thumb = $(label).data('thumb'),
                    href = $(label).data('url'),
                    photoStr = '',
                    id = $(label).data('id')
                    parent = label.closest(_this.block.product).find('.product_img');

                if(label.hasClass('checked') || parent.find('a[data-id="'+id+'"]').length)
                    return;

                if(photo && thumb){
                    photoStr = '<a href="'+href+'" class="lazyImages" data-id="'+id+'"><img src="'+photo+'"><img class="hover-thumb" src="'+thumb+'"></a>';
                    parent.append(photoStr);
                }
            });
        }
    };

})(window.App);

$(document).ready(function () {
    CatalogProductsListMain.init();
});
$(window).load(function(){
    CatalogProductsListMain.scrollToViewed();
    CatalogProductsListMain.lazyLoadPhoto();
});
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

        block:{
            main: '#catalog_products_list',
            pager: '#catalog_pagination',
            sort: '#catalog_sort',
            product: '.one-cat-item',
            numberPagination: '#catalog_pagination_num'
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
            overflow: '.overflow',
            numberPaginationLinks:'#catalog_pagination_num a',
            paginationRight:'#catalog_pagination_num .pagination_control.right',
            paginationLeft:'#catalog_pagination_num .pagination_control.left',
            birka:'.birka',
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
                        $(_this.block.product+'.fake').remove();
                        _this.initAnimOnScroll();
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
                            // console.log(page);
                            _this.pages.push(page);

                        })

                        $('#catalog_pagination_num .container li a').each(function () {
                            $(this).removeClass('circle');
                            // console.log('text' + $(this).text());
                            // console.log(_this.pages);
                            // console.log("inArray " + );
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
                    id = object.closest(_this.block.product).find(_this.selector.colorLabel+'[class="checked"]').data().id,
                    elite = object.closest(_this.block.product).hasClass('cat-50');
                    
                App.Catalog.getProduct({
                    id: id,
                    elite: elite,
                    handler: 'products.list',
                    component: _self.PARAMS
                }, function(){
                    _catalog.getModalSizeContent({
                        id: id,
                        elite: elite
                    }, function(){
                        _app.getStyler({object: $(_app.selector.styler)});
                        $(_catalog.modal.size.id).modal('show');
                    });
                });
            });
            $(_this.block.main).on('change', _this.selector.color, function(){
                var object = $(this);
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
                        _this.initAnimOnScroll();
                    });
                });
            });

            //autoload
            $(window).on('scroll', function () {
                var st = $(this).scrollTop();
                if (st > _this.lastScrollTop) {
                    if ($(window).scrollTop() > $(document).outerHeight(true) - 1000) {
                        if (_this.scroll == 0) {
                            if(App.Catalog.PAGINATION.NavPageNomer != App.Catalog.PAGINATION.NavPageCount){
                                $(_this.block.pager + ' a').trigger('click');
                                _this.scroll = 1;
                            }

                        }
                    }
                }
                _this.lastScrollTop = st;
            });
            
        },
        init:function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'script.js[CatalogProductListMain] init..'});
            
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
        getPager: function(){
            var _this = this,
                init = App.Catalog.PAGINATION.NavRecordCount-App.Catalog.PAGINATION.NavPageSize*App.Catalog.PAGINATION.NavPageNomer,
                pager = $(_this.block.pager);
            _this.pages = [];
            
            if(init <= 0){
                pager.hide();
                return false;
            }
            
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
                    onPageClick: function(pageNumber, event){
                        // console.log($(this));
                        if(!$(".anchor[name = page-"+pageNumber+"]").length > 0){
                            $(_this.selector.overflow).fadeIn();
                            App.Catalog.PAGINATION.NavPageNomer = pageNumber;
                            _this.getReLoad({
                                sort:$(this).data().sort
                            }, function(){
                                _this.initAnimOnScroll();
                                $(_this.selector.overflow).fadeOut();
                            });
                        } else {
                            $('a[href^="#"]').click(function () {
                                elementClick = $(this).attr("href");
                                destination = $(elementClick).offset().top;
                                if($.browser.safari){
                                    $('body').animate( { scrollTop: destination }, 1100 );
                                }else{
                                    $('html').animate( { scrollTop: destination }, 1100 );
                                }
                                return false;
                            });
                        }

                    }
                });




            }
            return true;
        },
        getLoad: function(params, callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main);

            App.Catalog.PAGINATION.NavPageNomer++;
            
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
                block.append(response.data.html);
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
        }
        
    };

})(window.App);

$(document).ready(function () {
    CatalogProductsListMain.init();
});
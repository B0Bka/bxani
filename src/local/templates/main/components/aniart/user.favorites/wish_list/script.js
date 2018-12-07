var CatalogProductListMain = (function(App){

    'use strict';

    window.App = App = App || {};

    return {
        //constants
        PARAMS:'',
        PAGINATION:{},

        block:{
            main:'#catalog_products_list',
            pager:'.more-items',
            sort:'#catalog_sort',
            fav_button_del: '.one-cat-thumb .del-fav',
            fav_list_item: '.one-news',
        },
        form:{
        },
        selector:{
            color:'.product_list_size label',
            size:'.prod-size label',
            sizeName:'#product_detail_size',
            buy:'#product_detail_buy a',
            basket:'.one-cat-by',

        },
        events:function(){
            var _app = App,
                _catalog = _app.Catalog,
                _basket = _app.Basket,
                _this = this;

            $(_this.block.pager).on('click', 'a', function(){
                _this.getLoad({
                    sort:$(this).data().sort
                }, function(){
                    _this.initAnimOnScroll();
                });
            });
            $(_app.block.main).on('change', _this.block.sort, function(){
                window.location.href=$(this).find(':selected').val();
            });
            $(_this.block.main).on('click', _this.selector.color, function(){
                _catalog.getModalSizeContent({
                    id:$(this).data().id
                }, function(){
                    _app.getStyler({object:$(_app.selector.styler)});
                    $(_catalog.modal.size.id).modal('show');
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
            $(document).on('click',_this.block.fav_button_del, function () {
                console.log(1);
                var productId= $(this).data('item');
                var dom_item = $(this).closest(_this.block.fav_list_item);
                _this.deleteProductFromfavorites(productId,dom_item);
                return false;
            });

            $(_this.block.main).on('click', _this.selector.basket, function(){
                var object = $(this),
                    id = object.closest(_this.block.fav_list_item).find('.del-fav').data().item;

                App.Catalog.getModalBuy({id: id});
            });

        },
        init:function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'script.js[CatalogProductListMain] init..'});

            _this.events();
        },
        deleteProductFromfavorites:function(productId,dom_item){
            var _app = App;
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
                            dom_item.toggle('slow');
                        }
                    }
                });
            }

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
                    handler:'favorites',
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
        /*initAnimOnScroll:function(){
            var _this = this;
            if($(_this.block.main).length){
                new AnimOnScroll(document.getElementById('catalog_products_list'), {
                    minDuration : 0.4,
                    maxDuration : 0.7,
                    viewportFactor : 0.2
                });
            }
        }*/
    };

})(window.App);

$(document).ready(function () {
    CatalogProductListMain.init();
});

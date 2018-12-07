(function(App){
    window.App = App = App || {};

    App.Catalog = {

        SIBLING: {},
        PARAMS:'',
        SIBLING_ELITE: {},
        PAGINATION: {},
        BLOG_PAGINATION: {},
        CURRENT_OFFER: 0,
        PRODUCT_ID: 0,
        productLoadPid: false,
        block: {
            grid: 'grid',
            main: '#catalog_products_list',
            item: '.one-cat-item',
            catalog: '.catalog',
            catalog_wrap: '.catalog-wrap-full'
        },
        selector: {
            basket: '.one-cat-by',
            set: '.banners',
            custom_filter_props: "#custom_filter_properties",
            multi_tit: '.multi-tit',
            multi_sel: '.multi-sel',
            size_change: '.size-set li',
            sizes: '.prod-size-left .one-color label',
        },
        modal: {
            block: {
                main: '.modal-dialog',
                fast_buy: '#size',
                set_modal: '#set-modal',
            },
            size: {
                id: '#size',
                body: '.modal-body.size-body',
                thumb: '.modal-size-thumb img',
                desc: '.modal-size-tit a',
                color_name: '.modal-size-name',
                color_img: '.modal-size-active span',
                sizes: '.prod-size-left',
                choose_sizes: '.one-color',
                buy: '#modal_product_by'
            }
        },
        setModal:{
            id: '#set-modal',
            body: '.modal-body.set-body .modal-tab',
            item: '#set-item'
        },
        favorites: {
            main: '.one-cat-info',
            fav_button_add: '.one-cat-fav',
            product_button_add: '.prod-fav a',
            product_popup_button_add: '.prod-fav-popup a',
            in_favorite: 'in-favorite',
        },
        set: {
            button: '.set-bt',
            reload: '.set-reload',
        },
        buy: {
            button: '.buy-bt',
        },
        seo_text: {
            block: '.section-description',
            height: 75
        },
        events: function(){
            var _app = App,
                _this = this,
                _self = this;
            var toggle = 0;

//избранное на странице товара
//             $('body').on('click', _this.favorites.product_button_add, function () {
//                 var func = '';
//                 _this.PRODUCT_ID = $(this).data('item');
//
//                 if($(this).hasClass(_this.favorites.in_favorite)){
//                     func = 'removeFromFavorite';
//                 } else {
//                     func = 'addToFavorite';
//                 }
//                 _this.addProduct2FavoriteList(_this.PRODUCT_ID, func, this);
//                 return false;
//             });
//избранное на модалке попапа
            $('body').on('click', _this.favorites.product_popup_button_add, function () {
                var func = '';
                _this.PRODUCT_ID = $(this).data('item');

                if($(this).hasClass(_this.favorites.in_favorite)){
                    func = 'removeFromFavorite';
                } else {
                    func = 'addToFavorite';
                }
                _this.addProduct2FavoriteList(_this.PRODUCT_ID,func, this);
                return false;
            });
//действия в модал (кнопка слева)
            $('body').on('click', _this.modal.block.main + ' ' + _this.modal.size.choose_sizes, function () {
                $('body').find(_this.modal.block.main + ' ' + _this.modal.size.buy).removeClass('disabled');
                $(_this.modal.block.main).find('.buy-error').fadeOut();
            });

            $('body').on('click', _this.modal.block.main + ' ' + _this.modal.size.buy, function () {
               if($(this).hasClass('disabled')){
                   $(this).closest('.modal-by').find('.buy-error').fadeIn();
               }

            });

            $('body').on('click', _this.set.button, function () {
                _this.set_id = $(this).data('id');
                _this.getModalSet({id: _this.set_id});
                return false;
            });

            $('body').on('click', _this.buy.button, function () {
                _this.id = $(this).data('id');
                _this.getModalBuy({id: _this.id});
                return false;
            });

            //show_small_pics - шаблон товара комплекта отличается только тем, что в нем не выводятся превью картинки слайдера
            $('body').on('click', _this.set.reload, function () {
                _this.id = $(this).data('item');
                if($(this).hasClass('set-preview-img')) parent = $('#set-item');
                    else parent = $(this).closest('.product-body');

                if($(parent).length)
                    show_small_pics = 'N';
                else {
                    show_small_pics = 'Y';
                    parent = $(this).closest('.modal-body');
                }
                // console.log(parent);
                _this.change_loader = new App.LoaderWidget($('#size .modal-content'));
                if($('#size .modal-content').length > 0){
                    $('.product-popup').animate({
                        opacity: 0,
                    }, 500, function () {
                        _this.change_loader.show();
                    })

                }
                _this.reloadModalSet({id: _this.id, small_pics: show_small_pics}, parent);
                return false;
            });
            $('body').on('click', _this.selector.basket,  function () {
                _this.showFastBuyModal();
            });
            $('body').on('click', _this.buy.button, function () {
                _this.showFastBuyModal();
            });
            $(_this.selector.custom_filter_props + ' > li > span').on('click', function (e) {
                if(e.target.className.indexOf("one-mob-filt-del") == -1){
                    $(this).closest('li').toggleClass('opened');
                }
            });

            $(_this.selector.multi_tit).on('click', function () {

                if(typeof ($(this)[0].toggle) == 'undefined'){$(this)[0].toggle = 0;}

                _this.alignFiltersZindex(this);
                // $(this)[0].toggle ++;
            });
            $('body').on('click', _this.selector.size_change, function () {
                var code = '';
                code = $(this).data('code');

                $.each( $(_this.selector.sizes), function(i, label) {
                    if($(label).hasClass('checked')){
                        $('#product_detail_size').text($(this).data('name_'+code));
                    }
                    $(label).find('span').text($(this).data('name_'+code));
                });

                $(_this.selector.size_change).removeClass('selected');
                $(this).addClass('selected');
            });
        },
        init: function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'catalog.js init..'});

            _this.events();

            _this.getAnimOnScroll();

            //_this.setSeoTextHeight(); Скрывание сео текста
        },
        setProductId: function(data){
            return this.PRODUCT_ID = +data;
        },
        setSibling:function(data){
            data = data || {};
            if($.isEmptyObject(data)) return false;
            return this.SIBLING = $.extend(this.SIBLING, data);
        },
        setSiblingElite:function(data){
            data = data || {};
            if($.isEmptyObject(data)) return false;
            return this.SIBLING_ELITE = $.extend(this.SIBLING_ELITE, data);
        },
        setPagination:function(data){
            data = data || {};
            if($.isEmptyObject(data)) return false;
           return this.PAGINATION = data;
        },
        setBlogPagination:function(data){
            data = data || {};
            if($.isEmptyObject(data)) return false;
            return this.BLOG_PAGINATION = data;
        },
        addProduct2FavoriteList:function(productId, func, current_button){
            var _app = App,
                _this = this;
            if(productId) {
                App.post({
                    url: App.AJAX_DIR,
                    data:{
                        handler: 'favorites',
                        func: 'addToFavorite',
                        prodId: productId,
                    }
                }, function(response){
                     if(response.status == 'error'){
                        if(response.message.type == "IN"){
                            App.post({
                                url: App.AJAX_DIR,
                                data:{
                                    handler: 'favorites',
                                    func: 'removeFromFavorite',
                                    prodId: productId,
                                }
                            }, function(response){
                                if($(current_button).hasClass('in-favorite')){
                                    $(current_button).removeClass('in-favorite').addClass('not-favorite');
                                }
                            })
                        }
                    } else {
                        $(current_button).addClass('in-favorite').removeClass('not-favorite')
                    }
                   return true;
                });
            }

        },
        setCurrentOffer:function(data){
            return this.CURRENT_OFFER = +data;
        },
        getSibling:function(){
            return this.SIBLING;
        },
        getSiblingElite:function(){
            return this.SIBLING_ELITE;
        },
        getAnimOnScroll:function(){
            var grid = document.getElementById(this.block.grid);

            if(!grid) return false;

            new AnimOnScroll(grid, {
                minDuration: 0.4,
                maxDuration: 0.7,
                viewportFactor: 0.2
            });
        },
        getSize:function(params){
            params.offer = params.offer || 0;
            params.size = params.size || 0;
            params.block = params.block || {};
            params.buy = params.buy || {};
            params.inBasket = params.inBasket || false;
            params.availability = params.availability || {};
            params.availabilityBlock = params.availabilityBlock || {};

            if(params.offer <= 0) return false;
            if(params.block.length){
                if( params.sizeText >=0)
                    params.block.html(params.sizeText);
                else if(params.size >=0)
                    params.block.html(params.size);
            }
            if(params.buy.length){
                params.buy.removeClass('disabled');
            }
            if(params.availability.length){
                params.availability.removeClass('disabled');
            }
            if(params.availabilityBlock.length){
                params.availabilityBlock.hide().html('');
            }
            if(params.inBasket){
                params.buy.addClass('disabled').html('в корзине');
            }
            //remove alert class on size click
            if($('.animated-alert').length > 0){
                $('.prod-size-name').removeClass('animated-alert')
            }
            else params.buy.removeClass('disabled').html('купить');
            return this.CURRENT_OFFER = params.offer;
        },
        getBuy:function(params){

            params.buy = params.buy || {};
            params.basket = params.basket || {};
            params.offer = params.offer || 0;
            if(params.offer <= 0) {
                $('.prod-size-left .prod-size-name').addClass('animated-alert');
                $('#product_detail_size').text('Не выбран. Выберите, пожалуйста, размер');
                setTimeout(function () {
                    $('.prod-size-name').removeClass('animated-alert');
                }, 3500);
                // return false;
            }
            if(!params.buy.length || params.buy.hasClass('disabled')) return false;


            var _self = this,
                _basket = App.Basket;

            return _basket.add({
                offer:params.offer
            }, function(data){
                _basket.refreshList({
                    block:params.basket,
                    amount:data.count
                });
                //open basket list after buy
                if($(_self.modal.size.id).length){
                    $(_self.modal.size.id).modal('hide');
                }
                params.buy.addClass('disabled').html('в корзине');
                $("label[data-offer='"+params.offer+"']").data('in_basket', 'true');
                try { App.Gtm.GetAddToBasket(params.offer) } catch(e) {}
                params.basket.trigger('click');
            });
        },
        getModalSizeContent:function(params, callback){
            params.id = params.id || 0;
            params.elite = params.elite || false;
            if(params.id <= 0) return false;

            var _this = this,
                siblings = (params.elite ? this.SIBLING_ELITE : this.SIBLING),
                sibling = siblings[params.id],
                image =  sibling.img[0],
                blockSize = '';

            //console.log(siblings);
            $(_this.modal.size.buy).addClass('disabled').html('купить');
            $(_this.modal.size.thumb).attr('src', image.src);
            $(_this.modal.size.desc).text(sibling.name);
            $(_this.modal.size.desc).attr('href',sibling.url);
            $(_this.modal.size.color_name).text('Цвет: ' + sibling.color.NAME);
            $(_this.modal.size.color_img).css('background-image', 'url(' + sibling.color.FILE + ')');

            if(sibling.sizes){
                for(var i in sibling.sizes){
                    blockSize += ''
                        +'<div class="one-color">'
                        +'<label class=""'
                        +'data-offer="'+sibling.sizes[i].offerId+'" '
                        +'data-size="'+sibling.sizes[i].value+'"'
                        +'data-in_basket="'+sibling.sizes[i].inBasket+'"'
                        +'>'
                        +'<span>'+sibling.sizes[i].value+'</span>'
                        +'<input type="radio" name="'+sibling.id+'" value="'+i+'">'
                        +'</label>'
                        +'</div>';
                }
            }
            $(_this.modal.size.sizes).html(blockSize);

            if(callback && typeof(callback) === 'function'){
                callback();
            }
        },
        getModalSet:function(params){
            var _app = App,
                _this = this;
            // _this.loader.show();
            $('.popup-loader').show();
            _app.post({
                data:{handler:'catalog', func:'getSetHTML', params: params}
            }, function(response){
                if(response.status != 'success'){
                     alert('error get detail..');
                     return false;
                } else {

                    var showLoadedContent = function (callback) {
                        $(_this.setModal.body).html(response.data.html);
                        _this.loader = new App.LoaderWidget($('.popup-loader'));
                        _this.loader.show();
                        callback();
                    }

                    var load = function () {
                        $('#set-modal .set-items, #set-modal #set-item').css('opacity', '0');
                        // $('.popup-loader').show();
                        $(_this.setModal.id).modal('show');
                        setTimeout(function () {
                            $('.set-items ul').css('transform','translate3d(0px, 0px, 0px)');
                            $('#set-modal .set-items, #set-modal #set-item').animate({
                                opacity: 1,
                            }, 500);
                            $('.popup-loader').fadeOut();
                            _this.loader.reset();
                            if($('#set-modal .prod-car-big .bx-controls').length > 0){
                                $('#set-modal .prod-car-big .bx-controls').append("<span class = 'info'>Кликни на фото для увеличения</span>");
                            } else {
                                $('#set-modal .prod-car-big').append("<span class = 'info absolute'>Кликни на фото для увеличения</span>");
                            }

                        }, 1000)
                    }
                    showLoadedContent(load)
                }

            });
        },
        getModalBuy:function(params){
            var _app = App,
                _this = this;

            _app.post({
                data:{handler:'catalog', func:'getProductDetailHTML', params: params}
            }, function(response){
                if(response.status != 'success'){
                     alert('error get detail..');
                     return false;
                } else {
                    $(_this.modal.size.body).html(response.data.html);
                    $(_this.modal.size.id).modal('show');
                }

            });
        },
        reloadModalSet:function(params, el){
            var _app = App,
                _this = this;
            _this.loader = new App.LoaderWidget($('.popup-loader'));
            _this.loader.show();

            $('.popup-loader').show();
            $('#set-modal .set-items, #set-modal #set-item').css('opacity', '0');
            if($(window).width() <= 425){
                $('#set-modal').animate({ scrollTop: 0 }, 'slow');
            }

                _app.post({
                data:{handler:'catalog', func:'getProductDetailHTML', params: params}
            }, function(response){
                if(response.status != 'success'){
                     alert('error get detail..');
                     return false;
                } else {
                    $(el).html(response.data.html);
                    // _this.loader.hide();
                    setTimeout(function () {
                        if($('#size .modal-content').length > 0){
                            _this.change_loader.reset();
                        }
                        $('#set-modal .set-items, #set-modal #set-item, #size .product-popup').animate({
                            opacity: 1,
                        }, 500, function () {

                        });
                        $('.popup-loader').fadeOut();
                        _this.loader.reset();
                        $('#set-modal .prod-car-big .bx-controls').append("<span class = 'info'>Кликни на фото для увеличения</span>");
                    }, 1000)
                }

            });
        },
        selectSibling: function(params){

            params.id = params.id || 0;
            params.name = params.name || {};
            params.img = params.img || {};
            params.price = params.price || {};
            params.sizes = params.sizes || {};
            params.elite = params.elite || false;
            if(params.id <= 0){
                alert('Wrong product id '+params.id);
                return false;
            }

            var siblings = (params.elite ? this.SIBLING_ELITE : this.SIBLING),
                sibling = siblings[params.id],
                priceHtml = '',
                imgHtml = '';

            if(params.name.length){
                params.name.attr('href', sibling.url);
                params.name.text(sibling.name);
            }
            if(params.sizes.length){
                var sizesHTML = 'В наличии: ';
                sibling.sizes.forEach(function (item, i) {
                    sizesHTML += '<span class="one-avaliable-size">' + item.value + '</span>'
                });
                params.sizes.html(sizesHTML);
            }
            if(params.img.length){
                if(sibling.img){
                    imgHtml = '<a href="'+sibling.url+'" data-id="'+params.id+'">';
                    for(var i in sibling.img){
                        imgHtml += ''
                            +'<img '
                            +'class="'+(i > 0 ? 'hover-thumb' : '')+'" '
                            +'src="'+sibling.img[i].src+'" '
                            +'alt="'+sibling.name+'" '
                            +'title="'+sibling.name+'"'
                        +'/>';
                    }
                    imgHtml += '</a>';
                }

                params.img.attr('href', sibling.url);
                //params.img.html(imgHtml);
                params.img.find('a').hide();
                params.img.append(imgHtml);
                /*
                this.getProductLoader({
                    object: params.img
                }, false);
                */
            }
            if(params.price.length){
                if(sibling.available != "Y"){
                     priceHtml = '<span class="current-price">Нет в наличии</span>';
                }
                else{
                    if(sibling.isDiscount == "Y"){
                        priceHtml = '<span class="old-price">'+sibling.price+'</span>'+
                                    '<span class="current-price">'+sibling.priceDiscount+'</span>';
                    }
                    else{
                        priceHtml = sibling.price;
                    }
                }

                params.price.html(priceHtml);
            }
            this.productLoadPid = false;
        },
        getProduct: function(params, callback){
            params.id = params.id || 0;
            params.elite = params.elite || false;
            params.loader = params.loader || {};
            params.handler = params.handler || '';
            params.component = params.component || '';
            if(params.id <= 0){
                console.log('Wrong product id = '+params.id);
                return false;
            }
            if(!params.handler){
                console.log('Error: handler not set');
                return false;
            }
            if(!params.component){
                console.log('Error: component params not set');
                return false;
            }
            var _self = this,
                storage = (params.elite ? this.getSiblingElite() : this.getSibling());

            this.PRODUCT_ID = params.id;
            this.productLoadPid = true;

            this.getProductLoader({
                object: params.loader
            }, false);


                // при повторном открытии модалки товары в корзине не отображаются,
                // поэтому закомментировал этот код

            // if(storage[params.id]){
            //     if(callback && typeof(callback) === 'function'){
            //         callback();
            //     }
            //     return true;
            // }

            App.post({
                url: App.AJAX_DIR,
                data:{
                    handler: params.handler,
                    func: 'getProduct',
                    product: params.id,
                    elite: (params.elite ? 'Y': 'N'),
                    componentParams: params.component
                }
            }, function(response){
                if(response.status == 'error'){
                    alert(response.message);
                    return false;
                }
                if(params.elite){
                    _self.setSiblingElite(response.data);
                }else{
                    _self.setSibling(response.data);
                }
                if(callback && typeof(callback) === 'function'){
                    callback();
                }
                return true;
            });
        },
        getProductLoader: function(params, init){
            params.object = params.object || {};
            init = init || false;

            if(!params.object.length){
                console.log('Error: loader object not set');
                return false;
            }

            var html = '',
                style = {};

            if(init){
                html = '<div></div>';
                style = {
                    height: params.object.parent().height(),
                    widht: params.object.parent().width()
                };
                params.object.hide();
                return params.object.parent().append(html).css(style);
            }
            params.object.parent().find('div').remove();
            return params.object.show();
        },
        //задержка пока не сформируется вертикальный слайдер в модал
        showFastBuyModal: function () {
            var _app = App,
                _this = this;
                _this.loader = new App.LoaderWidget($('#size .modal-content'));
                _this.loader.show();
                $(_this.modal.block.fast_buy + ' .product-popup').css('opacity','0');
                $(_this.modal.block.fast_buy).modal('show');
                setTimeout(function () {
                    _this.loader.reset();
                    $(_this.modal.block.fast_buy + ' .product-popup').animate({
                        opacity:1,
                    }, 300, function () {

                    });
                },1300)
        },
        setSeoTextHeight: function(){
            var _this = this;
            if($(_this.seo_text.block).length) {
                $(_this.seo_text.block).readmore({
                    speed: 500,
                    collapsedHeight: _this.seo_text.height,
                    moreLink: '<div class="catalog-description-more"><a href="#" class="readmore-link">...Читать далее</a></div>',
                    lessLink: '<div class="catalog-description-more" ' +
                    'style="margin-top: 0"><a href="#" class="readmore-link">Скрыть</a><div class="more">'
                });
            }
        },
        alignFiltersZindex: function(elem) {
            var _this = this;
            if ($(elem)[0].toggle % 2 == 0) {
                $(_this.selector.multi_sel + ' ul').css('z-index', 20);
                $(elem).siblings('ul').css('z-index', 200);
            }
            if (typeof $(elem)[0].toggle !== 'undefined') {
                $(elem)[0].toggle++;
            }
        },
        setCatalogBlockCenter: function () {
            var _this = this,
                catalogWidth,
                windowWidth;

            if($(_this.block.catalog).length <= 0)
                return false;

            catalogWidth = $(_this.block.catalog).width();
            windowWidth = $(_this.block.catalog_wrap).width();
            $(_this.block.catalog).css('margin-left', (windowWidth - catalogWidth) / 2);
        }
    };

})(window.App);

$(document).ready(function(){
    App.Catalog.init();
});
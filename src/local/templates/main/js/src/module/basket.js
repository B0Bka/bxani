(function(App){
    window.App = App = App || {};

    App.Basket = {
        //constants
        
        LIST_LOAD:false,
        
        block:{
            amount: '#basket_small_amount',
            list: '#basket_list',
            listAll: '#basket_list, .basket-pop, .basket-pop *',
            search: '.search-pop', //вынести в дальнейшем
        },
        form:{

        },
        selector:{
            list: '.bask-bt a, .close-basket',
            del: '.bask-delete a',
        },
        stickElement: {},
        events: function(){
            var _app = App,
                _self = this;
            
            $(document).click(function(e){
                if(!$(e.target).is(_self.block.listAll)){
                    $(_self.block.list).hide();
                }
            });
            
            $(this.selector.list).click(function(e){
                _self.getList({
                    block:$(_self.block.list), 
                    search:$(_self.block.search),
                    event:e
                });
            });
            
            $(this.block.list).on('click', this.selector.del, function(){
                _self.getDel({
                    id: $(this).data().id,
                    product_id: $(this).data().product_id,
                    amount: $(_self.block.amount),
                    list: $(_self.block.list)
                });
            });

//            _self.getStick();


        },
        init: function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'basket.js init..'});

            _this.events();
        },
        add:function(params, callback){
            var _app = App;
            _app.post({
                data:{handler:'basket', func:'add', offer:params.offer}
            }, function(response){
                if(response.status != 'success'){
                    return alert('error add to basket..');
                }
                if(callback && typeof(callback) === 'function'){
                    callback(response.data);
                    $('.bask-bt').removeClass('opacity');
                }
                //console.log(response);
            });
        },
        update:function(params, callback){
            var _app = App;
            _app.post({
                data:{
                    handler:'basket', 
                    func:'update', 
                    id:params.id,
                    quantity:params.quantity
                }
            }, function(response){
                if(response.status != 'success'){
                    return alert('error update basket..');
                }
                if(callback && typeof(callback) === 'function'){
                    callback(response.data);
                }
            });
        },
        del:function(params, callback){
            var _app = App;
            _app.post({
                data:{handler:'basket', func:'delete', id:params.id}
            }, function(response){
                if(response.status != 'success'){
                    return alert('error delete from basket..');
                }
                if(callback && typeof(callback) === 'function'){
                    callback(response.data);
                }
                //console.log(response);
            });
        },
        refreshList:function(params){
            //console.log(params);
            params.block = params.block || {};
            params.amount = params.amount || 0;
            params.list = params.list || {};
            params.reload = params.reload || false;
            if(!params.block.length) return false;
            params.block.html(this.getAmoutnBlock(params.amount));
            if(params.reload)
            {
                this.getListLoad(function(data){
                    params.list.html(data);
                });
            }else{
                this.delList();
            }
            
        },
        getListShow:function(params){
            params.search.fadeOut();
            params.block.fadeToggle();
            params.event.stopPropagation();
        },
        getListLoad:function(callback){
            var _app = App;
            
            _app.post({
                data:{handler:'basket', func:'getBasketList'}
            }, function(response){
                if(response.status != 'success'){
                     alert('error get basket list..');
                     return false;
                }
                if(callback && typeof(callback) === 'function'){
                    callback(response.data);
                }
                return true;
            });
        },
        getList:function(params){
            var _this = this;
            
            if(_this.LIST_LOAD){
                return _this.getListShow({
                    search:params.search, 
                    block:params.block,
                    event:params.event
                });
            }
            return _this.getListLoad(function(data){
                params.block.html(data);
                _this.getListShow({
                    search:params.search, 
                    block:params.block,
                    event:params.event
                });
                _this.LIST_LOAD = true;
            });
        },
        delList:function(){
            var _this = this;
            _this.LIST_LOAD = false;
            return $(_this.block.list).empty();
        },
        getDel:function(params){
            params.id = params.id || 0;
            params.product_id = params.product_id || 0;
            params.amount = params.amount || {};
            params.list = params.list || {};
            
            if(params.id <= 0) return alert('basket item not found..');
            
            var _self = this;
            if($("label[data-offer='"+params.product_id+"']").length)
                $("label[data-offer='"+params.product_id+"']").data('in_basket', '');
            return this.del({
                id:params.id
            }, function(data){
                try { App.Gtm.GetDelFromBasket(params.product_id) } catch(e) {}
                _self.refreshList({
                    block: params.amount,
                    amount: data.count,
                    list: params.list,
                    reload: true
                });

            });
        },
        getAmoutnBlock: function(amount){
            amount = amount || 0;
            if(amount > 0){
                return '<span>'+amount+'</span>';
            } else {
                $('.bask-bt').addClass('opacity');
                return '';
            }

        },
        getStickElements: function (page) {
            //basket , order
            var _this = this;
            var current_page = page || "";
            var stickElement = {};
            if(typeof current_page !== 'undefined'){
                if ( current_page == 'basket') {
                    stickElement.stickTo = '#basket-root';
                    stickElement.stickyEl = '#basket-total';
                }
                if(current_page == 'order') {
                    stickElement.stickTo = '#sale_order';
                    stickElement.stickyEl = '#sale_order .order-right';
                }
            } else {
                if ( $('body').find( "#basket-total" ).length) {
                    stickElement.stickTo = '#basket-root';
                    stickElement.stickyEl = '#basket-total';
                }
                if($('body').find('#sale_order .order-right').length) {
                    stickElement.stickTo = '#sale_order';
                    stickElement.stickyEl = '#sale_order .order-right';
                }
            }

            return stickElement;
        },
        getStick: function (params) {
            var _this = this;
            var sticky_element = _this.getStickElements(params);
            if ($(window).width() > 1091) {
                $(sticky_element.stickyEl).hcSticky({
                    top: 70,
                    stickTo: sticky_element.stickTo,
                });
            }
        },

    };

})(window.App);

$(document).ready(function(){
    App.Basket.init();
});
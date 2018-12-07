var BasketMain = (function(App){
    
    window.App = App = App || {};
    
    return {
        //constants
        CONTROLLER:'',
        TEMPLATE:'',
        PARAMS:'',
        
        block:{
            main:'#basket_main',
        },
        form:{

        },
        selector:{
            del:'.del-from a',
            quan:'.basket_quantity select',
            styler:'input, select'
        },
        events:function(){
            var _app = App,
                _basket = App.Basket,
                _this = this;
            
            $(_this.block.main).on('click', _this.selector.del, function(){
                _this.getDel({
                    id:$(this).data().id,
                    amount:$(_basket.block.amount)
                });
            });
            
            $(_this.block.main).on('change', _this.selector.quan, function(){
                _this.getCalculate({
                    id:$(this).data().id,
                    quantity:$(this).find('option:selected').val(),
                    basket:$(_basket.block.amount)
                });
            });
            
        },
        init:function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'script.js[BasketMain] init..'});
            
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
        refresh: function(callback){
            var _app = App,
                _this = this,
                block = $(_this.block.main);
            
            _app.post({
                url:_this.CONTROLLER,
                type:'html',
                data:{template:_this.TEMPLATE, ajax_mod:'N'}
            }, function(responce){
                if(!block.length){
                    alert('block basket not found.. ');
                    return false;
                }
                block.html(responce);
                
                if(callback && typeof(callback) === 'function'){
                    callback();
                }
                return true;
            });
        },
        getCalculate:function(params){
            params.id = params.id || 0;
            params.quantity = params.quantity || 0;
            params.basket = params.basket || {};
            if(params.id <= 0){
                alert('basket item not found..');
                return false;
            }
            if(params.quantity <= 0){
                alert('basket quantity not set..');
                return false;
            }
            var _app = App,
                _basket = App.Basket,
                _this = this;
        
            return _basket.update({
                id:params.id,
                quantity:params.quantity
            }, function(data){
                _basket.refreshList({
                    block:params.basket,
                    amount:data.count
                });
                _this.refresh(function(){
                    _app.getStyler({
                        object:$(_this.block.main).find(_this.selector.styler)
                    });
                });
            });
        },
        getDel:function(params){
            params.id = params.id || 0;
            params.amount = params.amount || {};
            if(params.id <= 0) return alert('basket item not found..');
            
            var _app = App,
                _basket = App.Basket,
                _this = this;
            
            return _basket.del({
                id:params.id
            }, function(data){
                _basket.refreshList({
                    block:params.amount,
                    amount:data.count
                });
                _this.refresh(function(){
                    _app.getStyler({
                        object:$(_this.block.main).find(_this.selector.styler)
                    });
                });
            });
        }
    };

})(window.App);

$(document).ready(function () {
    BasketMain.init();
});
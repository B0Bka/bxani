var SubscriptionMain = (function(App){
    
    window.App = App = App || {};
    
    return {
        //constants
        CONTROLLER:'',
        TEMPLATE:'',
        PARAMS:'',
        
        form:{
            main:'#sub_form'
        },
        selector:{
            message:'#sub_msg',
            submit:'#sub_add'
        },
        events: function(){
            var _app= App,
                _this = this;
            $(_this.form.main).on('click', _this.selector.submit, function(){
                _app.Subscription.add({
                    controller:_this.CONTROLLER,
                    template:_this.TEMPLATE,
                    params:_this.PARAMS,
                    form:$(_this.form.main),
                    msg:$(_this.selector.message)
                });
            });
        },
        init: function(){
            var _this = this;
            _this.events();
        },
        setTemplate: function(data){
            return this.TEMPLATE = data;
        },
        setParams: function(data){
            return this.PARAMS = data;
        },
        setController: function(data){
            return this.CONTROLLER = data;
        }
    };

})(window.App);

$(document).ready(function () {
    SubscriptionMain.init();
});
(function(App){
    window.App = App = App || {};

    App.Subscription = {
        //constants
        form:{
        },
        selector:{
            block: '#subscribeButtonBlock',
            button: '#subscribeButton',
            close: '#subscribeButton .sub-button-close'
        },
        events: function(){
            var _app = App,
                _this = this;

            $(document).on('click', _this.selector.close, function(e){
                e.preventDefault();
                e.stopPropagation();
                $(_this.selector.button).toggleClass('visible');
            });
        },
        init: function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'subscription.js init..'});
            
            _this.events();
        },
        add: function(data){
            
            data.controller = data.controller || '';
            data.template = data.template || '',
            data.params = data.params || {};
            data.form = data.form || {};
            data.msg = data.msg || {};
            
            if(!data.controller.length) return false;
            if(!data.template.length) return false;
            if(!data.params.length) return false;
            if(!data.form.length) return false;
            
            var _app = App,
                _auth = App.Auth,
                _this = this,
                form = data.form.submit().serializeArray(),
                isError = false,
                formData = {}, 
                key;
            
            $.each(form, function (i, val){
                key = val.name.split(/[-]/);
                formData[key[1]] = val.value;
            });
            isError = _auth.getInputError({
                object:data.form,
                data:form
            });
            if(isError) return false;
            
            _app.post({
                url:data.controller,
                data:{
                    ajax_mod:'Y', 
                    template:data.template,
                    params:data.params,
                    form:formData
                }
            }, function(response){
                data.msg.html(response.msg);
                if(response.status == 'success'){
                    return true;
                }else{
                    return _auth.getInputError({
                        object:data.form,
                        data:form,
                        error:true
                    });
                }
            });
        },
    };

})(window.App);

$(document).ready(function(){
    App.Subscription.init();
});
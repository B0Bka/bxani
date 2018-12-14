var Auth = (function(App){

    window.App = App = App || {};

    return {
        //constants
        form:{
            auth:'.auth_form',
        },
        selector:{
            submit:'.submit',
        },

        events: function(){
            var _app = App,
                _this = this;

            $(_this.form.auth).on('click', _this.selector.submit, function(){
                _this.getLogin({
                    form:$(_this.form.auth)
                });
            });
        },

        init:function(){
            var _app = App,
                _this = this;
            _this.events();
        },

        getLogin: function(params){
            var _app = App,
                _this = this,
                form = params.form.serializeArray();

            formData = App.Auth.normalizeData(form);

            _app.post({
                data:{handler:'auth', func:'getLogin', form:formData}
            }, function(response){
                if(response.status == 'success'){
                    return location.reload();
                }else{
                    return App.Auth.getInputErrorAjax({
                        object:params.form,
                        data:form,
                        error:response.message,
                    });
                }
            });
        },
    };
})(window.App);

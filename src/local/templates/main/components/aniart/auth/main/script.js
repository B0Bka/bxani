var Registration = (function(App){

    window.App = App = App || {};

    return {
        //constants
        form:{
            register:'.register_form_client',
        },
        selector:{
            submit:{
                register:'.submit',
            }
        },
        PARAMS:'',
        inProgress: false,

        events: function(){
            var _app = App,
                _this = this;

            $(_this.form.register).on('click', _this.selector.submit.register, function(){
                form = $(_this.form.register);
                _this.getRegister({
                    form:form
                });
            });
        },

        init:function(){
            var _app = App,
                _this = this;
            _this.events();
        },
        setParams: function(data){
            return this.PARAMS = data;
        },
        getRegister: function(params){
            var _app = App,
                _this = this,
                form = params.form.serializeArray(),
                formData = {};
            formData = App.Auth.normalizeData(form);
            _app.post({
                data:{handler:'register', func:'getRegister', form:formData, 'component': _this.PARAMS, 'type': 'client'}
            }, function(response){
                if(response.status == 'success'){
                    return location.reload();
                }else{
                    return App.Auth.getInputErrorAjax({
                        object:params.form,
                        data:params.form,
                        error:response.message
                    });
                }
            });
            return false;
        },
    };
})(window.App);

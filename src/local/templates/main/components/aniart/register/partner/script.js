var RegistrationPartner = (function(App){

    window.App = App = App || {};

    return {
        //constants
        form:{
            register:'.register_form_partner',
        },
        selector:{
            whatsapp_checkbox:'.whatsapp_checkbox',
            submit:{
                register:'.submit',
            },
            error:'.input-error',
        },
        PARAMS:'',
        whatsapp_status:false,
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
            $(_this.form.register).on('click', _this.selector.whatsapp_checkbox, function(){
                input = $(this).parents('form').find('.whatsapp-input');
                $(input).toggle().val('');
                _this.whatsapp_status = !_this.whatsapp_status;
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
                data:{handler:'register', func:'getRegister', form:formData, 'component': _this.PARAMS, 'type': 'partner', 'whatsapp' : _this.whatsapp_status}
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

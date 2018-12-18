var RestorePassword = (function(App){

    window.App = App = App || {};

    return {
        //constants
        form:{
            restore:'.restore_form',
        },
        selector:{
            submit:'.submit',
        },

        events: function(){
            var _app = App,
                _this = this;

            $(_this.form.restore).on('click', _this.selector.submit, function(){
                _this.getRestore({
                    form:$(_this.form.restore)
                });
            });
        },

        init:function(){
            var _app = App,
                _this = this;
            _this.events();
        },

        getRestore: function(params){
            var _app = App,
                _this = this,
                form = params.form.serializeArray();

            formData = App.Auth.normalizeData(form);

            _app.post({
                data:{handler:'restore', func:'getRestore', form:formData}
            }, function(response){
                if(response.status == 'success'){
                    //return location.reload();
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

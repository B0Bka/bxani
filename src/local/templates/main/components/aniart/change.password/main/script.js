var ChangePassword = (function(App){

    window.App = App = App || {};

    return {
        //constants
        form:{
            change:'.change_form',
        },
        selector:{
            submit:'.submit',
        },

        events: function(){
            var _app = App,
                _this = this;

            $(_this.form.change).on('click', _this.selector.submit, function(){
                _this.getChange({
                    form:$(_this.form.change)
                });
            });
        },

        init:function(){
            var _app = App,
                _this = this;
            _this.events();
        },

        getChange: function(params){
            var _app = App,
                _this = this,
                form = params.form.serializeArray();

            formData = App.Auth.normalizeData(form);

            _app.post({
                data:{handler:'change', func:'getChange', form:formData}
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

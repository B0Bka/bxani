(function(App){
    window.App = App = App || {};

    App.Auth = {
        //constants
        block:{
            modal:'#myModal',
            pass:{
                change:'.forg-block'
            },
            soc:'.social-auth'
        },
        form:{
            login:'#auth_login',
            register:'.register_form',
            forgot:"#auth_forgot"
        },
        selector:{
            popupButton:'.bt-1.modal-open',
            submit:{
                login:'#auth_submit',
                register:'.submit',
                forgot:'#forgot_submit',
                logout:'#auth_logout'
            },
            phone:'input[name="AUTH-PHONE"]',
            pass:{
                change:'.forg-bt'
            },
            error:'.input-error',
            restoreOk: '#restoreOk'
        },
        events: function(){
            var _app = App,
                _this = this;

            $(_this.block.modal).on('click', _this.selector.pass.change, function(){
                $(_this.block.pass.change).fadeToggle();
            });

            $(_this.form.login).on('click', _this.selector.submit.login, function(){
                _this.getLogin({
                    form:$(_this.form.login)
                });
            });

            $(_this.form.login).keypress(_this.selector.submit.login, function (e) {
                if(e.which == 13) {
                    _this.getLogin({
                        form:$(_this.form.login)
                    });
                }
            });

            $(_this.form.register).on('click', _this.selector.submit.register, function(){
                _this.getRegister({
                    form:$(_this.form.register)
                });
            });

            $(_this.form.forgot).on('click', _this.selector.submit.forgot, function(){
                _this.getForgot({
                    form:$(_this.form.forgot)
                });
            });

            $(_app.block.main).on('click', _this.selector.submit.logout, function(){
                _this.getLogout();
            });

            $(_app.block.main).on('click', _this.selector.error, function(){
                _this.clearError({input: $(this)});
            });
            /*
            ** Кнопки соц сетей подгружаю динамически, чтобы не было запросов при каждой загрузке страницы
             */
            $(_app.block.main).on('click', _this.selector.popupButton, function(){
                _this.getSocIcons();
            });
        },
        init: function(){
            var _app = App,
                _this = this;
            _app.getLogInit({message:'auth.js init..'});

            /*_app.getPhoneMask({
                object:$(_this.form.register).find(_this.selector.phone)
            });*/
            _this.events();
        },
        getLogin: function(params){
            var _app = App,
                _this = this,
                form = params.form.submit().serializeArray(),
                isError = false,
                formData = {},
                key;

            $.each(form, function (i, val){
                key = val.name.split(/[-]/);
                formData[key[1]] = val.value;
            });

            /*
            проверка заполнености полей перешла в бэкенд
            isError = _this.getInputError({
                object:params.form,
                data:form
            });
            if(isError) return false;
            */

            _app.post({
                data:{handler:'auth', func:'getLogin', form:formData}
            }, function(response){
                if(response.status == 'success'){
                    return location.reload();
                }else{
                    return _this.getInputErrorAjax({
                        object:params.form,
                        data:form,
                        error:response.message,
                    });
                }
            });
        },
        getRegister: function(params){
            var _app = App,
                 _this = this,
                form = params.form.serializeArray(),
                formData = {};
            formData = _this.normalizeData(form);
             _app.post({
                 data:{handler:'register', func:'getRegister', form:formData}
             }, function(response){
                 if(response.status == 'success'){
                     return location.reload();
                 }else{
                     return _this.getInputErrorAjax({
                         object:params.form,
                         data:params.form,
                         error:response.message
                     });
                 }
             });
            return false;
        },
        getForgot: function(params){
            var _app = App,
                _this = this,
                form = params.form.submit().serializeArray(),
                formData = {};

            $.each(form, function (i, val){
                key = val.name.split(/[-]/);
                formData[key[1]] = val.value;
            });
            _app.post({
                data:{handler:'auth', func:'getForgot', form:formData}
            }, function(response){
                if(response.status == 'success'){
                    _this.showRestoreOk({message:response.data.message});
                }else{
                    return _this.getInputErrorAjax({
                        object:params.form,
                        data:form,
                        error:response.message
                    });
                }
            });
        },
        getLogout: function(){
            var _app = App;
            _app.post({
                data:{handler:'auth', func:'getLogout'}
            }, function(response){
                return location.href = '/';
            });
        },
        getSocIcons: function(){
            var _app = App,
                _this = this;
            _app.post({
                data:{handler:'auth', func:'getSocIcons'}
            }, function(response){
                if(response.status == 'success') {
                 $(_this.block.soc).html(response.data);
                }
                return response;
            });
        },
        getCheckValue: function(value){
            if(!value.length) return false;
            return true;
        },
        getInputErrorAjax: function(params){
            params.object = params.object || {};
            params.data = params.data || [];
            params.error = params.error || [];
            params.prefix = params.prefix || '';
            if(!params.object.length) return false;
            if(!params.data.length) return false;
            console.log(params.error);
            var _this = this,
                code,
                isError = false;
            _this.clearErrors({form: params.object});
            for(code in params.error){
                console.log(code);
                _this.setError({code:code, text:params.error[code], object:params.object, prefix: params.prefix});
            }
            if(params.error.TYPE == 'ERROR'){
                _this.setSystemError({text:params.error.MESSAGE, object:params.object});
            }

            return true;

        },

        setError: function(params){
            params.text = params.text || [];
            params.code = params.code || '';
            params.object = params.object || {};
            var input = $(params.object).find('input[name="'+params.prefix+params.code+'"]'),
                parent = $(input).parent(),
                span = $(params.object).find('#'+params.code+'_error') || {};
            if(span.length){
                input.addClass('input-error');
                $(span).show().text(params.text);
            }
            else{
                input.addClass('input-error');
                $('<span>', { id: params.code+'_error', text: params.text, class: 'error-mes'}).appendTo(parent).show();
            }
        },
        setSystemError: function(params){
            params.text = params.text || [];
            params.object = params.object || {};
            var _this = this,
                span = $(params.object).find('.system-error') || {},
                errorText = _this.stripTags(params.text);
            if(span.length) {
                $(span).show().text(errorText);
            }
        },
        showRestoreOk: function(params){
            params.message = params.message || '';
            var _this = this;
            $(_this.selector.restoreOk).text(params.message);
        },
        clearErrors: function (params){
            params.form = params.form || {};
            $(params.form).find('.input-error').removeClass('input-error');
            $(params.form).find('.error-mes').hide();
            $(params.form).find('.system-error').hide();
        },
        clearError: function(params){
            params.input = params.input || {};
            $(params.input).removeClass('input-error');
            $(params.input).parent().find('.error-mes').hide();
        },
        stripTags: function (strInputCode) {
            return strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
        },
        normalizeData: function(data){
            var res = {},
                key = '';
            $.each(data, function (i, val){
                key = val.name;
                res[key] = val.value;
            });
            return res;
        },

        getInputError: function(params){
            params.object = params.object || {};
            params.data = params.data || [];
            params.error = params.error || false;
            params.text = params.text || false;
            params.type = params.type || false;
            if(!params.object.length) return false;
            if(!params.data.length) return false;
            var _this = this,
                input,
                isError = false,
                check = false;
            $.each(params.data, function (i, val){
                input = params.object.find('input[name="'+val.name+'"]');

                if(!input.length) return true;

                if(input.data().req != 1) return true;

                if(!params.error){
                    check = _this.getCheckValue(val.value);
                }
                if(!check){
                    input.css({'background-color':'pink'});
                    isError = true;
                }else{
                    input.css({'background-color':'white'});
                }
            });
            if(isError) return true;
            return false;
        },

    };

})(window.App);

$(document).ready(function(){
    App.Auth.init();
});
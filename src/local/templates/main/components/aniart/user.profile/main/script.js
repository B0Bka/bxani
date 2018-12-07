var UserProfileMain = (function(App){
    
    window.App = App = App || {};
    
    return {
        //constants
        CONTROLLER:'',
        TEMPLATE:'',
        PARAMS:'',
        CHILDREN:0,
        LIMIT_CHILD:5,
        
        block:{
            children:'#personal_children',
            child:'.one-order-form',
            delChild:'.del-input'
        },
        form:{
            main:'#user_profile'
        },
        selector:{
            message:'#msg',
            submit:'#personal_save',
            addChild:'.add-inp a',
            delChild:'.del-input a',
            showpassword:'.toggle-password',
        },
        events: function(){
            var _app = App,
                _this = this;
            
            $(_this.form.main).on('click', _this.selector.addChild, function(){
                _this.getChildHtml({
                    block:$(_this.block.children),
                    add:$(_this.selector.addChild),
                    del:$(_this.block.delChild)
                });
            });
            
            $(_this.form.main).on('click', _this.selector.delChild, function(){
                _this.delChildHtml({
                    object:$(this).closest(_this.block.child),
                    add:$(_this.selector.addChild)
                }, function(id){
                    _this.showDelPrevious({
                       object: $(_this.form.main).find('input[name="PROFILE-CHILD_'+id+'"]'),
                       del:_this.block.delChild
                    });
                });
            });
            
            $(_this.form.main).on('click', _this.selector.submit, function(){
                _this.save({
                    controller:_this.CONTROLLER,
                    template:_this.TEMPLATE,
                    params:_this.PARAMS,
                    form:$(_this.form.main),
                    msg:$(_this.selector.message)
                });
            });

            $(_this.form.main).on('click', _this.selector.showpassword, function(e){
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password") {
                input.attr("type", "text");
                } else {
                input.attr("type", "password");
                }
                e.preventDefault();
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
        },
        setChildren: function(data){
            
            return this.CHILDREN = (+data==0?1:+data);
        },
        getChildHtml: function(params){
            params.block = params.block || {};
            if(!params.block.length) return false;
            var _this = this,
                html;
            
            ++_this.CHILDREN;
            if(_this.CHILDREN > _this.LIMIT_CHILD){
                return false;
            }else if(_this.CHILDREN == (_this.LIMIT_CHILD)){
                params.add.hide();
            }
            
            html = ''
            +'<div class="one-order-form">'
            +'<div class="one-order-tit">Дети</div>'
            +'<input name="PROFILE-UF_CHILD_'+_this.CHILDREN+'" type="text" data-req="0"/>'
            +'<div class="del-input"><a href="javascript:void(0);">УДАЛИТЬ</a>'
            +'</div>'
            +'</div>';
            
            params.del.hide();
            params.block.append(html);
            return true;
        },
        delChildHtml: function(params, callback){
            params.object = params.object || {};
            if(!params.object.length) return false;
            var _this = this;
            
            if(_this.CHILDREN < 0){
                return false;
            }else if(_this.CHILDREN == (_this.LIMIT_CHILD)){
                params.add.show();
            }
            --_this.CHILDREN;
            params.object.remove();
            if(callback && typeof(callback) === 'function'){
                callback(_this.CHILDREN);
            }
            return true;
        },
        showDelPrevious: function(params){
            params.object = params.object || {};
            if(!params.object.length) return false;
            
            return params.object.siblings(params.del).show();
        },
        save: function(data){
            data.controller = data.controller || '';
            data.template = data.template || '';
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
                formData = {},
                key;

            $(_this.form.main).append("<div class='overflow-in-checkout'></div>").fadeIn();
            _this.loader = new App.LoaderWidget($(_this.form.main));

            $.each(form, function (i, val){
                key = val.name.split(/[-]/);
                if(key[1]){
                    formData[key[1]] = val.value;
                }
            });
            isError = _auth.getInputErrorAjax({
                object:data.form,
                data:form
            });
            if(!isError) return false;
            
            _app.post({
                url:data.controller,
                data:{
                    ajax_mod:'Y', 
                    template:data.template,
                    params:data.params,
                    func:'ajaxSave',
                    form:formData
                }
            }, function(response){
                $(_this.form.main).find('.overflow-in-checkout').fadeOut().remove();
                    _this.loader.hide();
                if(response.status == 'success'){
                    $(_this.selector.message).html(response.data);
                }else{
                    return _auth.getInputErrorAjax({
                        object:data.form,
                        data:form,
                        error:response.data,
                        prefix:'PROFILE-'
                    });
                }
            });
        }
    };

})(window.App);

$(document).ready(function () {
    UserProfileMain.init();
});
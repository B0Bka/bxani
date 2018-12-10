(function(App){
    window.App = App = App || {};

    App.Personal = {
        
        MENU_ITEM:1,
        
        block:{
            menu:'#personal_menu',
        },
        form:{
            profile:'#user_profile'
        },
        selector:{
            menuItem:'li:not(.active, .log-out)',
            birthday:'input[name="PROFILE-UF_BIRTHDAY"]',
            phone:'input[name="PROFILE-PHONE"]'
        },
        cookie:{
            menuItem:'personalMenuItem'
        },
        events: function(){
            var _app = App,
                _this = this;
            
            $(_this.block.menu).on('click', _this.selector.menuItem, function(){
                _this.getTabMenu({object:$(this)});
            });

            $(_this.form.profile).on('click', _this.selector.phone, function () {
                $(_this.selector.phone).mask("+38 (999) 999-99-99", {placeholder: "+38 (___) ___-__-__"});
            });




        },
        init: function(){
            var _app = App,
                _this = this;

            _app.getLogInit({message:'personal.js init..'});
            /*
            _this.getTabMenu({
                object:$(_this.block.menu+' li[data-id="'+_this.MENU_ITEM+'"]')
            });


            _this.getBirthday({
                block:$(_this.form.profile).find(_this.selector.birthday)
            });
            */

            _this.events();
        },
        /*
        getSelectedTab:function(){
            var _this = this,
                hash = location.hash.slice(1);
            
            if(+hash > 0){
                $.cookie(_this.cookie.menuItem, hash);
            }
            if($.cookie(_this.cookie.menuItem)){
                return _this.MENU_ITEM = $.cookie(_this.cookie.menuItem);
            }
            return _this.MENU_ITEM;
        },
        */
        getTabMenu:function(params){
            params.object = params.object || {};
            if(!params.object.length) return false;
            
            params.object
                .addClass('active')
                .siblings()
                .removeClass('active')
                .parents('.lk-page')
                .find('.hist-cont-in')
                .removeClass('active')
                .eq(params.object.index())
                .addClass('active');
            
            var _this = this,
                item = params.object.data().id;
            
            $.cookie(_this.cookie.menuItem, item);
        },
        getBirthday:function(params){
            params.block = params.block || {};
            
            if(!params.block.length) return false;
            
            return params.block.bootstrapBirthday({
                maxAge:80,
                minAge:5,
                monthFormat:'long',
                dateFormat:'bigEndian',
                placeholder:true,
                text: {
                    year:'Год',
                    month:'Месяц',
                    day:'День',
                    months:{
                        long:[
                            'Январь',
                            'Февраль',
                            'Март',
                            'Апрель',
                            'Май',
                            'Июнь',
                            'Июль',
                            'Август',
                            'Сентябрь',
                            'Октябрь',
                            'Ноябрь',
                            'Декабрь'
                        ]
                    }
                },
                widget: {
                    wrapper: {
                        tag: 'div',
                        class: 'select-data'
                    },
                    wrapperYear: {
                        use: true,
                        tag: 'div',
                        class: 'one-sel-year'
                    },
                    wrapperMonth: {
                        use: true,
                        tag: 'div',
                        class: 'one-sel-month'
                    },
                    wrapperDay: {
                        use: true,
                        tag: 'div',
                        class: 'one-sel-day'
                    },
                    selectYear: {
                        name: 'birthday[year]',
                        class: ''
                    },
                    selectMonth: {
                        name: 'birthday[month]',
                        class: ''
                    },
                    selectDay: {
                        name: 'birthday[day]',
                        class: ''
                    }
                },
                onChange: function(){
                    console.log($(this));
                }
            });
        },
    };

})(window.App);

$(document).ready(function(){
    App.Personal.init();
});
(function(App){
    window.App = App = App || {};

    if(!window.jQuery){
        alert('jquery is not found');
    }
    
    //console.log(App);
        
    $.extend(App, {
        
        AJAX_DIR:'/ajax/common.php',

        block:{
            main:'body'
        },
        selector:{
            styler:'.wrapper input:not(input[type="checkbox"]):not(".no-styler"), .wrapper select, .modal.fade input'
        },
        init: function(){
            var _this = this;

            _this.getLogInit({message:'app.js init..'});

            _this.events();
        },
        events: function(){

        },
        post: function(params, callback){
            var _this = this;

            params.type = params.type || 'json';
            params.url = params.url || _this.AJAX_DIR;
            params.data = params.data || {};
            
            return $.ajax({
                type: 'POST',
                url: params.url,
                data: params.data,
                dataType: params.type,
                cache: false,
                success: function(responce){
                    if(callback && typeof(callback) === 'function'){
                        callback(responce);
                    }
                }
            });
        },
        getLogInit:function(params){
            var _this = this;
            params.active = params.active || _this.LOG_INIT;
            params.message = params.message || 'not found';
            if(!params.active) return false;
            return console.log(params.message);
        }
    });

})(window.App);


$(document).ready(function(){
    App.init();
});
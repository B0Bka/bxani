(function(App){
    window.App = App = App || {};

    App.Final = {
        init: function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'final.js init..'});
            
            if(window.location.pathname.split('/')[1] != 'catalog'){
                _app.getStyler({object:$(_app.selector.styler)});
            } else {
                _app.getStyler({object:$('.wrapper select, .modal.fade input')});
            }
        }
    };

})(window.App);

$(document).ready(function(){
    App.Final.init();
});
(function(App){
    window.App = App = App || {};

    App.Final = {
        init: function(){
            var _app = App,
                _this = this;
            
            _app.getLogInit({message:'final.js init..'});
            
        }
    };

})(window.App);

$(document).ready(function(){
    App.Final.init();
});
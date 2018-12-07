var SubscriptionListMain = (function(App){
    
    window.App = App = App || {};
    
    return {

        form:{
        },
        selector:{
            sub:'.subs-list label',
            wrepperSub: '.subs-list',
        },
        events: function(){
            var _app= App,
                _this = this;

            $(_app.block.main).on('click', _this.selector.sub, function(){
										_this.addSubscribe({
										subscribe:$(_this.selector.wrepperSub).data().user_subscription_id,
										rubricList: _this.getListOfCheckedRubrics()
								});
            });
        },
        init: function(){
            var _app = App,
                _this = this;

					  _app.getLogInit({message:'script.js[SubscriptionListMain] init..'});
            _this.events();
        },
        addSubscribe: function(params){
            var _app= App,
            _this = this;
        	
            params.subscribe = params.subscribe || false;

						_app.post({
								url:App.AJAX_DIR,
										data:{
											handler:'subscribe',
											func:'subscribeUserOnRubric',
											user_subscription_id: params.subscribe,
											rubric_list: params.rubricList
										}
								}, function(response) {
									if(response.data.ID) {
										$(_this.selector.wrepperSub).data('user_subscription_id',response.data.ID);
									}
						});

        },
				getListOfCheckedRubrics: function() {
						var inputList = $('.subs-list').find('input:checked');
						var length = inputList.length;
						var rubricList = [];

						for(var i = 0; i < length; i++) {
							rubricList.push($(inputList[i]).data().rubric_id);
						}

						return rubricList;
				}

    };

})(window.App);

$(document).ready(function () {
	SubscriptionListMain.init();
});
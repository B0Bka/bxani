(function (App) {

    window.App = App = App || {};

    var CheckoutWidget = App.CheckoutWidget = App.Widget.extend({

        defaults: function () {
            return {
                ajaxHandler: CheckoutWidget.ajaxHandler,
                isAuth: false,
                props: {},
                paySystems: {},
                deliveries: {},
                authForm: {},
                componentParams: ''
            }
        },

        initialize: function () {
            this.__initForm();
            this.__initBasket();
            this.__initUserProps();
            this.__initPaySystems();
            this.__initDeliveries();
            this.__initAuthForm();
            this.__initCoupon();
            this.loader = new App.LoaderWidget(this.$el.find('.radio-hide'));
        },

        __initForm: function () {
            this.$form = this.$el.find('form#checkout_main_form');
            this.$form.on('submit', $.proxy(this, 'submit'));
        },

        __initBasket: function(){
            var _self = this,
                _basket = App.Basket,
                basket;

            this.$el.find('.basket-product').each(function(){
                basket = new BasketWidget($(this));
                $(basket).on(':delete', function(e, id, productId){
                    _basket.del({
                        id:id,
                        productId:productId
                    }, function(data){
                        if(data.count == 0){
                            location.reload();
                        }

                        _basket.refreshList({
                            block:$(_basket.block.amount),
                            amount:data.count
                        });
                        App.Gtm.GetDelFromBasket(productId);
                        _self.refresh();
                    });
                });
            });
        },

        __initCoupon: function () {
            var $couponInput = $("#js_coupon_input");
            var $applyCouponButton = $("#js_apply_coupon");
            var $removeCouponButton = $("#js_remove_coupon");
            var _this = this;

            $applyCouponButton.on("click", function (event) {
                event.preventDefault();
                _this.sendForm({
                    func: 'processOrder',
                    action: 'enterCoupon',
                    coupon: $couponInput.val(),
                    signedParamsString: _this.componentParams
                }, function (response) {
                    this.$el.replaceWith(response.data.html);
                    $('input').css({'visibility': 'visible'});
                });
            });

            $removeCouponButton.on("click", function (event) {
                event.preventDefault();
                _this.sendForm({
                    func: 'processOrder',
                    action: 'removeCoupon',
                    coupon: $couponInput.val(),
                    signedParamsString: _this.componentParams
                }, function (response) {
                    this.$el.replaceWith(response.data.html);
                    $('input').css({'visibility': 'visible'});
                });
            });

            return false;
        },

        __initUserProps: function () {
            var _this = this;
            this.$el.find('.a-checkout-user-prop').each(function () {
                var prop = PropWidget.build($(this));
                _this.props[prop.code] = prop;

            });
        },

        __initPaySystems: function () {
            var _this = this;
            this.$el.find('.one-pay-radio').each(function () {
                var paySystem = new PaySystemWidget($(this));
                $(paySystem).on(':change', function (e, paySystem) {
                    _this.refresh();
                });
                _this.paySystems[paySystem.id] = paySystem;
            });
        },

        __initDeliveries: function () {
            var _this = this;
            this.$el.find('.a-checkout-delivery').each(function () {
                var delivery = DeliveryWidget.build($(this));
                $(delivery).on(':change', function (e, delivery) {
                    _this.refresh();
                });
                _this.deliveries[delivery.id] = delivery;
            });
        },

        __initAuthForm: function () {
            var _this = this;
            this.authForm = new AuthFormWidget(this.$el.find('#checkout_auth'), {
                ajaxHandler: this.ajaxHandler
            });
            $(this.authForm).on(':submit', function () {
                _this.authorize();
            });
        },

        authorize: function () {
            var _this = this;
            this.sendForm({
                func: 'authorize',
                do_authorize: 'Y',
                action: 'showAuthForm',
                save: 'Y',
                signedParamsString: this.componentParams
            }, function (response) {
                var isError = !!response.data.order['ERROR'];
                if (response.status == 'ok' && !isError) {
                    window.location.reload();
                }
                if (isError) {
                    _this.authForm.showErrors(response.data.order['ERROR']['AUTH'].join('<br />'));
                }
                return false;
            }, this.authForm.$form);
        },

        refresh: function () {

        	var _this = this;
        	_this.loader.show();
            $('body').append("<div class='overflow-in-checkout'></div>").fadeIn();
            this.sendForm({
                func: 'processOrder',
                action: 'processOrder',
                signedParamsString: this.componentParams
            }, function (response) {
                $('body').find('.overflow-in-checkout').fadeOut().remove();
                // _this.loader.hide();
                this.$el.replaceWith(response.data.html);
                $('input, select').css({'visibility': 'visible'});
                App.getStyler({object: $(App.selector.styler)});
            });

        },

        submit: function (e) {
            if (e) {
                e.preventDefault();
            }
            var data = {
                func: 'processOrder',
                action: 'processOrder',
                json: 'Y',
                signedParamsString: this.componentParams,
                confirmorder: 'Y'
            };
            this.sendForm(data, function (response) {
                if (response.data.html['redirect']) {
                    var url = response.data.html['redirect'].split('?');
                    window.location = '/order/?' + url[1];
                    //window.location = response.data.html['redirect'];
                    return;
                }
                this.$el.replaceWith(response.data.html);
                $('input').css({'visibility': 'visible'});
            });
        },

        sendForm: function (data, callback, $form) {
            var _this = this;
            if (CheckoutWidget.sendFormTimeout) {
                clearTimeout(CheckoutWidget.sendFormTimeout);
            }
            CheckoutWidget.sendFormTimeout = setTimeout(function () {
                _this.__sendForm(data, function (response) {
                    if (response.status == 'ok') {
                        callback.apply(_this, [response]);
                    } else {
                        alert(response.message);
                    }
                }, $form);
            }, 500);
        },

        __sendForm: function (data, callback, $form) {
            data = data || {};
            callback = callback || $.noop;
            $form = $form || this.$form;

            var request = $form.serialize();
            for (var paramName in data) {
                var paramValue = data[paramName];
                request += '&' + paramName + '=' + paramValue;
            }
            return $.post(this.ajaxHandler, request, callback, 'json');
        }

    }, {
        lang: 'ru',
        ajaxHandler: App.AJAX_DIR + '?handler=order',
        sendFormTimeout: false
    });

    var BasketWidget = App.Widget.extend({
        defaults: function(){
            return {
                $delete: null
            };
        },
        initialize: function(){
            var _self = this;
            this.$delete = this.$el.find('.bask-delete a');
            this.$delete.on('click', function(event){
                $(_self).trigger(':delete', [$(this).data().id, $(this).data('product_id')]);
            });
        }
    });

    var BaseRadioWidget = App.Widget.extend({
        defaults: function () {
            return {
                $radio: null
            }
        },

        initialize: function () {
            var _this = this;
            this.$radio = this.$el.is('[type="radio"]') ? this.$el : this.$el.find('input[type="radio"]');
            this.$radio.on('change', function () {
                //_this.__initAdditional($(this));
                //console.log('radio:change');
                $(_this).trigger(':change', [_this]);
            });
            this.$radio.styler();
            _this.__initAdditional(this.$radio);
        },

        __initAdditional: function ($radio) {
            if ($radio.is(':checked')) {
                $radio.closest('.order-form-choose').find('.order-radio').removeClass('checked');
                $radio.closest('.order-radio').addClass('checked');
            } else {
                $radio.closest('.order-radio').removeClass('checked');
            }
        }

    });

    var PaySystemWidget = BaseRadioWidget.extend({
        defaults: function () {
            return {
                id: 0
            }
        },

        initialize: function () {
            PaySystemWidget.__super__.initialize.apply(this, arguments);
            var options = arguments[1] || {};
            this.id = options.id || this.$el.data('id');
        }
    });

    var DeliveryWidget = BaseRadioWidget.extend({
        defaults: function () {
            return {
                id: 0
            }
        },

        initialize: function () {
            DeliveryWidget.__super__.initialize.apply(this, arguments);
            var options = arguments[1] || {};
            this.id = options.id || this.$el.data('id');
        }
    }, {
        build: function ($el) {
            if ($el.find('#checkout_np_city').length) {
                return new NewPostDeliveryWidget($el);
            } else if ($el.find('#checkout_stores').length) {
                return new StoresDeliveryWidget($el);
            }
            return new DeliveryWidget($el);
        }
    });

    var StoresDeliveryWidget = DeliveryWidget.extend({

        defaults: function () {
            return {
                $schedule: null,
                storesWidget: null
            }
        },

        initialize: function () {
            StoresDeliveryWidget.__super__.initialize.apply(this, arguments);
            this.$schedule = this.$el.find('#checkout_stores_schedule');
            this.$storeAddress = this.$el.find('#js_checkout_store_address');
            this.__initStores();
        },

        __initStores: function () {
            var _this = this;
            this.storesWidget = new StoresWidget(this.$el.find('#checkout_stores'));
            $(this.storesWidget).on(':change', function (e, store) {
                //_this.$schedule.text(store.schedule);
                //_this.$storeAddress.val(store.$el.text());
                $(_this).trigger(':changeStore')
            });
        }

    });

    var NewPostDeliveryWidget = DeliveryWidget.extend({

        defaults: function () {
            return {
                cities: null,
                departments: null,
                $dep: null,
                $address: null,
                $schedule: null
            }
        },

        initialize: function () {
            NewPostDeliveryWidget.__super__.initialize.apply(this, arguments);
            this.loader = new App.LoaderWidget(this.$el.find('.radio-hide'));
            this.__initCity();
            this.__initDepartments();
            this.$dep = this.$el.find('.post-tit');
            this.$address = this.$el.find('.post-adr');
            this.$schedule = this.$el.find('.post-time');
        },

        __initCity: function () {
            var _this = this;
            this.cities = new NewPostDeliveryWidget.CitiesWidget(this.$el.find('#checkout_np_city'), {
                loader: this.loader
            });

            $(this.cities).on(':select', function (e, suggestion) {
                //console.log(':select');
                //console.log(suggestion);
                _this.departments.showForCity(suggestion.data);
            });
            $(this.cities).on(':empty', function (e) {
                _this.departments.$select.empty();
                _this.departments.hide();
                _this.$dep.empty();
                _this.$address.empty();
                _this.$schedule.empty();
            });
        },

        __initDepartments: function () {
            var _this = this;
            this.departments = new NewPostDeliveryWidget.DepartmentsWidget(this.$el.find('#checkout_np_departments'), {
                loader: this.loader
            });
            this.departments.hide();
            $(this.departments).on(':selectDepartment', function (e, department) {
                var dep, addr, schedule;
                [dep, addr] = department.NAME.split(':');
                schedule = department.UF_SCHEDULE || '';
                schedule += '<br />' + department.UF_PHONE;
                _this.$dep.html(dep);
                _this.$address.html(addr);
                _this.$schedule.html(schedule);
            });
        }

    }, {
        DepartmentsWidget: App.Widget.extend({
            defaults: function () {
                return {
                    $select: null,
                    $error: null,
                    loader: null,
                    departments: {}
                };
            },
            initialize: function () {
                this.__initSelect();
            },

            __initSelect: function () {
                var _this = this;
                this.$select = this.$el.find('select');
                this.$select.styler();
                this.$select.on('change', function () {
                    var $option = _this.$select.find('option:selected');
                    var department = _this.departments[$option.data('id')];
                    if (department) {
                        $(_this).trigger(':selectDepartment', [department]);
                    }
                });
                this.$error = this.$el.find('.err-text');
            },

            showForCity: function (cityRef) {
                if (!cityRef) {
                    return;
                }
                var _this = this;
                this.clearError();
                //this.loader.show();
                $.post(App.CheckoutWidget.ajaxHandler, {
                    func: 'getNewPostDepartmentsByCityRef',
                    cityRef: cityRef,
                    lang: App.CheckoutWidget.lang
                }, function (response) {
                    //_this.loader.hide();
                    if (response.status === 'success') {
                        _this.$select.empty();
                        var defaultDepartment = _this.$select.data('dep') || response.data.DEPARTMENTS[0]['NAME'];

                        response.data.DEPARTMENTS.map(function (departmentData) {

                            var $option = $('<option>', {
                                text: departmentData.NAME,
                                value: departmentData.NAME
                            });
                            if (departmentData.NAME === defaultDepartment) {
                                $option.attr('selected', 'selected');
                            }
                            $option.data('id', departmentData['ID']);
                            _this.$select.append($option);
                            _this.departments[departmentData['ID']] = departmentData;
                        });
                        _this.$select.trigger('change');
                        _this.$select.trigger('refresh');
                        _this.show();
                       // _this.loader.reset();
                    } else {
                        _this.showError(response.message);
                    }
                }, 'json');
            },

            hide: function () {
                this.$el.hide();
            },

            show: function () {
                this.$el.show();
            },

            clearError: function () {
                this.$el.removeClass('error-inp');
                this.$error.empty();
            },

            showError: function (message) {
                this.$el.addClass('error-inp');
                this.$error.html(message);
            }
        }),

        CitiesWidget: App.Widget.extend({
            defaults: function () {
                return {
                    $input: null,
                    $error: null,
                    loader: null
                }
            },

            initialize: function () {
                this.$error = this.$el.find('.err-text');
                this.__initInput();
            },

            __initInput: function () {
                var _this = this;
                this.$input = this.$el.find('input');

                _this.$input.autocomplete({
                    serviceUrl: CheckoutWidget.ajaxHandler,
                    type: 'POST',
                    params: {
                        lang: App.CheckoutWidget.lang,
                        func: 'getNewPostCities'
                    },
                    deferRequestBy: 350,
                    minChars: 2,
                    maxHeight: 350,
                    onSearchStart: function () {
                        //_this.loader.show();
                        _this.clearError();
                    },
                    transformResult: function (response) {
                        //_this.loader.hide();
                        response = JSON.parse(response);
                        var suggestions = [];
                        if (response.status == 'error') {
                            _this.showError(response.message);
                            return {suggestions: suggestions};
                        }
                        suggestions = response.data.map(function (cityData) {
                            return {value: cityData.UF_NAME_RU, data: cityData.UF_REF_ID};
                        });
                        if (!suggestions.length) {
                            _this.$input.val('');
                            $(_this).trigger(':empty');
                        }
                        return {suggestions: suggestions.slice(0, 10)};
                    },
                    onSelect: function (suggestion) {
                        $(_this).trigger(':select', [suggestion]);
                    }
                });
                var value = _this.$input.val();
                if (value) {
                    //console.log(this.$input);
                    _this.$input.focus();
                }
            },

            clearError: function () {
                this.$el.removeClass('error-inp');
                this.$error.empty();
            },

            showError: function (message) {
                this.$el.addClass('error-inp');
                this.$error.html(message);
            }
        })
    });

    var StoresWidget = App.Widget.extend({
        defaults: function () {
            return {
                stores: {}
            }
        },

        initialize: function () {
            var _this = this;
            this.$el.styler();
            this.$el.find('.a-checkout-store').each(function () {
                var store = new StoresWidget.Store($(this));
                _this.stores[store.id] = store;
            });
            this.$el.on('change', function () {
                var storeId = $(this).val();
                $(_this).trigger(':change', [_this.stores[storeId]]);
            });
        },

        isSelected: function () {
            return this.$el.is(':selected');
        }

    }, {
        Store: App.Widget.extend({
            defaults: function () {
                return {
                    id: 0,
                    schedule: ''
                }
            },
            initialize: function () {
                var options = arguments[1] || {};
                this.id = options.id || this.$el.attr('value');
                this.schedule = options.schedule || this.$el.data('schedule');
            },
            isSelected: function () {
                return this.$el.is(':selected');
            }
        })
    });

    var PropWidget = App.Widget.extend({
        defaults: function () {
            return {
                id: 0,
                code: '',
                $input: null
            }
        },

        initialize: function () {
            this.$input = this.$el.find('input');
        },

        getValue: function () {
            return this.$input.val();
        }

    }, {
        build: function ($el) {
            var params = {id: $el.data('id'), code: $el.data('code')};

            // console.log(params);
            if (!params.code) {
                throw new Error('PropWidget: Invalid property code');
            }
            var propWidget;
            if (params.code == 'PHONE') {
                propWidget = new PhonePropWidget($el, params);
            } else {
                propWidget = new PropWidget($el, params);
            }
            return propWidget;
        }
    });

    var PhonePropWidget = PropWidget.extend({

        initialize: function () {
            PhonePropWidget.__super__.initialize.apply(this, arguments);

            // App.phoneField(this.$input);
        }

    });

    var AuthFormWidget = App.Widget.extend({

        defaults: function () {
            return {
                $form: null,
                $emailInput: null,
                $passwordInput: null,
                $forgotLink: null,
                $forgotModal: null,
                $errors: null
            }
        },

        initialize: function () {
            this.__initForm();
            this.__initForgotLink();
            this.$emailInput = this.$form.find('input[name="USER_LOGIN"]');
            this.$passwordInput = this.$form.find('input[name="USER_PASSWORD"]');
            this.$errors = this.$form.find('#checkout_auth_error');
        },

        __initForm: function () {
            var _this = this;
            this.$form = this.$el.find('form');
            this.$form.on('submit', function (e) {
                e.preventDefault();
                _this.clearErrors();
                $(_this).trigger(':submit');
            });
        },

        __initForgotLink: function () {
            this.$forgotLink = this.$form.find('#checkout_auth_forgot');
            this.$forgotModal = $('#myModal');
            var _this = this;
            this.$forgotLink.on('click', function () {
                _this.$forgotModal.one('show.bs.modal', function () {
                    _this.$forgotModal.find('div.one-log-forg a').click();
                });
                _this.$forgotModal.modal();
            });
        },

        clearErrors: function () {
            this.$errors.empty().hide();
        },

        showErrors: function (err) {
            this.$errors.html(err).show();
        }

    });

})(window.App);
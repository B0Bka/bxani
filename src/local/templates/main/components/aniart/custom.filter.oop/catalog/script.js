(function ($, App) {

    var CustomFilter = App.CustomFilter = App.Widget.extend({

        defaults: function () {
            return {
                url: '/',
                sefController: null,
                filter: {},
                properties: [],
                countBlankDelay: 3000,
                showCountTimeout: false,
                hideCountBlankTimeout: false,
                lang: 'ru',
                catalogHeight: 0,
                current_action: 'append',
                open_filters: [],
                openFilterCount:0,
                is_filter_opened: 0,
                block: 0,
                filterElement: {},
                prop_value: '',
            }
        },

        initialize: function () {
            var options = arguments[1] || {};
            this.$countBlank = this.$el.find('#custom_filter_count');
            this.$countCount = this.$countBlank.find('.show-items-tit span');
            this.$active = this.$el.find('#custom_filter_active');
            this.$properties = options['$properties'] || this.$el.find('#custom_filter_properties');
            this.pricesFilter = document.getElementById('range-filter');
            this.cat_filter_left = options['cat_filter_left'] || $('.multi-sel');
            this.sefController = new SEFController(this.url);
            this.is_mobile = false;
            this.defaultFilterPrices = {};
            this.priceID = 27;

            this.selector = {
                custom_filter_properties: '#custom_filter_properties',
                filt_mobile: '.filt-mobile',
                filters_active: '#filters_active',
                custom_filter: '#custom_filter',
                multi_title: '.multi-tit',
                del_price_filter: '.del-price-filter',
                reset_price_filter: '#reset-price-filter',
                text_min_price_value: '#text-min-value',
                text_max_price_value: '#text-max-value',
                filter_overflow: '.custom-filter-overflow',
                range_set: '.range-set',
            };

            this.__initProperties();
            this.__initCountBlank();
            this.__events();

        },

        setDefaultFilterPrices: function () {
            this.defaultFilterPrices.min = parseFloat($('input[name="default-min-price"]').val());
            this.defaultFilterPrices.max = parseFloat($('input[name="default-max-price"]').val());
        },


        hasSelectedValues: function () {
            return !$.isEmptyObject(this.getSelectedPropertiesValues());
        },

        getSelectedPropertiesValues: function (ignorePropCode) {
            var props = this.getSelectedProperties(ignorePropCode);
            var values = {};
            if (props.count) {
                for (var propCode in props.properties) {
                    values[propCode] = props.properties[propCode].map(function (value) {
                        return value.id
                    });
                }
            }
            return values;
        },

        getSelectedProperties: function (ignorePropCode) {
            ignorePropCode = ignorePropCode || false;
            var result = {
                properties: {},
                count: 0
            };
            var props = this.getSelectedElements();
            props.forEach(function ($prop) {
                var propCode = $prop.data('propcode');
                if (!ignorePropCode && !propCode) {
                    return;
                }
                var propValue = $prop.val();
                var propValueCode = $prop.data('code');
                if (!result.properties[propCode]) {
                    result.properties[propCode] = [];
                }
                result.properties[propCode].push({
                    id: propValue,
                    code: propValueCode
                });
                result.count++;
            }, this);
            return result;
        },

        getSelectedElements: function () {
            var _this = this;
            var props = [];
            this.properties.forEach(function ($prop) {
                if (_this.isPropertySelected($prop)) {
                    props.push($prop);
                }
            }, this);
            return props;
        },

        isPropertySelected: function ($prop) {
            var selected = ($prop.get(0).hasAttribute('data-selected')) && $prop.data('selected') == 'selected';
            var changeDefault = ($prop.get(0).hasAttribute('data-default') && ($prop.data('default') != $prop.val()));
            return $prop.is(':selected') || $prop.is(':checked') || selected || changeDefault;
        },

        showCountBlank: function (obj) {
            if (CustomFilter.isMobile()) {
                return this.showCountBlankMobile();
            }
            var _this = this;
            var $obj = $(obj).parent();
            if (!$obj.length) {
                return;
            }

            var offset = $obj.offset();
            var left = offset.left;
            var top = offset.top;


            var x = (left + $obj.outerWidth() - $('.catalog').offset().left );
            var y = (top + $obj.outerHeight() / 2) - (this.$countBlank.outerHeight() / 2) - $('.catalog').offset().top;


            this.$countBlank.css({left: x + 'px', top: y + 'px'});
            this.$countCount.text('...');
            if (this.showCountTimeout) {
                clearTimeout(this.showCountTimeout);
            }
            if (this.hideCountBlankTimeout) {
                clearTimeout(this.hideCountBlankTimeout);
            }
            this.showCountTimeout = setTimeout(function () {
                _this.recalcCount(function (data) {
                    this.$countCount.text(data.cnt_text);
                    /*this.hideCountBlankTimeout = setTimeout(function(){
                        _this.hideCountBlank(3000);
                    }, this.countBlankDelay);*/
                });
            }, 500);
            this.$countBlank.show();
        },

        showCountBlankMobile: function () {
            var _this = this;
            this.$countCount.text('...');
            if (this.showCountTimeout) {
                clearTimeout(this.showCountTimeout);
            }
            this.showCountTimeout = setTimeout(function () {
                _this.recalcCount(function (data) {
                    this.$countCount.text(data.cnt_text);
                });
            }, 500);
        },

        hideCountBlank: function (timeout) {
            timeout = timeout || 0;
            this.$countBlank.fadeOut(timeout);
        },

        recalcCount: function (callback) {
            callback = callback || $.noop;
            var _this = this;
            var selectedPropsValues = this.getSelectedPropertiesValues(true);
            var params = {
                handler: 'customfilter',
                func: 'recalcCount',
                filter: $.extend({}, this.filter, {'PROPERTIES': selectedPropsValues}),
                lang: this.lang
            };
            $.post('/ajax/', params, function (response) {
                if (response.status == 'ok') {
                    callback.call(_this, response.data);
                }
            }, 'json');
        },

        onPriceFilterInput: function (values, callback) {
            var _this = this;
            var defaultMinPrice = $('input[name="default-min-price"]').attr('value');
            var defaultMaxPrice = $('input[name="default-max-price"]').attr('value');
            if( parseFloat(values.minPrice) < parseFloat(defaultMinPrice) ||
                parseFloat(values.minPrice) > parseFloat(defaultMaxPrice) ||
                isNaN(parseFloat(values.minPrice))
                ){
                $('#text-min-value').val(defaultMinPrice);
            } else {
                $('#text-min-value').val(parseFloat(values.minPrice));
            }

            if( parseFloat(values.maxPrice) > parseFloat(defaultMaxPrice) ||
                parseFloat(values.maxPrice) < parseFloat(defaultMinPrice) ||
                parseFloat(values.maxPrice) < parseFloat(values.minPrice) ||
                isNaN(parseFloat(values.maxPrice))
            ){
                $('#text-max-value').val(defaultMaxPrice);
            } else {
                $('#text-max-value').val(parseFloat(values.maxPrice));
            }

            if(typeof callback === "function"){callback()}
        },

        setCheckBox: function(selector){
            var _this = this, _app = App;
            var filterElement = _this.getFilterElement();
            $(selector).find('#' + $(filterElement).attr("id")).prop('checked') ?
                $(selector).find('#' + $(filterElement).attr("id")).prop('checked', false) :
                $(selector).find('#' + $(filterElement).attr("id")).prop('checked', true);
        },

        getFilterSource: function () {
            var _this = this, _app = App;
            var source = '';
            var filterElement = _this.getFilterElement();
            if($(_this.selector.custom_filter_properties).has( filterElement ).length > 0){
                source = 'leftFilter';
            } else if($(_this.selector.filt_mobile).has( filterElement ).length > 0){
                source = 'topFilter';
            } else if($(_this.selector.filters_active).has( filterElement ).length > 0){
                source = 'activeFilter';
            }
            return source;
        },

        setFilterElement: function(filterElement){
            var _this = this, _app = App;
            if(typeof filterElement !== 'undefined'){
                _this.filterElement = filterElement;
            }
        },

        getFilterElement: function () {
            var _this = this, _app = App;
            return _this.filterElement;
        },

        setFiltersCheckboxes: function () {
            var _this = this, _app = App;
            var filterSource = _this.getFilterSource();

            switch (filterSource){
                case 'leftFilter':
                    _this.setCheckBox(_this.selector.filt_mobile);
                    break;
                case 'topFilter':
                    _this.setCheckBox('#custom_filter_properties');
                    break;
                case 'activeFilter':
                    setTimeout(function () {
                        _this.setCheckBox('#custom_filter');
                    }, 100);
                    _this.setCheckBox(_this.selector.filt_mobile);
            }
        },

        setFilterCheckboxesVisual: function () {
            var _this = this, _app = App;
            _this.setFiltersCheckboxes();
        },

        arrangeTextMultiValue: function(e) {
            var _this = this, _app = App;
            $(e).find('.multi-tit')
                .text($(e)[0].defaultText.trim().split(' ')[0] + ' ' +
                    _this.prop_value + ' + ' +
                    ($(e)[0].checkedCount - 1)
                );
        },

        arrangeTextSingleValue: function(e) {
            var _this = this, _app = App;
        $(e).find('.multi-tit')
            .text($(e)[0].defaultText.trim().split(' ')[0] + ' ' +
                _this.prop_value);
        },

        arrangeTextZeroValue: function(e) {
            var _this = this, _app = App;
            $(e).find('.multi-tit').text($(e)[0].defaultText);
        },

        getFilterPropertyName: function (element) {
            var _this = this, _app = App;
            let prop_value = '';
            if($(element).find('input:checked').eq(0).siblings('span').text().length > 0){
                prop_value = $(element).find('input:checked').eq(0).siblings('span').text()
            } else {
                prop_value = $(element).find('input:checked').eq(0).siblings('span').data('original-title');
            }
            return prop_value;
        },

        setTopFiltersCaption: function () {
            var _this = this, _app = App;

            $(_this.selector.filt_mobile).find('.multi-sel').each(function (i,e) {
                $(e)[0].checkedCount = $(e).find('input:checked').length;
                if(typeof $(e)[0].defaultText === 'undefined'){
                        $(e)[0].defaultText = $(e).find('.multi-tit').text().trim().split(' ')[0];
                }
                _this.prop_value = _this.getFilterPropertyName(e);

                if($(e)[0].checkedCount > 1){
                    _this.arrangeTextMultiValue(e);
                }
                if($(e)[0].checkedCount == 1){
                    _this.arrangeTextSingleValue(e);
                }
                if($(e)[0].checkedCount == 0){
                    _this.arrangeTextZeroValue(e);
                }
                if($(e)[0].checkedCount > 0){
                    $(e).find('.multi-tit').addClass('selected_values');
                } else {
                    $(e).find('.multi-tit').removeClass('selected_values');
                }
            })
        },

        getResetFiltBtn: function () {
            var _this = this, _app = App;
            if(!$('#filters_active li').length > 0){
                $('body').find('#custom_filter .reset-filt').remove();
            } else {
                if($('body').find('#custom_filter .reset-filt').length == 0){
                    $('<div class="reset-filt">\n' +
                        '                <input id="custom_filter_reset" type="button" value="Сбросить фильтр" onclick="App.CatalogFilter.resetFilter(\'\')">\n' +
                        '            </div>').insertBefore(_this.selector.custom_filter_properties);
                }
            }
        },

        getEmptyActiveFiltersHTML: function () {
            var _this = this, _app = App;
            $('<div id="filters_active">\n' +
                '<ul>\n' +
                '</ul>\n' +
                '</div>').insertBefore(_this.selector.custom_filter_properties);
        },

        addActiveFilters: function (callback) {
            var _this = this, _app = App;
            if(!$('#custom_filter #filters_active').length > 0){
                _this.getEmptyActiveFiltersHTML();
            }
            if(typeof callback === 'function'){callback()}
        },

        addOneActiveFilter: function (oneFilterElement) {
            var _this = this, _app = App;

            oneFilterElement.find('span').text($(_this.filterElement).data('section_name') + ': ' + $(_this.filterElement).data('filter_name'));
            oneFilterElement
                .addClass('one-active-filt filter-delete')
                .appendTo($('body').find(_this.selector.filters_active + ' ul'))
        },

        removeOneActiveFilter: function () {
            var _this = this, _app = App;
            $('body').find(_this.selector.filters_active).find('#' + $(_this.filterElement).attr('id')).closest('li').remove();
        },

        setActiveFilterVisual: function () {
            var _this = this, _app = App;
            var filterElementClone = $(_this.filterElement).closest('li').clone();
            if($('body').find(_this.selector.filters_active).find('#' + $(_this.filterElement).attr('id')).length > 0){
                _this.removeOneActiveFilter();
            } else {
                _this.addOneActiveFilter(filterElementClone);
            }
        },

        setActiveFilterVisualPrice: function () {
            var _this = this, _app = App;
            if(!$('body').find(_this.selector.filters_active + ' .del-price-filter').length > 0){
                $('body').find(_this.selector.filters_active + ' ul').append("<li class = 'del-price-filter one-filt-element one-active-filt'></li>");
                $('#reset-price-filter')
                    .clone()
                    .text('Цена: ' + $('#text-min-value').val() + ' - ' + $('#text-max-value').val())
                    .appendTo($('body').find(_this.selector.filters_active + ' ul .del-price-filter'));
            } else {
                $('body').find('.del-price-filter').text('Цена: ' + $('#text-min-value').val() + ' - ' + $('#text-max-value').val());
            }
        },

        addMobileFilterBtnProperties: function () {
            var btn = $('body').find('.mobile-filter-caption');
            if(btn.length > 0){
                btn.each(function (i, e) {
                    $(e)[0].isFilterOpened = 0;
                });
            }
        },

        setCheckedFilters: function (filter) {
            var _this = this, _app = App;
            _this.addActiveFilters(function () {
                if($(_this.filterElement).attr('id') != 'submit-price-filter' && filter != 'priceFilt'){
                    _this.setActiveFilterVisual();
                } else {
                    _this.setActiveFilterVisualPrice()
                }

            })
            
        },

        setFiltersVisual: function (filterElement) {
            var _this = this, _app = App;
            // _this.setFilterElement(filterElement);
            _this.setFilterCheckboxesVisual();
            _this.setTopFiltersCaption();
            _this.setCheckedFilters();
        },

        setLocation: function(curLoc){
            try {
                history.pushState(null, null, curLoc);
                return;
            } catch(e) {}
            location.hash = '#' + curLoc;
        },

        setFiltersOverflow: function (state) {
            var _this = this, _app = App;
            if(state == 'start'){
                $('.catalog-wrap').append("<div class = 'custom-filter-overflow'></div>");
                // $('.catalog-wrap').addClass('blur');
            } else {
                if($('.catalog-wrap').find('.custom-filter-overflow').length > 0){
                    $('.custom-filter-overflow').remove();
                    // $('.catalog-wrap').removeClass('blur');
                }
            }
        },
        pushSelectedProps: function (array, data) {
            if(typeof array.properties[data.propcode] === 'undefined'){
                array.properties[data.propcode] = [];
                array.properties[data.propcode].push({
                    code: data.code,
                    name: data.name,
                    property_section_id: data.property_section_id,
                    value: data.value,
                });
            } else {
                array.properties[data.propcode].push({
                    code: data.code,
                    name: data.name,
                    property_section_id: data.property_section_id,
                    value: data.value,
                });
            }
            return array;
        },
        getSelectedValuesObj :function () {
            var _this = this, _app = App;
            var checked_values = {properties: {}, count: 0};
            var props = {};

            $('.one-filt-element input[type="checkbox"]').each(function () {
                if($(this).is(':checked')){

                    props.propcode = $(this).data('propcode');
                    props.code = $(this).data('code');
                    props.name = $(this).data('filter_name');
                    props.property_section_id = $(this).data('property_id');
                    props.value = $(this).attr('value');
                    checked_values = _this.pushSelectedProps(checked_values, props);
                }
            });
            if(_this.isPriceFiltered()){
                var min_price = parseFloat($('#text-min-value').val());
                var max_price = parseFloat($('#text-max-value').val());
                props.propcode = 'min_price';
                props.code = min_price + "-" + max_price;
                props.name = min_price + " - " + max_price;
                props.property_section_id = _this.priceID;
                props.value = {min: min_price, max: max_price,};
                checked_values.properties['min_price'] = [];
                checked_values.properties['min_price'].push({
                    code: props.code,
                    name: props.name,
                    property_section_id: props.property_section_id,
                    value: props.value,
                });
            } else {
                if(typeof checked_values.properties["min_price"] !== "undefined"){
                    checked_values.properties.splice('min_price', 1);
                }

            }
            for(key in checked_values.properties){
                checked_values.properties[key] = _this.removeDuplicates(checked_values.properties[key], 'code');
                checked_values.count += checked_values.properties[key].length;
            }

            return checked_values;
        },

        isPriceFiltered: function () {
            var _this = this, _app = App;
            var default_min_price = parseFloat($('input[name="default-min-price"]').val());
            var default_max_price = parseFloat($('input[name="default-max-price"]').val());
            var current_min_price = parseFloat($('#text-min-value').val());
            var current_max_price = parseFloat($('#text-max-value').val());
            if(current_min_price != default_min_price || current_max_price != default_max_price) {
                return true;
            }
        },

        getFilterUrl: function () {
            var _this = this, _app = App;
            var url = '';

            var objURL = _this.getSelectedValuesObj();
            for(key in objURL.properties){
                if(objURL.properties[key].length > 0){
                    url += key + "-";
                }
                objURL.properties[key].forEach(function (item, i, arr) {
                    url += item.code + "_";
                })
            }
            if(objURL.count > 0){
                url = this.url + url.slice(0, -1) + '/';
            } else {
                url = this.url;
            }
            return url;
        },

        submitFilter: function (filterElement) {
            var _this = this, _app = App;
            if(_this.is_mobile) return false;
            var selectedProps = this.getSelectedProperties();
            var additionalProps = {
                code:'',
                value:'',
            };
            _this.setFilterElement(filterElement);

            for (var propCode in selectedProps.properties) {
                var values = selectedProps.properties[propCode];
                this.sefController.clearParamValues(propCode);
                if (values.length && propCode) {
                    values.forEach(function (value) {
                        var code = value.code;
                        if (typeof(code) == "undefined") {
                            code = value.id;
                        }
                        this.sefController.addParam(propCode, code, 1);
                    }, this);
                }
            }
            var location = this.sefController.getSefUrl();
            // window.location.replace(location);

            additionalProps.code = $(filterElement).data('property_id');
            additionalProps.value = $(filterElement).attr('value');
            // _this.sendFiltersAjax({
            //     set_price: false,
            //     additional_params: additionalProps,
            // });
            _this.setFiltersOverflow('start');
            _app.post({
                url:_app.AJAX_DIR,
                data:{
                    handler:'catalog',
                    func:'loadPageWithFilters',
                    // page:page,
                    additional_params: additionalProps,
                    componentParams:CatalogProductsListMain.PARAMS
                }
            }, function(response){
                $('#catalog_products_list').html(response.data.html);
                _this.setDefaultPagination();
                _this.setFiltersVisual(filterElement); //добавляет кнопки удаления, выбирает соотв фильтры сверху или снизу
                _this.setFiltersOverflow('reset');
                _this.getDefaultView();
                _this.adjustLeftFilterHeight();
                _this.openFilterCount = response.data.activeFilterCount;
                var interval = setTimeout(function () {
                    _this.setLocation(_this.getFilterUrl());
                    CatalogProductsListMain.lazyLoadPhoto();
                }, 200);
            });

        },

        sendFiltersAjax: function (params) {
            var _this = this, _app = App;

            var set_price = params.params || false;
            var additional_params = params.additional_params || {};

            // _this.setFiltersOverflow('start');
            // _app.post({
            //     url:_app.AJAX_DIR,
            //     data:{
            //         handler:'catalog',
            //         func:'loadPageWithFilters',
            //         // set_price: set_price,
            //         additional_params: additional_params,
            //         componentParams:CatalogProductsListMain.PARAMS
            //     }
            // }, function(response){
            //     $('#catalog_products_list').html(response.data.html);
            //     _this.setDefaultPagination();
            //     // _this.setFiltersVisual(_this.filterElement); //добавляет кнопки удаления, выбирает соотв фильтры сверху или снизу
            //     _this.setFiltersOverflow('reset');
            //     _this.getDefaultView();
            //     _this.adjustLeftFilterHeight();
            //     _this.openFilterCount = response.data.activeFilterCount;
            // });
        },

        setDefaultPagination: function () {
            var _this = this, _app = App;
            App.Catalog.PAGINATION.NavPageNomer = 1;
            CatalogProductsListMain.topPage = 1;
            history.pushState({}, '', '?top=1');
            $('#myBtn').trigger('click');//scroll to top
            if($('body').find('#catalog_products_list .one-cat-item').length > 0)
            {$('#catalog_pagination_num').show()}
            else
            {$('#catalog_pagination_num').hide(); $('#catalog_products_list').text('По вашим фильтрам ничего не найдено')}
        },

        resetFilter:function (params) {
            if(params){
                window.location.href = '/catalog/?' + params;
            } else {
                window.location.href = '/catalog/'
            }
        },

        adjustLeftFilterHeight: function () {
            $('.wrapper-sticky').css('height', $('.sidebar').height());
        },

        getDefaultView: function () {
            var _this = this;
            _this.getResetFiltBtn();
        },

        setRangeSlider: function (callback) {
            this.pricesFilter = document.getElementById("range-filter");
            if(typeof callback === 'function'){callback()}
        },

        getMobile: function () {
            this.is_mobile = true;
        },

        getRangeSlider: function () {
            var _this = this;
            var slider = this.pricesFilter;
            var sl_min = parseFloat(slider.dataset.slider_min);
            var sl_max = parseFloat(slider.dataset.slider_max);

            noUiSlider.create(slider, {
                connect: true,
                start: [sl_min, sl_max],
                range: {
                    'min': sl_min,
                    'max': sl_max,
                },

            });

            var rangeValues = [
                document.getElementById('text-min-value'),
                document.getElementById('text-max-value')
            ];
            var inval = 0;

            slider.noUiSlider.on('set', function (values, handle) {
                inval = parseFloat(values[handle]);
                rangeValues[handle].innerHTML = inval;
                $(rangeValues[handle]).attr('value', inval);
                _this.setRangeCheckboxes(values, handle);
            });

            slider.noUiSlider.on('set', function () {
                $("#text-min-value").val($("#text-min-value").attr("value"));
                $("#text-max-value").val($("#text-max-value").attr("value"));
                setTimeout(function () {
                    var min = $('#text-min-value').attr("value");
                    var max = $('#text-max-value').attr("value");

                    if(_this.block != 1 ){
                        _this.filterPrices(min, max);
                    }
                    _this.setCheckedFilters('priceFilt');
                }, 200) //хавает новую установленую цену после задержки

            });

        },

        setRangeCheckboxes: function (values, handle) {
            var _this = this, _app = App;
            $('body').find('.range-set').each(function (i,e) {
                switch (handle){
                    //left handle
                    case 0:
                        if(values[0] < $(e).find('input').data('max') && $(e).find('input').data('min') < $('body').find('#text-max-value').val())
                        {
                            $(e).find('input').prop( "checked", true );
                        }
                        else
                        {
                            $(e).find('input').prop( "checked", false );
                        }
                        break;
                        //right handle
                    case 1:
                        if(values[1] > $(e).find('input').data('min') && $(e).find('input').data('max') > $('body').find('#text-min-value').val())
                        {
                            $(e).find('input').prop( "checked", true );
                        }
                        else
                        {
                            $(e).find('input').prop( "checked", false );
                        }
                        break;
                }
            });
        },

        setRangeByCheckbox: function (el) {
            var _this = this;
            var slider = _this.pricesFilter;
            var values = {};
            var val_from = '';
            var val_to = '';
            var min_enable_index = $('.range-set input').index($('.range-set input:checked').eq(0));
            var max_enable_index = $('.range-set input').index($('.range-set input:checked').eq(-1));
            var current_index = $('.range-set').index(el);
                if($('.range-set input:checked').length > 1){
                    val_from = $('.range-set input:checked').eq(0).data('min');
                    val_to = $('.range-set input:checked').eq(-1).data('max');
                } else {
                    val_from = $(el).find('input').data('min');
                    val_to = $(el).find('input').data('max');
                }

            slider.noUiSlider.set([val_from, val_to]);
        },

        filterPrices: function (min, max) {
            var _this = this, _app = App;
            if(_this.is_mobile) return false;
            var additionalProps = {code: '', value: {min: '', max: ''}};
            _this.block = 1;
            additionalProps.code = $('#text-min-value').data('property_id');
            additionalProps.value.min = min;
            additionalProps.value.max = max;

            // _this.sendFiltersAjax({
            //     set_price: true,
            //     additional_params: additionalProps,
            // });

            _this.setFiltersOverflow('start');
            _app.post({
                url:_app.AJAX_DIR,
                data:{
                    handler:'catalog',
                    func:'loadPageWithFilters',
                    // page:page,
                    additional_params: additionalProps,
                    set_price: true,
                    componentParams:CatalogProductsListMain.PARAMS
                }
            }, function(response){
                $('#catalog_products_list').html(response.data.html);
                // _this.setFiltersVisual(_this.filterElement); //добавляет кнопки удаления, выбирает соотв фильтры сверху или снизу
                _this.setDefaultPagination();
                _this.setFiltersOverflow('reset');
                _this.getDefaultView();
                _this.adjustLeftFilterHeight();
                _this.openFilterCount = response.data.activeFilterCount;
                _this.block = 0;
            });
        },

        removePriceFilterVisual: function(){
            var _this = this, _app = App;
            if(_this.openFilterCount > 1){
                if($('body').find('#custom_filter #reset-price-filter').length > 0){
                    $('body').find('#custom_filter #reset-price-filter').remove();
                }
                if($('body').find('#custom_filter .del-price-filter').length > 0){
                    $('body').find('#custom_filter .del-price-filter').remove();
                }
            } else {
                $(_this.selector.filters_active).remove();
                $('#custom_filter_reset').remove();
            }
        },

        array_unique: function (array) {
            return array.filter(function(el, index, arr) {
                return index == arr.indexOf(el);
            });
        },

        removeDuplicates: function(originalArray, prop) {
            var newArray = [];
            var lookupObject  = {};

            for(var i in originalArray) {
                lookupObject[originalArray[i][prop]] = originalArray[i];
            }

            for(i in lookupObject) {
                newArray.push(lookupObject[i]);
            }
            return newArray;
        },
        filterAutoClose :function (button) {
            var _this = this, _app = App;
            if(button.parent().attr("id") == "custom_filter"){
                $('.catalog-open .mobile-filter-content').slideUp('slow', function () {
                    if($('.catalog-open .mobile-filter-caption')[0].isFilterOpened % 2 != 0 )
                        $('.catalog-open .mobile-filter-caption')[0].isFilterOpened ++;
                });
                $('.catalog-open .mobile-filter-caption').removeClass('opened');
            } else {
                $('.filter-open .mobile-filter-content').slideUp('slow', function () {
                    if($('.filter-open .mobile-filter-caption')[0].isFilterOpened % 2 != 0 )
                        $('.filter-open .mobile-filter-caption')[0].isFilterOpened ++;
                });
                $('.filter-open .mobile-filter-caption').removeClass('opened');

            };
        },

        mobileFilterAnimation: function (button) {
            var _this = this, _app = App;
            _this.filterAutoClose(button);
            let toggleOptions = {
                duration: 1000,
                progress: function () {
                    // debugger;
                },
                start: function () {
                    if( button[0].isFilterOpened % 2 == 0) {
                        button.parent().find('.mobile-filter-caption').addClass('opened');
                    }
                },
                done: function () {
                    button.parent().find('.mobile-filter-content').toggleClass('opened');
                    if( button[0].isFilterOpened % 2 == 0){
                        $(this).parent().find('.mobile-filter-caption').removeClass('opened');
                    }
                },
            };
            button.parent().find('.mobile-filter-content').slideToggle(toggleOptions);
            button[0].isFilterOpened ++;
        },

        saveOpenFiltersCondition: function () {
            var _this = this;
            var open_filters = [];
            $("#custom_filter_properties li").each(function (i,e) {
                if($(e).hasClass('opened')){
                    var filterName = $(e).attr("class").split(' ');
                    open_filters.push(filterName[0] + "%" + _this.url);
                }
            });
            var res = _this.array_unique(open_filters);
            $.cookie('filtersCondition', window.btoa(res.join()), { expires: 30, path: _this.url });
        },

        onClickItemEvent: function (filter) {
            var _this = this;
            var filter_part;
            var interval;
            var extend_catalog = function () {
                interval = setInterval(function () {
                    $('.catalog-wrap').height($('.sidebar').height() + $('.sidebar').offset().top - $('header').height());
                }, 100)
            };
            $(filter).on('click', '.toggle-open-js', function () {
                // extend_catalog();

                    var toggleOptions = {
                        start: function () {
                            $(this).css('display', 'flex');
                        },
                        complete: function () {
                            filter_part = this;
                            if(!$(this).hasClass('filt-opened')){
                                $(this).addClass('filt-opened');

                            } else {
                                $(this).removeClass('filt-opened');
                                $(this).css('display', 'none');
                            }
                        },
                        progress: function (animation, progress, remainingMs) {
                            // debugger;
                            //250 - уменьшает расстояние между фильтром и футером
                            $('.wrapper-sticky').css('height', $('.sidebar').height() + $('.sidebar').offset().top - 250);
                        }
                };

                $(this).parent().find('ul').first().slideToggle(toggleOptions);
				$(this).parent().find('.size-set-filter').toggle('slow');
                });
            },

        changeSizeType: function(code, el) {
            var name = '';
            $.each( $('.size-prop'), function(i, span) {
                name = $(span).data('name-'+code);
                if($(name).length)
                    $(span).text(name);
            });

            $('.size-set-filter span').removeClass('selected');
            $(el).addClass('selected');
            CatalogProductsListMain.changeSizeType(code);
            $.cookie("sizeType", code, { path: '/' });
        },
        uncheckAll: function (el) {
            if(el.find('input[type="checkbox"]').length > 0){
                el.find('input[type="checkbox"]').prop('checked', false);
                try{el.find('.range-set').removeClass('checked')}catch(err){console.log(err)}
            }
        },
        delMobileFilterGroup: function (group, callback) {
            var _this = this, _app = App;
            $('body').find('.one_filter[data-filter_group_code="'+group+'"] input[type="checkbox"]').each(function (i,e) {
                if($(e).is(":checked")){
                    $(e).prop('checked', false);
                }
            });
            if(group == 'min_price'){
                _this.uncheckAll($('body').find('.one_filter[data-filter_group_code="min_price"]'));
                _this.pricesFilter.noUiSlider.set([_this.defaultFilterPrices.min, _this.defaultFilterPrices.max]);
            };
            $('body').find('.one_filter[data-filter_group_code="'+group+'"] .mob-selected-values-append').text('');
            if(typeof callback === 'function'){
                callback();
            }
        },
        setMobileFiltersVisual: function (selectedValues) {
            var _this = this, _app = App;
            var additional = 0;
            for(var prop in selectedValues.properties){
                var selector_filter_group_append = $('.one_filter[data-filter_group_code="'+prop+'"]')
                    .find('.mob-selected-values-append');
                selector_filter_group_append.text('');
                selectedValues.properties[prop].forEach(function (currentValue, index, array) {
                    if(index < 3){
                        selector_filter_group_append.append(currentValue.name + ", ");
                    } else {
                        additional = 1;
                    }

                });
                if(additional == 1){
                    selector_filter_group_append.append(" + " + (selectedValues.properties[prop].length - 3) + ", ");
                    additional = 0;
                }
                selector_filter_group_append.text(selector_filter_group_append.text().slice(0, -2));
                selector_filter_group_append.append(
                    "<i class='one-mob-filt-del'></i>"
                )
            }
            if('min_price' in selectedValues.properties){
                var inputMinValue = $('#text-min-value').val();
                var inputMaxValue = $('#text-max-value').val();
                _this.onPriceFilterInput(
                    {minPrice: inputMinValue, maxPrice: inputMaxValue},
                    function () {
                        _this.pricesFilter.noUiSlider.set([$('#text-min-value').val(), $('#text-max-value').val()]);
                    }
                );
            }
            if(selectedValues.count > 0){
                $('#reset-mobile-filter').show();
                $('.filter-open .mobile-filter-caption').addClass('hasActiveFilters');
            } else {
                $('#reset-mobile-filter').hide();
                $('.filter-open .mobile-filter-caption').removeClass('hasActiveFilters');
            }
        },
        applyAllFilters: function () {
            var _this = this, _app = App;
            var selectedValues = _this.getSelectedValuesObj();
            _this.setFiltersOverflow();
            _app.post({
                url:_app.AJAX_DIR,
                data:{
                    handler:'catalog',
                    func:'loadPageWithFiltersMobile',
                    selectedValues: selectedValues,
                    componentParams:CatalogProductsListMain.PARAMS
                }
            }, function(response){
                $('#catalog_products_list').html(response.data.html);
                _this.setDefaultPagination();
                _this.setMobileFiltersVisual(selectedValues);
                // _this.setFiltersVisual(filterElement); //добавляет кнопки удаления, выбирает соотв фильтры сверху или снизу
                _this.setFiltersOverflow('reset');
                _this.getDefaultView();
                _this.adjustLeftFilterHeight();
                _this.openFilterCount = response.data.activeFilterCount;
                var interval = setTimeout(function () {
                    _this.setLocation(_this.getFilterUrl());
                }, 200);
            });
        },

        __initProperties: function () {
            var _this = this;
            this.$properties.find('input,option').each(function () {
                _this.properties.push($(this));
            });
        },

        __initCountBlank: function () {
            this.$countBlank.find('.close-filt').on('click', $.proxy(this.hideCountBlank, this));
            this.$countBlank.find('.show-items-in a').on('click', $.proxy(this.submitFilter, this));
        },

        __events: function(){
            var _this = this;
            var _app = App;

            $(_this.selector.custom_filter_properties + ' > li, #custom_filter_properties .more-properties ul > li').on('click', function () {
                setTimeout(function () {
                    _this.saveOpenFiltersCondition();
                }, 500);

            });
            setTimeout(function () {
                _this.setRangeSlider(function () {
                    _this.getRangeSlider()
                });

            }, 1000); //ожидание пока не загрузится функция определения мобайл app()->getdevcetype();
            $('body').on('change', '.range-set', function () {
                _this.setRangeByCheckbox(this);
            });

            _this.adjustLeftFilterHeight();

            //set


            $('body').on('click', "#reset-price-filter", function () {
                var slider = _this.pricesFilter;
                var min = Math.round($('input[name="default-min-price"]').val());
                var max = Math.round($('input[name="default-max-price"]').val());
                if(_this.block != 1 ){
                    _this.filterPrices(min, max);
                }
                slider.noUiSlider.set([min, max]);
                if($('body').find('#custom_filter #reset-price-filter').length > 0){
                    $('body').find('#custom_filter #reset-price-filter').remove();
                }
            });
            $('body').on('click', '.del-price-filter', function () {
                var slider = _this.pricesFilter;
                var min = Math.round($('input[name="default-min-price"]').val());
                var max = Math.round($('input[name="default-max-price"]').val());
                if(_this.block != 1 ){
                    _this.filterPrices(min, max);
                }
                slider.noUiSlider.set([min, max]);

                setTimeout(function () {
                    _this.removePriceFilterVisual();
                }, 1000)


            });

            $(_this.selector.custom_filter_properties + ' .filt-opened').on('click', function () {
                // alert(123);
            });
            
            $('body').on('keypress', '.range-input-text-area input', function (event) {
                var slider = _this.pricesFilter;
                var inputMinValue = $('#text-min-value').val();
                var inputMaxValue = $('#text-max-value').val();
                if ( event.which == 13 ) {
                        _this.onPriceFilterInput(
                            {minPrice: inputMinValue, maxPrice: inputMaxValue},
                            function () {
                                slider.noUiSlider.set([$('#text-min-value').val(), $('#text-max-value').val()]);
                            }
                            );
                }
            });
            $('body').on('click', '#apply-filter-mobile', function () {
                if(_this.is_mobile){
                    _this.applyAllFilters();
                }
            });
            $('body').on('click', '.one-mob-filt-del', function (e) {
                e.stopPropagation();
                var filter_group = $(this).closest(".one_filter").data().filter_group_code;
                _this.delMobileFilterGroup(filter_group, function () {
                    _this.getSelectedValuesObj();
                });
            });
            $('body').on('click', '.mobile-filter-content .toggle-open-js', function () {
                var el = $(this);
                $(this).parent().find('ul').slideToggle('fast', function () {
                    if(el.closest('.one_filter').hasClass('openfix')){el.closest('.one_filter').removeClass('openfix')}
                    if(el.parent().find('.checkbox-prices').hasClass('filt-opened')){el.parent().find('.checkbox-prices').removeClass('filt-opened')}
                });
            });

            $('body').on('click', '.more-properties-toggle-mobile', function () {
                var toggle_button = $(this);
                var toggle_block = $('body').find('.mobile-filter-content .more-properties');
                toggle_block.slideToggle('fast', function () {
                    toggle_block.toggleClass('opened');
                    if(toggle_block.hasClass('opened')){
                        toggle_button.text("Скрыть дополнительные фильтры");
                    } else {
                        toggle_button.text("Дополнительные фильтры");
                    }
                })
            });
            $('body').on('click', '.mobile-filter-caption', function () {
                var button = $(this);
                _this.mobileFilterAnimation(button);
            });
            $('body').on('click', '.mob-filter-sort', function (e) {
                button.find('.mobile-catalog-sort').toggleClass('openfix');
            });
        }

    }, {
        isMobile: function () {
            return $('body').outerWidth() < 768;
        }
    });

    /**
     * Внедрение ЧПУ
     *
     * @param code
     * @param checked
     * @constructor
     */

    function SEFValue(code, checked) {
        this.code = code;
        this.checked = checked || 0;
        this.repetition = 1;
    }

    function SEFProperty(code, values) {
        values = values || {};
        this.code = code;
        this.setValues(values);
    }

    SEFProperty.prototype = {
        setValues: function (values) {
            values = values || [];
            if (typeof values == 'array') {
                this.values = {};
                values.forEach(function (value) {
                    this.addValue(value);
                }, this);
            }
            else {
                this.values = values;
            }
        },

        isValueExist: function (value) {
            return !!this.values[value];
        },

        addValue: function (value, checked) {
            if (value instanceof SEFValue) {
                if (!this.isValueExist(value.code)) {
                    this.values[value.code] = value;
                }
                else {
                    this.values[value.code].repetition++;
                }
            }
            else {
                checked = checked || 0;
                return this.addValue(new SEFValue(value, checked));
            }
            return value;
        },

        removeValue: function (value) {
            if (!value) {
                return;
            }
            if (this.isValueExist(value)) {
                delete this.values[value];
            }
        },

        getCheckedValues: function () {
            var result = [];
            for (var value in this.values) {
                if (this.values[value].checked == 1) {
                    result.push(this.values[value]);
                }
            }
            return result;
        },

        getValuesCodes: function (sort) {
            sort = !!sort;
            var result = [];
            var sortType = 'text';
            for (var value in this.values) {
                for (var i = 0; i < this.values[value].repetition; i++) {
                    result.push(value);
                }
                if ($.isNumeric(value)) {
                    sortType = 'number';
                }
            }
            if (sort) {
                if (sortType == 'number') {
                    result.sort(function (a, b) {
                        return a - b;
                    });
                }
                else {
                    result.sort();
                }
            }

            return result;
        },

        getValueByCode: function (code) {
            if (this.values[code]) {
                return this.values[code];
            }
            return false;
        },

        getValueCount: function (value) {
            if (this.isValueExist(value)) {
                return this.values[value];
            }
            return false;
        },

        sortValues: function () {
            var values = {};
            var valuesCodes = this.getValuesCodes(true);
            valuesCodes.forEach(function (value) {
                values[value] = this.values[value];
            }, this);
            this.setValues(values);
        },

        toUrl: function () {
            var url = '';
            var checkedCodes = this.getValuesCodes().filter(function (code) {
                var sefValue = this.getValueByCode(code);
                return (sefValue && sefValue.checked == 1);
            }, this);
            if (checkedCodes && checkedCodes.length > 0) {
                var url = this.code + '-';
                url += checkedCodes.join('_');
                url += '-';
            }
            return url;
        }

    };

    function SEFController(url) {
        this.url = url;
        this.use = false;
        this.params = {};
    }

    SEFController.prototype = {
        addParam: function (propCode, propValue, checked) {
            checked = checked || 0;
            if (!this.params[propCode]) {
                this.params[propCode] = new SEFProperty(propCode);
            }
            return this.params[propCode].addValue(propValue, checked);
        },

        removeParam: function (propCode, propValue) {
            propValue = propValue || '';
            var property = this.params[propCode];
            if (property) {
                if (propValue) {
                    property.removeValue(propValue);
                }
                if (!propValue || property.getValuesCount() == 0) {
                    delete this.params[propCode];
                }
            }
        },

        clearParamValues: function (propCode) {
            if (this.params[propCode]) {
                this.params[propCode].setValues({});
            }
        },

        isParamNew: function (propCode, propValue) {
            if (this.pageParams[propCode]) {
                if (this.pageParams[propCode][propValue]) {
                    return false;
                }
            }
            return true;
        },

        getCheckedParams: function () {
            var result = [];
            for (var propCode in this.params) {
                var checkedValues = this.params[propCode].getCheckedValues();
                if (checkedValues.length > 0) {
                    result.push({
                        code: propCode,
                        values: checkedValues
                    });
                }
            }

            return result;
        },

        getSefUrl: function () {
            var url = this.url;
            var checkedParams = this.getCheckedParams();
            if (
                checkedParams.length == 1 &&
                checkedParams[0].values.length == 1 &&
                $.inArray(checkedParams[0].code, ['some_property_code']) != -1
            ) {
                url += checkedParams[0].values[0].code;
            }
            else {
                this.sortParams();
                for (var propCode in this.params) {
                    url += this.params[propCode].toUrl();
                }
                url = url.substring(0, url.length - 1); //remove last "-"
            }
            if (url != this.url) {
                url += '/';
            }
            url += window.location.search;
            return url;
        },

        sortParams: function () {
            var propCodes = [];
            for (var propCode in this.params) {
                propCodes.push(propCode);
                this.params[propCode].sortValues();
            }
            propCodes.sort();
            var params = {};
            propCodes.forEach(function (propCode) {
                params[propCode] = this.params[propCode];
            }, this);
            this.params = params;
            params = null;
        }
    };

})(jQuery, App);

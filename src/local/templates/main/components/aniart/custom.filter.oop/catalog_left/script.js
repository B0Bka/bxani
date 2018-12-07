(function ($, App) {

    var CustomLeftFilter = App.CustomLeftFilter = App.Widget.extend({

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

            }
        },

        initialize: function () {
            var options = arguments[1] || {};
            this.$countBlank = this.$el.find('#custom_filter_count');
            this.$countCount = this.$countBlank.find('.show-items-tit span');
            //this.$active = this.$el.find('#custom_filter_active');
            this.$properties = options['$properties'] || this.$el.find('#cat-filter-left');
            //this.cat_filter_left = options['cat_filter_left'] || $('.multi-sel');
            this.sefController = new SEFController(this.url);

            this.__initProperties();
            this.__initCountBlank();


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

        getSelectedProperties: function (active_el) {
            //ignorePropCode = ignorePropCode || false;
            var result = {
                properties: {},
                count: 0
            };
            var props = this.getSelectedElements(active_el);

            props.forEach(function ($prop) {
                var propCode = $prop.data('propcode');

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

        getSelectedElements: function (prop) {
            var props = [];
            var children = prop.children();
            var _this = this;
            children.each(function(){
                if (_this.isPropertySelected($(this).find('input'))) {
                    props.push($(this).find('input'));
                }

            });
           /*prop.forEach(function ($prop) {
                if (_this.isPropertySelected($prop)) {
                    props.push($prop);
                }
            }, this);*/

            return props;
        },

        isPropertySelected: function ($prop) {
            var selected = ($prop.get(0).hasAttribute('data-selected')) && $prop.data('selected') == 'selected';
            var changeDefault = ($prop.get(0).hasAttribute('data-default') && ($prop.data('default') != $prop.val()));
            return $prop.is(':selected') || $prop.is(':checked') || selected || changeDefault;
        },

        showCountBlank: function (obj) {

            if (CustomLeftFilter.isMobile()) {
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
                handler: 'customleftfilter',
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

        submitFilter: function (el) {
            var selectedProps = this.getSelectedProperties(el);
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
            window.location.replace(location);
        },
        onClickItemEvent: function (filter) {
            var _this = this;
            $(filter).on('click', 'li', function () {

               _this.submitFilter($(this).parent());

            });
            return false;
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
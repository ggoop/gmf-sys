const isArray = (value) => {
    return value && value.constructor === Array;
};
const uniqueId = () => {
    return Math.random().toString(36).slice(4);
};
const debounce = (callback, limit) => {
    var wait = false;

    return () => {
        if (!wait) {
            callback.call();
            wait = true;

            window.setTimeout(() => {
                wait = false;
            }, limit);
        }
    };
};
const supplant = function(str, o) {
    return str.replace(/\{([^{}]*)\}/g, function(a, b) {
        var r = o[b];
        return typeof r === 'string' || typeof r === 'number' ? r : a;
    })
}
const isDefined = function(value) {
    return typeof value !== 'undefined';
}
const css = function(element, name, value) {
    if (isDefined(value)) {
        element.style[name] = value;
    } else {
        return element.style[name];
    }
}
const style = function(el, st) {
        _.forEach(st, function(value, key) {
            css(el, key, value);
        });
    }
    //驼峰命名法
const snakeCase = function(name, separator) {
    var regexp = /[A-Z.]/g;
    separator = separator || '-';
    name = name.replace(regexp, function(letter, pos) {
        return (pos ? separator : '') + letter.toLowerCase();
    });
    return name.replace(/\./g, '');
};

const formatDecimal = function(num, options) {
    //precision:精度，保留的小数位数
    //unit:单位，0,1个，2十，3百，4千
    //quantile:分位数，默认3，表示千分位
    options = Object.assign({}, { precision: 2, unit: 0, quantile: 3 }, options);

    num = parseFloat(num);
    if (options.unit) {
        num = num / Math.pow(10, options.unit);
    }
    var vv = Math.pow(10, options.precision);
    num = Math.round(num * vv) / vv;

    const groups = (/([\-\+]?)(\d*)(\.\d+)?/g).exec('' + num);
    // 获取符号(正/负数)
    const sign = groups[1];
    //整数部分
    const integers = (groups[2] || "").split("");
    // 求出小数位数值
    var cents = groups[3] || ".0";
    while (cents.length <= options.precision) {
        cents = cents + '0';
    }
    cents = options.precision ? cents.substring(0, options.precision + 1) : '';
    var temp = integers.join('');
    if (options.quantile>1) {
        var remain = integers.length % options.quantile;
        temp = integers.reduce(function(previousValue, currentValue, index) {
            if (index + 1 === remain || (index + 1 - remain) % options.quantile === 0) {
                return previousValue + currentValue + ",";
            } else {
                return previousValue + currentValue;
            }
        }, "").replace(/\,$/g, "");
    }

    const rtn=sign + temp + cents;
    if (options.quantile<=1){
        return parseFloat(rtn);
    }
    return rtn;
};
const common = {
    isArray,
    uniqueId,
    debounce,
    supplant,
    isDefined,
    css,
    style,
    snakeCase,
    formatDecimal,
};
export default common;

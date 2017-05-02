const isArray = (value) => {
    return value && value.constructor === Array;
};

function isString(val) {
    return typeof val === 'string';
}

function isNumber(val) {
    return typeof val === 'number';
}

function isUndefined(val) {
    return typeof val === 'undefined';
}

function isObject(val) {
    return val !== null && typeof val === 'object';
}

function isDate(val) {
    return toString.call(val) === '[object Date]';
}

function isFile(val) {
    return toString.call(val) === '[object File]';
}

function isBlob(val) {
    return toString.call(val) === '[object Blob]';
}

function isFunction(val) {
    return toString.call(val) === '[object Function]';
}

function isStream(val) {
    return isObject(val) && isFunction(val.pipe);
}

function trim(str) {
    return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

function bind(fn, thisArg) {
    return function wrap() {
        var args = new Array(arguments.length);
        for (var i = 0; i < args.length; i++) {
            args[i] = arguments[i];
        }
        return fn.apply(thisArg, args);
    };
};
function combineURLs(baseURL, relativeURL) {
    return relativeURL ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '') : baseURL;
};
/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};
function isAbsoluteURL(url) {
    // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
    // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
    // by any combination of letters, digits, plus, period, or hyphen.
    return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
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
    if (options.quantile > 1) {
        var remain = integers.length % options.quantile;
        temp = integers.reduce(function(previousValue, currentValue, index) {
            if (index + 1 === remain || (index + 1 - remain) % options.quantile === 0) {
                return previousValue + currentValue + ",";
            } else {
                return previousValue + currentValue;
            }
        }, "").replace(/\,$/g, "");
    }

    const rtn = sign + temp + cents;
    if (options.quantile <= 1) {
        return parseFloat(rtn);
    }
    return rtn;
};

function merge( /* obj1, obj2, obj3, ... */ ) {
    var result = {};

    function assignValue(val, key) {
        if (typeof result[key] === 'object' && typeof val === 'object') {
            result[key] = merge(result[key], val);
        } else {
            result[key] = val;
        }
    }

    for (var i = 0, l = arguments.length; i < l; i++) {
        forEach(arguments[i], assignValue);
    }
    return result;
}

function extend(a, b, thisArg) {
    forEach(b, function assignValue(val, key) {
        if (thisArg && typeof val === 'function') {
            a[key] = bind(val, thisArg);
        } else {
            a[key] = val;
        }
    });
    return a;
}

function forEach(obj, fn) {
    if (obj === null || typeof obj === 'undefined') {
        return;
    }
    if (typeof obj !== 'object' && !isArray(obj)) {
        obj = [obj];
    }

    if (isArray(obj)) {
        for (var i = 0, l = obj.length; i < l; i++) {
            fn.call(null, obj[i], i, obj);
        }
    } else {
        for (var key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                fn.call(null, obj[key], key, obj);
            }
        }
    }
}
const common = {
    isArray,
    uniqueId,
    debounce,
    supplant,
    isDefined,
    isAbsoluteURL,
    combineURLs,
    css,
    style,
    snakeCase,
    formatDecimal,
    merge: merge,
    forEach: forEach,
    isString,
    isNumber,
    isObject,
    isUndefined,
    isDate,
    isFile,
    isBlob,
    isFunction,
    isStream,
    extend,
    trim,
    spread
};
export default common;

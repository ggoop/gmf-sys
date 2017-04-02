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
const common = {
    isArray,
    uniqueId,
    debounce,
    supplant,
    isDefined,
    css,
    style,
    snakeCase,
};
export default common;

import Vue from 'vue';
import MdTip from './MdTip';
import { isObject } from 'gmf/core/utils/common';

const defaultOptions = {
    type: 'text',
    mask: false,
    message: '',
    value: true,
    duration: 3000,
    position: 'middle',
    loadingType: 'circular',
    forbidClick: false,
    overlayStyle: {}
};
const parseOptions = message => isObject(message) ? message : { message };

let queue = [];
let singleton = true;
let currentOptions = {...defaultOptions };

function createInstance() {
    if (!queue.length || !singleton) {
        const tip = new(Vue.extend(MdTip))({
            el: document.createElement('div')
        });
        document.body.appendChild(tip.$el);
        queue.push(tip);
    }
    return queue[queue.length - 1];
};

// transform tip options to popup props
function transformer(options) {
    options.overlay = options.mask;
    return options;
}

function Tip(options = {}) {
    const tip = createInstance();

    options = {
        ...currentOptions,
        ...parseOptions(options),
        clear() {
            tip.value = false;
        }
    };

    Object.assign(tip, transformer(options));
    clearTimeout(tip.timer);

    if (options.duration > 0) {
        tip.timer = setTimeout(() => {
            tip.clear();
        }, options.duration);
    }

    return tip;
};

const createMethod = type => options => Tip({
    type,
    ...parseOptions(options)
});
Tip.waiting = function(options) {
    options = {
        ...parseOptions(options),
        mask: true,
        type: 'loading'
    };
    Tip(options);
};
['loading', 'success', 'fail'].forEach(method => {
    Tip[method] = createMethod(method);
});

Tip.clear = all => {
    if (queue.length) {
        if (all) {
            queue.forEach(tip => {
                tip.clear();
            });
            queue = [];
        } else if (singleton) {
            queue[0].clear();
        } else {
            queue.shift().clear();
        }
    }
};

Tip.setDefaultOptions = options => {
    Object.assign(currentOptions, options);
};

Tip.resetDefaultOptions = () => {
    currentOptions = {...defaultOptions };
};

Tip.allowMultiple = (allow = true) => {
    singleton = !allow;
};

Tip.install = () => {
    Vue.use(MdTip);
};

Vue.prototype.$tip = Tip;

export default Tip;
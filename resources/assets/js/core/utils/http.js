import axios from 'axios';
import utils from './common';
import combineURLs from 'gmf/core/utils/MdCombineURLs';
const defaults = {
    headers: {
        common: {
            //'X-CSRF-TOKEN': '', //window.Laravel.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            //'Authorization': 'Bearer YXBpOnBhc3N3b3Jk',
        },
        post: {
            'Content-Type': 'application/json'
        }
    }
};

function Http(instanceConfig) {
    this.defaults = instanceConfig || {};
    this.Cancel = axios.Cancel;
    this.CancelToken = axios.CancelToken;
    this.isCancel = axios.isCancel;
    this.spread = axios.spread;
}
Http.prototype.request = function request(config) {
    if (typeof config === 'string') {
        config = utils.merge({ url: arguments[0] }, arguments[1]);
    }
    config = utils.merge({}, defaults, this.defaults, { method: 'get' }, config);
    if (utils.isAbsoluteURL(config.url) || /^\//g.test(config.url)) {
        config.baseURL = '';
    }
    return axios.request(config);
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
    /*eslint func-names:0*/
    Http.prototype[method] = function(url, config) {
        return this.request(utils.merge(config || {}, {
            method: method,
            url: url
        }));
    };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
    /*eslint func-names:0*/
    Http.prototype[method] = function(url, data, config) {
        return this.request(utils.merge(config || {}, {
            method: method,
            url: url,
            data: data
        }));
    };
});
Http.prototype.create = function(config) {
    return new Http(config);
};

let queue = {};
const defaultName = 'default';

function createGHTTPInstance(name, config) {
    name = name || defaultName;
    if (!queue[name]) {
        config = config || {};
        queue[name] = new Http(config);
    }
    return queue[name];
};

function GHTTP(name, config) {
    return createGHTTPInstance(name, config);
};
/**
 * {name,appId,entId,gateway,timestamp,apiList}
 * @param {object} config 
 */
GHTTP.config = function(config) {
    if (!config.name) {
        alert('[assert]: name is required');
    }
    if (!config.appId) {
        alert('[assert]: appId is required');
    }
    var chttp = createGHTTPInstance();
    var instance = createGHTTPInstance(config.name);

    return new Promise((resolved, rejected) => {
        chttp.post('sys/apps/config', config).then(res => {
            var d = res.data.data;
            instance.defaults.baseURL = d.host;
            instance.defaults.headers = { common: {} };
            if (d.ent) {
                instance.defaults.headers.common.Ent = utils.isObject(d.ent) ? d.ent.id : d.ent;
            }
            if (d.token) {
                instance.defaults.headers.common.Authorization = utils.isObject(d.token) ? (d.token.token_type ? d.token.token_type : "Bearer") + " " + d.token.access_token : d.token;
            }
            resolved(true);
        }, err => {
            rejected(false);
        });
    });
};
['delete', 'get', 'head', 'options'].forEach(method => {
    GHTTP[method] = function(url, config) {
        return createGHTTPInstance().request(utils.merge(config || {}, {
            method: method,
            url: url
        }));
    };
});
['post', 'put', 'patch'].forEach(method => {
    GHTTP[method] = function(url, data, config) {
        return createGHTTPInstance().request(utils.merge(config || {}, {
            method: method,
            url: url,
            data: data
        }));
    };
});
GHTTP.defaults = createGHTTPInstance().defaults;
export default GHTTP;
import Vue from 'vue';

import lodash from 'lodash';

import bootstrap from './bootstrap';
import router from './router';
import lang from './lang';
import validator from './validator';

import model from './core/mixin/model';

import http from './core/utils/http';

import common from './core/utils/common';

window._ = window._ || lodash;

window.Vue = window.Vue || Vue;
window.$modelMixin = model;
const start = {
    configs: []
};
start.run = function(elID) {
    elID = elID || '#gmfApp';
    var rootData = { title: '',userData:{} };

    baseConfig();

    //run configs
    for (var i = 0; i < this.configs.length; i++) {
        this.configs[i] && this.configs[i](Vue);
    }

    Vue.use(bootstrap);

    const app = new Vue({
        router: router,
        el: elID,
        data: rootData
    });
}
start.config = function(callback) {
    this.configs.push(callback);
}

function baseConfig() {
    http.defaults.baseURL = '/api';
    http.defaults.headers = { common: { Ent: window.gmfEntID } };
    Vue.prototype.$http = http;

    Vue.prototype._ = lodash;
    Vue.prototype.$toast = function(toast) {
        this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$validate = function(input, rules, customMessages) {
        return new validator(input, rules, customMessages);
    };
    Vue.prototype.$goModule = function(module, options, isReplace) {
        var localtion = { name: 'module', params: { module: module } };
        isReplace = !!isReplace;
        localtion = common.merge(localtion, options);
        this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
    };
    Vue.prototype.$goApp = function(app, options, isReplace) {
        var localtion = { name: 'app' };
        isReplace = !!isReplace;
        localtion = common.merge(localtion, options, { params: { app: app } });
        this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
    };
    Vue.prototype.$documentTitle = function(title) {
        document.title = title;
        this.$root.title = title;
    };
}
export default start;

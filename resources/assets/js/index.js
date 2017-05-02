import Vue from 'vue';

import lodash from 'lodash';

import bootstrap from './bootstrap';
import router from './router';
import lang from './lang';
import validator from './validator';

import model from './core/mixin/model';

import http from './core/utils/http';

window._ = window._ || lodash;

window.Vue = window.Vue || Vue;
window.$modelMixin = model;
const start = {};
start.run = function(elID) {
    elID = elID || '#gmfApp';
    baseConfig();

    Vue.use(bootstrap);
    const app = new Vue({
        router: router,
        el: elID
    });
}
start.config = function(callback) {
    if (callback && callback(Vue));
}

function baseConfig() {
    http.defaults.baseURL='/api';
    Vue.prototype.$http = http;

    Vue.prototype._ = lodash;
    Vue.prototype.$toast = function(toast) {
        this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$validate = function(input, rules, customMessages) {
        return new validator(input, rules, customMessages);
    };
    Vue.prototype.$goModule=function(module,options){
        this.$router&&this.$router.push({ name: 'module', params: { module: module }});
    };
    Vue.prototype.$goApp=function(app,options){
        this.$router&&this.$router.push({ name: 'app', params: { app: app }});
    };
}
export default start;

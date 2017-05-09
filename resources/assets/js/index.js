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
const start = {};
start.run = function(elID) {
    elID = elID || '#gmfApp';
    baseConfig();

    var rootData={title:''};

    Vue.use(bootstrap);
    const app = new Vue({
        router: router,
        el: elID,
        data:rootData
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
        var localtion={name: 'module', params: { module: module }};
        localtion=common.merge(localtion,options);
        this.$router&&this.$router.push(localtion);
    };
    Vue.prototype.$goApp=function(app,options){
        var localtion={name: 'app'};
        localtion=common.merge(localtion,options,{params: { app: app }});
        this.$router&&this.$router.push(localtion);
    };
    Vue.prototype.$documentTitle=function(title) {
      document.title=title;
      this.$root.title=title;
    };
}
export default start;

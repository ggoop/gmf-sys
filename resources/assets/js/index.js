import Vue from 'vue';
import axios from 'axios';
import lodash from 'lodash';

import bootstrap from './bootstrap';
import router from './router';
import lang from './lang';
import validator from './validator';

import model from './core/mixin/model';

window._ = window._ || lodash;
window.axios = window.axios || axios;
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
    Vue.prototype.$http = axios;
    Vue.prototype._ = lodash;
    Vue.prototype.$toast = function(toast) {
        this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$validate = function(input, rules, customMessages) {
        return new validator(input, rules, customMessages);
    };

    axios.defaults.headers.common = {
        'X-CSRF-TOKEN': '', //window.Laravel.csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    };
    axios.defaults.baseURL = '/api';
    axios.defaults.headers.common['Authorization'] = 'Basic YXBpOnBhc3N3b3Jk';;
    axios.defaults.headers.post['Content-Type'] = 'application/json';
}
export default start;

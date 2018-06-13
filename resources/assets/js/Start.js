import Vue from 'vue';
import Vuex from 'vuex';
import http from './core/utils/http';
import common from './core/utils/common';
import VueRouter from 'vue-router';
import extend from 'lodash/extend'
import merge from 'lodash/merge'
import isString from 'lodash/isString'
import lang from './utils/lang';
import gmfConfig from './config';
import enumCache from './core/utils/MdEnumCache';
import storeConfig from './store';
import Vuei18n from 'vue-i18n'
import combineURL from './core/utils/MdCombineURLs'

export default class Start {
    constructor(columnComponent) {}
    use(component) {
        Vue.use(component);
    }
    route(routes) {
        gmfConfig.route(routes);
    }
    config(fn) {
        gmfConfig.config(fn);
    }
    store(store) {
        gmfConfig.store(store);
    }
    i18n(locale, name, i18n) {
        gmfConfig.i18n(locale, name, i18n);
    }
    run(options, mixin) {
        options = options || {};
        const elID = options.elID || '#gmfApp';
        http.config({host:combineURL(options.host, '/api')});
        initVue(options);

        const appConfig = getAppConfig();
        /*routes*/
        initRoute(appConfig, options);

        /*store*/
        initStore(appConfig, options);

        //Vuei18n
        initI18n(appConfig, options);

        if (options.app) {
            appConfig.render = (mount) => mount(options.app);
        }
        if (mixin) {
            appConfig.mixins = [mixin];
        }
        document.addEventListener('DOMContentLoaded', () => {
            initConfigs().then(res => {
                extend(appConfig.data.configs, res);
                http.config(res);
                if (res && res.loadEnums) {
                    return loadEnums();
                }
                return true;
            }).then(res => {
                const app = new Vue(appConfig);
                appConfig.router.onReady(() => { app.$mount(elID); });
            });
        });
    }
}

function initConfigs() {
    return new Promise((resolved, rejected) => {
        return Promise.all([gmfConfig.configs.length > 0 ? gmfConfig.configs[0]() : false]).then(res => {
            resolved(res[0]);
        }, err => {
            rejected(err);
        });
    });
}

function initRoute(appConfig, options) {
    Vue.use(VueRouter);
    const router = {
        mode: 'history',
        routes: gmfConfig.routes,
        scrollBehavior(to, from, savedPosition) {
            if (savedPosition) {
                return savedPosition
            } else {
                if (from.meta.keepAlive) {
                    from.meta.savedPosition = document.body.scrollTop
                }
                return { x: 0, y: to.meta.savedPosition || 0 }
            }
        },
    };
    const vueRouter = new VueRouter(router);
    vueRouter.beforeEach((to, from, next) => {
        if (to.meta.requiresAuth && !appConfig.data.configs.user) {
            if (isString(appConfig.data.configs.auth.route)) {
                next(appConfig.data.configs.auth.route + "?continue=" + to.fullPath);
            } else {
                next(extend({}, appConfig.data.configs.auth.route, { query: { continue: to.fullPath } }));
            }
        } else {
            next();
        }
    });
    appConfig.router = vueRouter;
}

function initStore(appConfig, options) {
    Vue.use(Vuex);
    if (gmfConfig.stores && gmfConfig.stores.length > 0) {
        storeConfig.modules = {};
        gmfConfig.stores.forEach(item => {
            storeConfig.modules[item.name] = item;
        });
    }
    const store = new Vuex.Store(storeConfig);
    appConfig.store = store;
}

function initI18n(appConfig, options) {
    Vue.use(Vuei18n);
    const i18n = new Vuei18n({
        locale: options.locale || 'zh',
        messages: gmfConfig.i18ns.messages
    });
    appConfig.i18n = i18n;
}

function getAppConfig() {
    return {
        data: {
            'appName': '',
            'title': '',
            'configs': { home: '/', ent: false, user: false, token: false, auth: { route: { name: 'auth.login' } } }
        },
        methods: {
            $loadConfigs() {
                initConfigs().then(res => {
                    this.$setConfigs(res);
                })
            },
            changedConfig() {
                extend(window.gmfConfig, this.configs);
                this.$http.config(this.configs);
            },
            setCacheEnum(item) {
                enumCache.set(item);
            },
            getCacheEnum(type) {
                return enumCache.get(type);
            },
            getCacheEnumName(type, item) {
                return enumCache.getEnumName(type, item);
            },
            issueUid() {
                return new Promise((resolved, rejected) => {
                    this.$http.get('sys/datas/uid').then(res => {
                        resolved(res.data.data);
                    }, err => {
                        rejected(false);
                    });
                });
            },
            issueSn(node, num) {
                return new Promise((resolved, rejected) => {
                    this.$http.get('sys/datas/sn', { params: { node: node, num: num } }).then(res => {
                        resolved(res.data.data);
                    }, err => {
                        rejected(false);
                    });
                });
            }
        }
    };
}

function initVue(options) {
    options = options || {};

    Vue.prototype.$http = http;
    Vue.prototype._ = common;
    Vue.prototype.$devicePixelRatio = 1;
    Vue.prototype.$toast = function(toast) {
        this.$root.$refs.rootToast && this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$setConfigs = function(configs) {
        extend(this.$root.configs, configs);
        this.$root.changedConfig();
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$go = function(options, isReplace) {
        this.$router && this.$router[isReplace ? 'replace' : 'push'](options);
    };
    Vue.prototype.$goID = function(id, options, isReplace) {
        var localtion = { name: 'id', params: { id: id } };
        isReplace = !!isReplace;
        localtion = merge(localtion, options);
        this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
    };
    Vue.prototype.$goModule = function(module, options, isReplace) {
        var localtion = { name: 'module', params: { module: module } };
        isReplace = !!isReplace;
        localtion = merge(localtion, options);
        this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
    };
    Vue.prototype.$goApp = function(app, options, isReplace) {
        var localtion = { name: 'app' };
        isReplace = !!isReplace;
        localtion = merge(localtion, options, { params: { app: app } });
        this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
    };
    Vue.prototype.$hasRole = function(roles) {
        if (!roles || !this.$root.configs || !this.$root.configs.roles) return false;
        if (isString(roles)) {
            roles = roles.split(',');
        }
        return roles.map((v) => {
            return this.$root.configs.roles.indexOf(v) >= 0;
        }).filter(v => v).length > 0;
    };
    Vue.prototype.$canPermit = function(permits) {
        if (!permits || !this.$root.configs || !this.$root.configs.permits) return false;
        if (isString(roles)) {
            roles = roles.split(',');
        }
        return permits.map((v) => {
            return this.$root.configs.permits.indexOf(v) >= 0;
        }).filter(v => v).length > 0;
    };
    Vue.prototype.$documentTitle = function(title) {
        document.title = title;
        this.$root.title = title;
    };
}

function loadEnums() {
    return new Promise((resolved, rejected) => {
        http.get('sys/enums/all').then(res => {
            if (res && res.data && res.data.data) {
                res.data.data.forEach((item) => {
                    enumCache.set(item);
                });
            }
            resolved();
        }, err => {
            rejected();
        });
    });
}
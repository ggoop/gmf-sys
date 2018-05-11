import Vue from 'vue';
import Vuex from 'vuex';
import http from './core/utils/http';
import VueRouter from 'vue-router';
import common from './core/utils/common';
import lang from './lang';
// import lodash from 'lodash';
import gmfConfig from './config';
import enumCache from './core/utils/enumCache';
import storeConfig from './store';

export default class Start {
  constructor(columnComponent) {
    this.routes = [];
  }
  use(component) {
    Vue.use(component);
  }
  run(options, mixin) {
    options = options || {};
    const elID = options.elID || '#gmfApp';
    /*routes*/
    Vue.use(VueRouter);

    var routes = gmfConfig.routes;
    this.init();
    if (options.routes) {
      routes = routes.concat(options.routes);
    }
    const router = {
      mode: 'history',
      routes: routes,
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

    /*store*/
    Vue.use(Vuex);
    if (gmfConfig.stores && gmfConfig.stores.length > 0) {
      storeConfig.modules = {};
      gmfConfig.stores.forEach(item => {
        storeConfig.modules[item.name] = item;
      });
    }
    const store = new Vuex.Store(storeConfig);

    const rootData = {
      'appName': '',
      'title': '',
      'configs': { home: '/', ent: false, user: false, token: false, auth: { route: { name: 'auth.login' } } },
      'userConfig': {}
    };

    const vueRouter = new VueRouter(router);
    vueRouter.beforeEach((to, from, next) => {
      if (to.meta.requiresAuth && !rootData.configs.user) {
        next(rootData.configs.auth.route);
      } else {
        next();
      }
    });

    document.addEventListener('DOMContentLoaded', () => {
      Promise.all(common.values(options)).then(function(values) {

        if (window.gmfConfig) {
          common.extend(rootData.configs, window.gmfConfig);
        }

        const app = new Vue({
          router: vueRouter,
          data: rootData,
          store: store,
          mixins: [mixin],
          methods: {
            changedConfig() {
              common.extend(window.gmfConfig, this.configs);
              this.$http.defaults.headers.common.Ent = this.configs.ent ? this.configs.ent.id : false;
              if (this.configs.token) {
                this.$http.defaults.headers.common.Authorization = (this.configs.token.token_type?this.configs.token.token_type:"Bearer") + " " + this.configs.token.access_token;
              } else {
                this.$http.defaults.headers.common.Authorization = false;
              }
            },
            async loadEnums() {
              try {
                const response = await this.$http.get('sys/enums/all');
                if (response && response.data && response.data.data) {
                  response.data.data.forEach((item) => {
                    this.setCacheEnum(item);
                  });
                }
              } catch (error) {}
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
            async issueUid() {
              try {
                const response = await this.$http.get('sys/datas/uid');
                return response.data.data;
              } catch (error) {
                return false;
              }
              return false;
            },
            async issueSn(node, num) {
              try {
                const response = await this.$http.get('sys/datas/sn', { params: { node: node, num: num } });
                return response.data.data;
              } catch (error) {
                return false;
              }
              return false;
            },
            async $loadConfigs() {
              if (this.loadConfigs) {
                var res = await this.loadConfigs();
                if (res && res.data) {
                  res = res.data;
                }
                if (res && res.data) {
                  res = res.data;
                }
                if (res) {
                  this.$setConfigs(res);
                }
              }
            }
          },
          async created() {
            if (this.beforeCreated) {
              await this.beforeCreated();
            }
            await this.$loadConfigs();
            if (this.configs.loadEnum) {
              await this.loadEnums();
            }
          },
          async mounted() {
            if (this.beforeMounted) {
              await this.beforeMounted();
            }
          }
        });
        vueRouter.onReady(() => {
          app.$mount(elID);
        });
      });
    });
  }
  init() {
    http.defaults.baseURL = '/api';
    http.defaults.headers = { common: { Ent: false } };
    Vue.prototype.$http = http;

    Vue.prototype._ = common;
    Vue.prototype.$toast = function(toast) {
      this.$root.$refs.rootToast && this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$setConfigs = function(configs) {
      common.extend(this.$root.configs, configs);
      this.$root.changedConfig();
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$go = function(options, isReplace) {
      this.$router && this.$router[isReplace ? 'replace' : 'push'](options);
    };
    Vue.prototype.$goID = function(id, options, isReplace) {
      var localtion = { name: 'id', params: { id: id } };
      isReplace = !!isReplace;
      localtion = common.merge(localtion, options);
      this.$router && this.$router[isReplace ? 'replace' : 'push'](localtion);
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
    Vue.prototype.$hasRole = function(roles) {
      if (!roles || !this.$root.configs || !this.$root.configs.roles) return false;
      if (common.isString(roles)) {
        roles = roles.split(',');
      }
      return roles.map((v) => {
        return this.$root.configs.roles.indexOf(v) >= 0;
      }).filter(v => v).length > 0;
    };
    Vue.prototype.$canPermit = function(permits) {
      if (!permits || !this.$root.configs || !this.$root.configs.permits) return false;
      if (common.isString(roles)) {
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
}

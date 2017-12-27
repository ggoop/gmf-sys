import Vue from 'vue';
import Vuex from 'vuex';
import http from './core/utils/http';
import VueRouter from 'vue-router';
import validator from './validator';
import common from './core/utils/common';
import lang from './lang';
import lodash from 'lodash';
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
    if (!!options.defaultRoutes) {
      routes = routes.concat(gmfConfig.defaultRoutes);
    }
    const router = { mode: 'history', routes: routes };

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
      'appName':'',
      'title': '',
      'configs': { ent: false, user: false, token: false },
      'userConfig': {}
    };
    if (window.gmfConfig) {
      rootData.configs.ent = window.gmfConfig.ent;
      rootData.configs.user = window.gmfConfig.user;
      rootData.configs.token = window.gmfConfig.token;
    }

    const app = new Vue({
      router: new VueRouter(router),
      el: elID,
      data: rootData,
      store: store,
      mixins: [mixin],
      watch: {
        "userConfig.ent": function(v, o) {
          this.changedConfig();
        },
        "userConfig.user": function(v, o) {
          this.changedConfig();
        },
        "userConfig.token": function(v, o) {
          this.changedConfig();
        }
      },
      methods: {
        changedConfig() {
          if (this.userConfig && this.userConfig.ent) this.configs.ent = this.userConfig.ent;
          if (this.userConfig && this.userConfig.user) this.configs.user = this.userConfig.user;
          if (this.userConfig && this.userConfig.token) this.configs.token = this.userConfig.token;

          this.$http.defaults.headers.common.Ent = this.configs.ent ? this.configs.ent.id : false;
          if (this.configs.token) {
            this.$http.defaults.headers.common.Authorization = this.configs.token.token_type + " " + this.configs.token.access_token;
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
        async issueUid(node, num) {
          try {
            const response = await this.$http.get('sys/uid', { params: { node: node, num: num } });
            return response.data.data;
          } catch (error) {
            return false;
          }
          return false;
        },
      },
      async created() {
        if (this.beforeCreated) {
          await this.beforeCreated();
        }
        await this.loadEnums();
      },
      async mounted() {
        if (this.beforeMounted) {
          await this.beforeMounted();
        }
      }
    });
  }
  init() {
    http.defaults.baseURL = '/api';
    http.defaults.headers = { common: { Ent: false } };
    Vue.prototype.$http = http;

    Vue.prototype._ = lodash;
    Vue.prototype.$toast = function(toast) {
      this.$root.$refs.rootToast && this.$root.$refs.rootToast.toast(toast);
    }
    Vue.prototype.$setConfigs = function(configs) {
      //至少需要{ent:{},user:{},token:{}}字段
      this.$root.userConfig = configs;
      this.$root.changedConfig();
    }
    Vue.prototype.$lang = lang;
    Vue.prototype.$validate = function(input, rules, customMessages) {
      return new validator(input, rules, customMessages);
    };
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
    Vue.prototype.$documentTitle = function(title) {
      document.title = title;
      this.$root.title = title;
    };
  }
}

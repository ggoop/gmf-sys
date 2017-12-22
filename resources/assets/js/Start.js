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
  run(options) {
    options = options || {};
    const elID = options.elID || '#gmfApp';
    var rootData = {
      title: '',
      $rootData: { ent:false, user: false, token: false,configUrl:options.configUrl||'sys/auth'},
      userConfig: {}
    };
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

    const app = new Vue({
      router: new VueRouter(router),
      el: elID,
      data: rootData,
      store: store,
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
          this.$rootData.ent=this.userConfig.ent;
          this.$rootData.user=this.userConfig.user;
          this.$rootData.token=this.userConfig.token;

          this.$http.defaults.headers.common.Ent = this.$rootData.ent?this.$rootData.ent.id;
          if(this.$rootData.token){
            this.$http.defaults.headers.common.Authorization=this.$rootData.token.token_type+" "+this.$rootData.token.access_token;
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
        async loadConfigs() {
          //接口返回，至少需要返回{ent:{},user:{},token:{}}字段
          try {
            if(!this.$rootData.configUrl)return;
            const response = await this.$http.get(this.$rootData.configUrl);
            if(response.data&&response.data.data){
              this.userConfig=response.data.data;

              this.changedConfig();
            }
          } catch (error) {
            return false;
          }
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
      async created: function() {
        await this.loadConfigs();
        await this.loadEnts();
        await this.loadEnums();
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

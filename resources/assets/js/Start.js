import Vue from 'vue';
import http from './core/utils/http';
import VueRouter from 'vue-router';
import validator from './validator';
import common from './core/utils/common';
import lang from './lang';
import lodash from 'lodash';
import sysConfig from './config';
import enumCache from './core/utils/enumCache';
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
      userData: { ent: {}, entId: this.getEntId(), ents: [] },
      userConfig: window.gmfConfig
    };
    var routes=sysConfig.routes;
    this.init();
    if(options.routes){
      routes=routes.concat(options.routes);
    }
    if(sysConfig.defaultRoutes){
      routes=routes.concat(sysConfig.defaultRoutes);
    }
    const router = { mode: 'history', routes: routes };
    Vue.use(VueRouter);
    const app = new Vue({
      router: new VueRouter(router),
      el: elID,
      data: rootData,
      watch: {
        "userData.ent": function(v, o) {
          this.userData.entId = v ? v.id : "";
        },
        "userData.entId": function(v, o) {
          this.setHttpConfig();
          window.gmfEntID = v;
        },
        "userConfig": function(v, o) {
          window.gmfConfig = v;
        }
      },
      computed: {

      },
      methods: {
        setHttpConfig() {
          this.$http.defaults.headers.common.Ent = this.userData.entId;
        },
        loadEnums() {
          this.$http.get('sys/enums/all').then(response => {
            if (response.data && response.data.data) {
              response.data.data.forEach((item) => {
                this.setCacheEnum(item);
              });
            }
          }, response => {
            console.log(response);
          });
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
        loadEnts() {
          this.$http.get('sys/ents/my').then(response => {
            this.userData.ents = response.data.data || [];
            var ent = false;
            _.forEach(this.userData.ents, (value, key) => {
              if (value.id == this.userData.entId) {
                ent = value;
              }
            });
            if (!ent) {
              _.forEach(this.userData.ents, function(value, key) {
                if (value.is_default) {
                  ent = value;
                }
              });
            }
            if (!ent && this.userData.ents && this.userData.ents.length > 0) {
              ent = this.userData.ents[0];
            }
            this.userData.ent = ent;
          }, response => {
            console.log(response);
          });
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
      created: function() {
        this.loadEnts();
        this.loadEnums();
      }
    });
  }


  getEntId() {
    return window.gmfEntID;
  }
  init() {

    http.defaults.baseURL = '/api';
    http.defaults.headers = { common: { Ent: this.getEntId() } };
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

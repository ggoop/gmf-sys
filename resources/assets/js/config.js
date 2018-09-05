import Vue from 'vue';
import snakeCase from 'gmf/core/utils/MdSnakeCase';
import isArray from 'lodash/isArray'
import startsWith from 'lodash/startsWith';
import merge from 'lodash/merge';
const wrapApp = {
  template: '<md-wrap :name="wrap"></md-wrap>',
  computed: {
    wrap: function() {
      return this.$route.params.app;
    }
  },
  beforeRouteEnter(to, from, next) {
    next();
  },
  beforeRouteUpdate(to, from, next) {
    next();
  },
  beforeRouteLeave(to, from, next) {
    next();
  }
};
const wrapModule = {
  template: '<md-wrap :name="wrap"></md-wrap>',
  computed: {
    wrap: function() {
      const app = snakeCase(this.$route.params.app);
      const module = snakeCase(this.$route.params.module);
      if (!startsWith(module, app) && module.indexOf('-') < 0) {
        return app + '-' + module;
      }
      return module;
    }
  },
  beforeRouteEnter(to, from, next) {
    next();
  },
  beforeRouteUpdate(to, from, next) {
    next();
  },
  beforeRouteLeave(to, from, next) {
    next();
  }
};
const wrapExtend = {
  template: '<md-wrap :name="wrap"></md-wrap>',
  computed: {
    wrap: function() {
      const app = snakeCase(this.$route.params.app);
      const module = snakeCase(this.$route.params.module);
      if (!startsWith(module, app) && module.indexOf('-') < 0) {
        return app + '-' + module;
      }
      return module;
    }
  },
  beforeRouteEnter(to, from, next) {
    next();
  },
  beforeRouteUpdate(to, from, next) {
    next();
  },
  beforeRouteLeave(to, from, next) {
    next();
  }
};
const defaultRoutes = [{
  path: '/:app',
  component: wrapApp,
  name: 'app',
  children: [{
    path: ':module',
    name: 'module',
    component: wrapModule,
    children: [
      { path: ':id', name: 'id', component: wrapExtend },
      { path: '*', component: wrapExtend }
    ]
  }]
}];

class gmfConfig {
  constructor() {
    this.routes = [];
    this.stores=[];
    this.configs=[];
    this.beforeEachs=[];
    this.i18ns={messages:{}};
    this.defaultRoutes = defaultRoutes;
  }
  route(routes) {
    if (isArray(routes)) {
      routes.forEach((item) => {
        this.routes.push(item);
      });
    } else {
      this.routes.push(routes);
    }
  }
  config(fn){
    this.configs.push(fn);
  }
  //i18n('en','home',{title:'title',name:'name of name'})
  i18n(locale,name,i18n){
    if(name){
      this.i18ns.messages[locale][name]=merge(this.i18ns.messages[locale][name],i18n);
    }else{
      this.i18ns.messages[locale]=merge(this.i18ns.messages[locale],i18n);
    }    
  }
  store(store){
    if(!store.name)return;
    this.stores.push(store);
  }
  beforeEach(fn){
    this.beforeEachs.push(fn);
  }
}

if (!Vue.gmfConfig) {
  Vue.gmfConfig = new gmfConfig()
  Vue.prototype.$gmfConfig = Vue.gmfConfig;
}
const config = Vue.gmfConfig;
export default config;

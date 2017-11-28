import common from 'gmf/core/utils/common';


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
      const app = common.snakeCase(this.$route.params.app);
      const module = common.snakeCase(this.$route.params.module);
      if(!this._.startsWith(module,app)&&module.indexOf('-')<0){
        return app+'-'+module;
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
      const app = common.snakeCase(this.$route.params.app);
      const module = common.snakeCase(this.$route.params.module);
      if(!this._.startsWith(module,app)&&module.indexOf('-')<0){
        return app+'-'+module;
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
const routes = [{
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
export default routes;

import VueRouter from 'vue-router';
import Vue from 'vue';
Vue.use(VueRouter);

const wrapApp = {
    template: '<md-wrap :name="$route.params.app"></md-wrap>',
    beforeRouteEnter (to, from, next) {
        next();
    },
    beforeRouteUpdate (to, from, next) {
        next();
    },
    beforeRouteLeave (to, from, next) {
        next();
    }
};
const wrapModule = {
    template: '<md-wrap :name="$route.params.module"></md-wrap>',
    beforeRouteEnter (to, from, next) {
        next();
    },
    beforeRouteUpdate (to, from, next) {
        next();
    },
    beforeRouteLeave (to, from, next) {
        next();
    }
};
const wrapExtend = {
    template: '<md-wrap :name="$route.params.module"></md-wrap>',
    beforeRouteEnter (to, from, next) {
        next();
    },
    beforeRouteUpdate (to, from, next) {
        next();
    },
    beforeRouteLeave (to, from, next) {
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
const router = new VueRouter({ mode: 'history', routes });
router.beforeEach((to, from, next) => {
    next();
});
router.afterEach(route => {
    //console.log('afterEachafterEach');
});
export default router;

import VueRouter from 'vue-router';
import Vue from 'vue';
Vue.use(VueRouter);

const wrapApp = {
    template: '<md-wrap :name="$route.params.app"></md-wrap>'
};
const wrapModule = {
    template: '<md-wrap :name="$route.params.module"></md-wrap>'
};
const wrapExtend = {
    template: '<md-wrap :name="$route.params.module"></md-wrap>'
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
    var aaaa = to;
    //console.log('beforeEachbeforeEach', to, from);
    next();
});
router.afterEach(route => {
    //console.log('afterEachafterEach');
});
export default router;

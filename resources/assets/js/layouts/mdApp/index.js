import mdApp from './mdApp.vue';
import mdAppContent from './mdAppContent.vue';
import mdAppFooter from './mdAppFooter.vue';
import mdAppMenu from './mdAppMenu.vue';
import mdAppToolbar from './mdAppToolbar.vue';



export default function install(Vue) {
    Vue.component('mdApp', mdApp);
    Vue.component('mdAppContent', mdAppContent);
    Vue.component('mdAppFooter', mdAppFooter);
    Vue.component('mdAppMenu', mdAppMenu);
    Vue.component('mdAppToolbar', mdAppToolbar);
}

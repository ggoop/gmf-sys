import mdPart from './mdPart.vue';
import mdPartBody from './mdPartBody.vue';
import mdPartToolbar from './mdPartToolbar.vue';
import mdPartToolbarCrumb from './mdPartToolbarCrumb.vue';
import mdPartToolbarGroup from './mdPartToolbarGroup.vue';

export default function install(Vue) {
    Vue.component('mdPart', mdPart);
    Vue.component('mdPartBody', mdPartBody);
    Vue.component('mdPartToolbar', mdPartToolbar);
    Vue.component('mdPartToolbarCrumb', mdPartToolbarCrumb);
    Vue.component('mdPartToolbarGroup', mdPartToolbarGroup);
}

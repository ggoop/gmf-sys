import mdPart from './MdPart.vue';
import mdPartBody from './MdPartBody.vue';
import mdPartBodySide from './MdPartBodySide.vue';


import mdPartToolbar from './MdPartToolbar.vue';
import mdPartToolbarCrumbs from './MdPartToolbarCrumbs.vue';
import mdPartToolbarCrumb from './MdPartToolbarCrumb.vue';
import mdPartToolbarGroup from './MdPartToolbarGroup.vue';
import mdPartToolbarPager from './MdPartToolbarPager.vue';

export default function install(Vue) {
    Vue.component('mdPart', mdPart);
    Vue.component('mdPartBody', mdPartBody);
    Vue.component('mdPartBodySide', mdPartBodySide);
    Vue.component('mdPartToolbar', mdPartToolbar);
    Vue.component('mdPartToolbarCrumbs', mdPartToolbarCrumbs);
    Vue.component('mdPartToolbarCrumb', mdPartToolbarCrumb);
    Vue.component('mdPartToolbarGroup', mdPartToolbarGroup);
    Vue.component('mdPartToolbarPager', mdPartToolbarPager);
}

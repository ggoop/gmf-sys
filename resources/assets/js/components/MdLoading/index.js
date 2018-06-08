import mdLoading from './MdLoading.vue';
import MdLoading2 from './MdLoading2.vue';
export default function install(Vue) {
    Vue.component(mdLoading.name, mdLoading);
    Vue.component(MdLoading2.name, MdLoading2);
}
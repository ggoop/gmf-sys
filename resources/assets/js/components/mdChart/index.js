// import MdChart from './MdChart.vue'
export default function install(Vue) {
    // Vue.component(MdChart.name, MdChart);
    Vue.component('MdChart', () => import('./MdChart.vue'));
}

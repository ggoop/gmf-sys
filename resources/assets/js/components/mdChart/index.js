
export default function install(Vue) {
  Vue.component('MdChart', () =>    import ('./MdChart.vue'));
  Vue.component('MdChartJs', () =>    import ('./MdChartJs.vue'));
}

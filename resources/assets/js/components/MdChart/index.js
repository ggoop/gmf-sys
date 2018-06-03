import material from 'gmf/material'
import MdChart from './MdChart'
import MdChartJs from './MdChartJs'

export default Vue => {
  material(Vue)
  Vue.component(MdChart.name, MdChart)
  Vue.component(MdChartJs.name, MdChartJs)
}


// export default function install(Vue) {
//   Vue.component('MdChart', () =>    import ('./MdChart.vue'));
//   Vue.component('MdChartJs', () =>    import ('./MdChartJs.vue'));
// }

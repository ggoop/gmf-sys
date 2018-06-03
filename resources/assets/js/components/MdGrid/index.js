import MdGrid from './MdGrid.vue'
import MdGridColumn from './MdGridColumn.vue'

export default function install(Vue) {
  Vue.component('MdGrid', MdGrid);
  Vue.component('MdGridColumn', MdGridColumn);
}
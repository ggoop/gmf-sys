import MdGrid from './MdGrid.vue'
import MdGridColumn from './MdGridColumn.vue'
import MdPagination from './MdPagination.vue'

export default function install(Vue) {
  Vue.component('MdGrid', MdGrid);
  Vue.component('MdGridColumn', MdGridColumn);
  Vue.component('MdPagination', MdPagination);
}

import material from 'gmf/material'
import MdLayout from './MdLayout'
import MdLayoutGrid from './MdLayoutGrid'
import MdLayoutGridItem from './MdLayoutGridItem'
export default Vue => {
  material(Vue)
  Vue.component(MdLayout.name, MdLayout)
  Vue.component(MdLayoutGrid.name, MdLayoutGrid)
  Vue.component(MdLayoutGridItem.name, MdLayoutGridItem)
}
